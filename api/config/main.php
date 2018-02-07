<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //控制器默认命名空间
    'controllerNamespace' => 'api\controllers',
    //模块类
    'modules' => [
        'medal' => [
            'class' => 'api\modules\medal\Module',
        ],
        'user' => [
            'class' => 'api\modules\user\Module',
        ],
        'redpacket' => [
            'class' => 'api\modules\redpacket\Module',
        ],
        'rose' => [
            'class' => 'api\modules\rose\Module',
        ],
    ],
    //'defaultRoute'=>'user/user/test',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],

                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['cross_chain'],
                    'logFile' => '@app/runtime/eth/cross_chain'.date("Ymd",time()).'log',
                    'maxFileSize' => 1024 * 1024000,
                    'maxLogFiles' => 200,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['internal_transfer'],
                    'logFile' => '@app/runtime/ug/internal_transfer'.date("Ymd",time()).'log',
                    'maxFileSize' => 1024 * 102400,
                    'maxLogFiles' => 200,
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
