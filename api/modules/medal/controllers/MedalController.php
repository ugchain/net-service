<?php
namespace api\modules\medal\controllers;


use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;

class MedalController extends  Controller
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
     * 创建/导入用户地址
     */
    public function actionCreateUser()
    {
        //昵称
        $nickname = Yii::$app->request->post("nickname","");
        //地址
        $address = Yii::$app->request->post("address");
        if(!$address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }

    }
}