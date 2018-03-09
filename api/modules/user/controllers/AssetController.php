<?php
namespace api\modules\user\controllers;


use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use common\helpers\CurlRequest;
use api\modules\user\models\Trade;
use api\modules\user\models\CenterBridge;
use admin\modules\reward\models\Reward;
use common\wallet\Operating;
use common\models\ExtraPrice;

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
        //写入log
        OutputHelper::log("跨链划转api信息:".json_encode(["txid" => $txid,"address" => $address,"amount" => $amount,"type" => $type == 1 ? "eth_ug" : "ug_eth"]),"cross_chain");
        //检验txid是否存在
        if ($txid_info = CenterBridge::getTxidInfo($txid)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TXID_EXIST);
        }

        //插入划转通知
        if(!$result = CenterBridge::insertData($txid, $address, $type, $amount, $gasPrice, CenterBridge::CONFIRMED, time())){
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
        //写入log
        OutputHelper::log("ug划转api信息: ".json_encode(["txid" => $txid, "from" => $from, "to" => $to, "amount" => $amount]),"internal_transfer");
        if(!Trade::insertData($txid, $from, $to, $amount, Trade::CONFIRMED)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //确认转账成功
        $block_info = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$txid]);
        //写入log
        OutputHelper::log("ug划转链上确认信息: ".$txid."--".$block_info, "internal_transfer");

        if (!$block_info) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        $block_info = json_decode($block_info,true);
        if (isset($block_info["error"])) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        if($block_info["result"]["blockNumber"] == null){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //进制转换
        $block_info["result"] = Operating::substrHexdec($block_info["result"]);
        if(!Trade::updateBlockAndStatusBytxid($txid, $block_info["result"]["blockNumber"], Trade::SUCCESS)){
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

    /**
     * ug-eth时手续费汇率
     */
    public function actionUgFree()
    {
        $free_info = ExtraPrice::getList();
        $data["ug_free"] = "6000";
        if(!$free_info || !$free_info["ug_extra_free"]){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
        }
        $data["ug_free"] = $free_info["ug_extra_free"];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
    }

    /**
     * 获取活动to_address
     */
    public function actionGettoaddress()
    {
        $data = Reward::getTo();
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
    }

    /**
     * ugc发放活动
     */
    public function actionUgReward()
    {
        //交易id
        $txid = Yii::$app->request->post("txid", "");
        //地址
        $from = Yii::$app->request->post("from", "");
        //地址
        $to = Yii::$app->request->post("to", "");
        //价格
        $amount = Yii::$app->request->post("amount", 0);
        //写入log
        OutputHelper::log("ugc奖励api信息: ".json_encode(["txid" => $txid, "from" => $from, "to" => $to, "amount" => $amount]),"internal_transfer");
        // 通过to地址update
        if(!Reward::updateData($txid, $from, $to, $amount, Trade::CONFIRMED)){
            //写入log
            OutputHelper::log("ugc奖励错误信息1: ".json_encode(["txid" => $txid, "from" => $from, "to" => $to, "amount" => $amount]),"internal_transfer");
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //检验txid是否存在
        if ($txid_info = Trade::getTxidInfo($txid)) {
            //写入log
            OutputHelper::log("ugc奖励错误信息2: ".json_encode(["txid" => $txid, "from" => $from, "to" => $to, "amount" => $amount]),"internal_transfer");
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TXID_EXIST);
        }

        if(!Trade::insertData($txid, $from, $to, $amount, Trade::CONFIRMED,Trade::REWARD)){
            //写入log
            OutputHelper::log("ugc奖励错误信息3: ".json_encode(["txid" => $txid, "from" => $from, "to" => $to, "amount" => $amount]),"internal_transfer");
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }
}