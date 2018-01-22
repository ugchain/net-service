<?php
namespace api\modules\user\controllers;

use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\user\models\Address;
use api\modules\user\models\Trade;
use api\modules\user\models\CenterBridge;

class AssetController extends  Controller
{

    public $enableCsrfValidation = false;
    const ETHUG = 1;
    const UGETH = 2;
    const ADDRESSLEN = 42;

    /**
     * 跨链划转通知
     */
    public function actionTransferNotice()
    {
        //交易id
        $txid = Yii::$app->request->post("txid", "");
        //地址
        $address = Yii::$app->request->post("address", "");
        //价格
        $amount = Yii::$app->request->post("amount", 0);
        //划转类型
        $type = Yii::$app->request->post("type", self::ETHUG);
        //gasPrice
        $gasPrice = Yii::$app->request->post("gasPrice", 0);


//        //检查地址位数及空
//        if (!$address || strlen($address) != self::ADDRESSLEN) {
//            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
//        }
//        //校验地址是否存在
//        if (!$address_info = Address::getInfoByAddress($address)) {
//            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
//        }

        //检验txid是否存在
        if ($txid_info = CenterBridge::getTxidInfo($txid)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TXID_EXIST);
        }

        //插入划转通知
        if(!$result = CenterBridge::insertData($txid, $address, $type, $amount, $gasPrice ,CenterBridge::CONFIRMED, time())){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 跨链划转记录
     */
    public function actionTransferRecord()
    {
        //地址
        $address = Yii::$app->request->post("address", "");
        //交易id
        $txid = Yii::$app->request->post("txid", "");
        $page = Yii::$app->request->post("page", "1");
        $pageSize = Yii::$app->request->post("pageSize", "10");

        //校验地址是否存在
//        if (!$address_info = Address::getInfoByAddress($address)) {
//            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
//        }

        $result = [];
        $result = CenterBridge::getList($address, $page, $pageSize);
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }

    /**
     * ug划转通知
     */
    public function actionUgTransferNotice()
    {
        //交易id
        $txid = Yii::$app->request->post("txid", "");
        //地址
        $from = Yii::$app->request->post("from", "");
        //地址
        $to = Yii::$app->request->post("to", "");
        //价格
        $amount = Yii::$app->request->post("amount", 0);

        //检验txid是否存在
        if ($txid_info = Trade::getTxidInfo($txid)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TXID_EXIST);
        }

        //插入划转通知
        if(!$result = Trade::insertData($txid, $from, $to, $amount, Trade::CONFIRMED, time())){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * ug交易记录
     */
    public function actionTradeRecord()
    {
        //地址
        $address = Yii::$app->request->post("address", "");
        $page = Yii::$app->request->post("page", Yii::$app->params['pagination']['page']);
        $pageSize = Yii::$app->request->post("pageSize", Yii::$app->params['pagination']['pageSize']);
        //参数验证
        if(!$address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        $trade_record = Trade::getRecordByAddress($address,$page,$pageSize);
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$trade_record);
    }

}