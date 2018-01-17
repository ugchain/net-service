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
     *勋章-我的资产
     * @param address 地址
     */
    public function actionGetList()
    {
        //地址
        $address = Yii::$app->request->post("address");

    }
}