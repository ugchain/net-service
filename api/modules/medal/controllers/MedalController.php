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

    /**
     * 勋章转赠
     */
    public function actionMedalGive()
    {
        //持有者
        $address = Yii::$app->request->post("owner_address", "");
        //勋章id
        $medal_id = Yii::$app->request->post("medal_id", "");
        //接收勋章者
        $address = Yii::$app->request->post("owner_address", "");

        //校验持有者是否真实持有勋章

        //校验接收勋章者是否存在

    }
}