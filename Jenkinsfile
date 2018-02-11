/*
 * 《ug钱包服务端》持续集成配置
 *
 * yii2-app-advanced
 * Application  [common console   backend api]
 * Environments [Development Testing Production]
 */

pipeline {
    agent any

    environment {
        //项目名称
        PROJECT_NAME='ug-php-wallet'

        //版本保留数
        KEEP_VERSION_NUM='10'

        //当前版本号
        VERSION=getVersion()

        //发布模式（兼容回滚）
        RELEASE_ENV=getReleaseEnv(VERSION, env.BRANCH_NAME)
        //目标机器-发布版本库
        RELEASE_LIB='/data/build_release/'
        //目标机器-代码最终部署路径
        RELEASE_TO='/data/site/'

        //测试环境
        DEVELOP_USER='root'
        DEVELOP_S1='10.30.185.40'
        //生产环境
        MASTER_USER='root'
        MASTER_S1='10.29.165.44'
    }

    stages {
        stage('检出') {
            steps {
                script {
                    println('检出');
                }
            }
        }
        stage('编译') {
            //判断是否回滚
            when { allOf { environment name: 'RELEASE_ENV', value: '1' } }

            steps {
                //Git Submodule
                sh "git submodule init && git submodule update"

                //Composer Build...
                //sh "composer install -vvv"

                sh "composer update -vvv"

                //Yii2 重新初始化环境配置
                script {
                    if (env.BRANCH_NAME == 'master') {
                        sh "./init --env=Production --overwrite=y"
                    } else {
                        sh "./init --env=Test --overwrite=y"
                    }
                }
            }
        }
        stage('测试') {
            parallel {
                stage('单元测试') {
                    steps {
                        println('跳过...');
                    }
                }
                stage('接口测试') {
                    steps {
                        println('跳过...');
                    }
                }
                stage('集成测试') {
                    steps {
                        println('跳过...');
                    }
                }
                stage('回归测试') {
                    steps {
                        println('跳过...');
                    }
                }
            }
        }
        stage('打包') {
            //判断是否回滚
            when { allOf { environment name: 'RELEASE_ENV', value: '1' } }

            steps {
                //删除文件(README.md)
                sh "rm -rf README.md"

                //删除文件(Yii2)
                sh "rm -rf console/runtime backend/runtime api/runtime"
                sh "rm -rf console/web/assets/* backend/web/assets/* api/web/assets/*"

                //打包
                sh "tar -p --exclude=.release --exclude=.ht --exclude=.svn --exclude=.git --exclude=.gitignore --exclude=.DS_Store -czvf ${VERSION}.tar.gz *"
            }
        }
        stage('部署') {
            //判断是否回滚
            when { allOf { environment name: 'RELEASE_ENV', value: '1' } }

            parallel {
                stage('S1') {
                    steps {
                        script {
                            if (env.BRANCH_NAME == 'master') {
                                deployJob(MASTER_S1)
                            } else {
                                deployJob(DEVELOP_S1)
                            }
                        }
                    }
                }
                // stage('S2') {
                //     when { branch 'master' }
                //     steps {
                //         deployJob(MASTER_S2)
                //     }
                // }
            }
        }
        stage('预发布') {
            //判断是否回滚
            when { allOf { branch 'master'; environment name: 'RELEASE_ENV', value: '1' } }

            steps {
                /* 预发布脚本...... */

                timeout(time:3, unit:'DAYS') {
                    input '确认更新生产环境？'
                }
            }
        }
        stage('更新') {
            parallel {
                stage('S1') {
                    steps {
                        script {
                            if (env.BRANCH_NAME == 'master') {
                                updateLnJob(MASTER_S1, MASTER_USER)
                            } else {
                                updateLnJob(DEVELOP_S1, DEVELOP_USER)
                            }
                        }
                    }
                }
                // stage('S2') {
                //     when { branch 'master' }

                //     steps {
                //         updateLnJob(MASTER_S2)
                //     }
                // }
            }
        }
        stage('后置操作') {
            steps {
                println('后置操作');
            }
        }
    }

    post {
        always {
            sh "rm -rf ${VERSION}.tar.gz"
        }

        success {
            //版本维护
            script {
                if (env.BRANCH_NAME == 'master') {
                    sh "ssh root@${MASTER_S1} 'ls -1 ${RELEASE_LIB}${PROJECT_NAME}/' > .release/list"
                } else {
                    sh "ssh root@${DEVELOP_S1} 'ls -1 ${RELEASE_LIB}${PROJECT_NAME}/' > .release/list"
                }
            }
        }
    }
}

/* ================= FUNCTION ================= */
/*
 * 部署
 */
def deployJob(host) {
    //创建发布版本文件夹
    sh "ssh root@${host} mkdir -vp ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}"

    //传输压缩包到指定目标机器
    sh "scp ${VERSION}.tar.gz root@${host}:${RELEASE_LIB}${PROJECT_NAME}/"

    //解压缩
    sh "ssh root@${host} 'tar -zxf ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}.tar.gz -C ${RELEASE_LIB}${PROJECT_NAME}/${VERSION} && rm -rf ${RELEASE_LIB}${PROJECT_NAME}/*.tar.gz'"

    //Yii2软链
    sh "ssh root@${host} 'ln -sfn /data/runtime/${PROJECT_NAME}/backend/runtime ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}/backend/ && ln -sfn /data/runtime/${PROJECT_NAME}/api/runtime ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}/api/ && ln -sfn /data/runtime/${PROJECT_NAME}/console/runtime ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}/console/ && ln -sfn /data/runtime/${PROJECT_NAME}/api/uploads ${RELEASE_LIB}${PROJECT_NAME}/${VERSION}/api/web/'"

    //删除老版本
    sh "ssh -T -q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o CheckHostIP=false 'root'@'${host}' 'cd ${RELEASE_LIB}${PROJECT_NAME}/ && ls -1rt | tac | awk '\\''FNR > ${KEEP_VERSION_NUM}  {printf(\"rm -rf %s\\n\", \$0);}'\\'' | bash'"

    return true
}

/*
 * 软链更新
 */
def updateLnJob(host, user='root') {
    //目标机器文件夹检查（代码最终部署路径）
    def is_release_to = sh returnStatus: true, script: 'ssh root@${host} test -d ${RELEASE_TO}'
    if (is_release_to == '1') {
        sh "ssh root@${host} mkdir -vp ${RELEASE_TO}"
    }

    //打软链
    sh "ssh root@${host} 'ln -sfn ${RELEASE_LIB}${PROJECT_NAME}/${VERSION} ${RELEASE_LIB}${PROJECT_NAME}/current-${PROJECT_NAME}.tmp && chown -h ${user} ${RELEASE_LIB}${PROJECT_NAME}/current-${PROJECT_NAME}.tmp && mv -fT ${RELEASE_LIB}${PROJECT_NAME}/current-${PROJECT_NAME}.tmp ${RELEASE_TO}${PROJECT_NAME}'"

    //所有目标机器都部署完毕之后，做一些清理工作
    sh "ssh root@${host} 'rm -rf console/runtime/cache/* api/runtime/cache/* backend/runtime/cache/*  && rm -rf api/runtime/Smarty/* backend/runtime/Smarty/* '"

    //restart php-fpm server
    if (env.BRANCH_NAME == 'master') {
        sh "ssh root@${MASTER_S1} 'service php-fpm restart'"
    } else {
        sh "ssh root@${DEVELOP_S1} 'service php-fpm restart'"
    }
    return true
}


/*
 * 获取当前版本号
 */
def getVersion() {
    def version = sh returnStdout: true, script: "git log --oneline | head -n 1 | cut -d' ' -f1"
    return version.trim()
}

/*
 * 获取发布模式  1:发布  0:回滚
 */
def getReleaseEnv(version, branchName) {
    //创建发布缓存文件夹
    sh '''
        if [ ! -d ".release" ]; then
            mkdir -v .release
        fi

        if [ ! -f ".release/list" ]; then
            touch .release/list
        fi
    '''

    //判断是否需要回滚
    def releaseEnv = sh returnStatus: true, script: "cat .release/list | grep ${version}"
    return releaseEnv
}