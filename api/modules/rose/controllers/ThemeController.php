<?php

namespace api\modules\rose\controllers;


use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;
use api\modules\rose\models\RoseTheme;

class ThemeController extends  Controller
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access-behavior' => [
//                'class' => 'common\behavior\AccessBehavior',//验证签名
//            ]
//        ];
//    }

   /**
    * 创建主题
    */
    public function actionCreateTheme()
    {
        //实例化
       $model = new RoseTheme();
       //主题标题
       $model->title = Yii::$app->request->post("title","");
       $model->content = Yii::$app->request->post("content","");
       $model->slogan = Yii::$app->request->post("slogan","");
       //判断参数
       if(!$model->title || !$model->content || !$model->slogan){
           outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
       }
       //实例化文件
       $model->img = UploadedFile::getInstanceByName('img');
       $model->thumb_img = UploadedFile::getInstanceByName('thumb_img');
       $model->banner_img = UploadedFile::getInstanceByName('banner_img');
       //判断是否有图片上传
       if(!$model->img || !$model->thumb_img || !$model->banner_img){
           outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::UPLOAD_FILE_FALL);
       }
       //上传文件到服务器
       $model->upload();
       $model->addtime = time();
       //保存红包主题
       if(!$model->save()){
           outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
       }
       outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);

    }


    /**
     *主题列表接口
     */
     public function actionThemeList()
     {
        //获取主题列表数据
         $theme_list = RoseTheme::getList();
         //组装数据
         $data = [
             "list"=>$theme_list,
             "image_url"=>Yii::$app->params['image_url']
         ];
         outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
     }

}