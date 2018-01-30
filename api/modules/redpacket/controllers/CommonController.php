<?php

namespace api\modules\redpacket\controllers;

use api\modules\redpacket\models\RedPacketTheme;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;

class CommonController extends  Controller
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
    * 中心化账户地址接口
    */
    public function actionCenterAddress()
    {
       //组装数据
        $data = ["address"=>Yii::$app->params["ug"]["red_packet_address"]];
       outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
    }

}