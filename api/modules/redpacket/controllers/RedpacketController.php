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