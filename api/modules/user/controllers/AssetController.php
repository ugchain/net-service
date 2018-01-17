<?php
namespace api\modules\user\controllers;

use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\user\models\Address;
use api\modules\user\models\CenterBridge;

class AssetController extends  Controller
{

    public $enableCsrfValidation = false;
    const ETHUG = 1;
    const UGETH = 2;
    const ADDRESSLEN = 42;

    /**
     * 划转通知
     */
    public function actionTransferNotice()
    {
        //交易id
        $txid = Yii::$app->request->post("txid", "");
        //地址
        $address = Yii::$app->request->post("address", "");
        //划转类型
        $type = Yii::$app->request->post("type", self::ETHUG);

        //检查地址位数及空
        if (!$address || strlen($address) != self::ADDRESSLEN) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
        }
        //校验地址是否存在
        if (!$address_info = Address::getInfoByAddress($address)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
        }
        //检验txid是否存在
        if ($txid_info = CenterBridge::getTxidInfo($txid)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TXID_EXIST);
        }

        //插入划转通知
        if(!$result = CenterBridge::insertData($txid, $address, $type, CenterBridge::CONFIRMED, time())){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 划转记录
     */
    public function actionTransferRecord()
    {
        //地址
        $address = Yii::$app->request->post("address", "");

        //校验地址是否存在
        if (!$address_info = Address::getInfoByAddress($address)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
        }

        $result = [];
        $result = CenterBridge::getList($address);
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }
}