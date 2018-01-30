<?php
namespace api\modules\redpacket\controllers;

use api\modules\redpacket\models\RedPacket;
use api\modules\redpacket\models\RedPacketRecord;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;

class RedpacketController extends  Controller
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
     * 创建红包
     */
    public function actionCreatePacket()
    {
        //红包标题
        $title = Yii::$app->request->post("title", "");
        //主题ID
        $theme_id = Yii::$app->request->post("theme_id", "");
        //地址
        $address = Yii::$app->request->post("address", "");
        //金额
        $amount = Yii::$app->request->post("amount", "");
        //个数
        $quantity = Yii::$app->request->post("quantity", "");
        //红包类型
        $type = Yii::$app->request->post("type", "0");
        //离线签名
        $raw_transaction = Yii::$app->request->post("raw_transaction", "");
        //hash
        $hash = Yii::$app->request->post("hash", "");

        //判断

    }

        /**
         * 红包兑换
         */
        public function actionExchange()
        {
            //兑换码
            $code = Yii::$app->request->post("code", "");
            //账户地址
            $address = Yii::$app->request->post("address", "");

            //校验账户和兑换码
            RedPacketRecord::checkCodeAndAddress($address, $code);

        }



}