<?php

namespace api\modules\redpacket\controllers;

use api\modules\redpacket\models\PacketOfflineSign;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\redpacket\models\RedPacket;
use api\modules\user\models\Trade;
use api\modules\redpacket\models\RedPacketRecord;
use common\helpers\CurlRequest;
use common\wallet\Operating;

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
        $data['title'] = Yii::$app->request->post("title", "");
        //主题ID
        $data['theme_id'] = Yii::$app->request->post("theme_id", "");
        //主题ID
        $data['theme_img'] = Yii::$app->request->post("theme_img", "");
        //主题ID
        $data['theme_thumb_img'] = Yii::$app->request->post("theme_thumb_img", "");
        //主题ID
        $data['theme_share_img'] = Yii::$app->request->post("theme_share_img", "");
        //地址
        $data['address'] = Yii::$app->request->post("address", "");
        //金额
        $data['amount'] = Yii::$app->request->post("amount", "");
        //个数
        $data['quantity'] = Yii::$app->request->post("quantity", "");
        //红包类型
        $data['type'] = Yii::$app->request->post("type", "0");
        //离线签名
        $data['raw_transaction'] = Yii::$app->request->post("raw_transaction", "");
        //hash
        $data['hash'] = Yii::$app->request->post("hash", "");

        //验证参数
        if(!$data['title'] || !$data['theme_id'] || !$data['address'] || !$data['amount'] || !$data['quantity'] || ! $data['raw_transaction'] || $data['hash']){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //创建红包
        $packet_id = RedPacket::saveRedPacket($data);
        if(!$packet_id){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //发送离线签名数据
        $res_data = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_sign_url"], "eth_sendRawTransaction", [$data['raw_transaction']]);
        if(!$res_data){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //组装创建红包的签名数据
        $sign_data = [
            "packet_id" => $packet_id,
            "address" => $data["address"],
            "raw_transaction" => $data["raw_transaction"],
            "type" => "0",
        ];
        //保存创建红包的签名
        $sign_save_status = PacketOfflineSign::saveOfflineSign($sign_data);

        //保存交易历史记录
        $trade_save_status = Trade::insertData($data["hash"],$data["address"],Yii::$app->params["ug"]["red_packet_address"],"0",Trade::REDPACKET);
        if(!$sign_save_status || !$trade_save_status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //组装返回数据
        $return_data = [
            "url"=>"",
            "packet_id"=>$packet_id,
            "title"=>$data["title"],
        ];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$return_data);
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
        $result = RedPacketRecord::checkCodeAndAddress($address, $code);
        if (!$result) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_REDEMPTION);
        }
        $result['address'] = $result['to_address'];
        $result['app_txid'] = $result['txid'];
        //开始兑换红包
        //获取nince且组装签名数据
        $send_sign_data = Operating::getNonceAssembleData($result, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth_getTransactionCount", [Yii::$app->params["ug"]["owner_address"], "pending"]);

        //组装创建红包的签名数据
        $sign_data = [
            "packet_id" => $result['id'],
            "address" => $address,
            "raw_transaction" => $send_sign_data,
            "type" => "1",
        ];

        //保存创建红包的签名
        PacketOfflineSign::saveOfflineSign($sign_data);

        //根据组装数据获取签名且广播交易
        $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_url"], $send_sign_data, Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction");

        //根据txid去块上确认
        $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);

        //上链成功,插入内部交易表同时修改红包记录表状态
        $trade_info['blockNumber'] = 0;
        $recordStatus = RedPacketRecord::REDEMPTION;
        $tradeStatus = Trade::CONFIRMED;
        if ($trade_info) {
            //截取blockNumber
            $trade_info = Operating::substrHexdec($trade_info["result"]);
            $tradeStatus = Trade::SUCCESS;
            $recordStatus = RedPacketRecord::EXCHANGE_SUCC;
        }

        if (!RedPacketRecord::updateStatusByTxid($result['txid'], $recordStatus)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        if (!Trade::insertData($res_data["result"], $result["from_address"], $result["to_address"], $result["amount"], $tradeStatus, Trade::REDPACKET, $trade_info['blockNumber'])) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }

        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 红包详情
     */
    public function actionDetail()
    {
        //红包id
        $id = Yii::$app->request->post("id", "");
        //账户地址
        $address = Yii::$app->request->post("address", "");
    }

    /**
     * 红包记录
     */
    public function actionList()
    {
        //账户地址
        $address = Yii::$app->request->post("address", "");
        //类型 0我收到的；1我发出的
        $type = Yii::$app->request->post("type", "0");
        $page = Yii::$app->request->post("page", "1");
        $pageSize = Yii::$app->request->post("pageSize", "10");

        //获取红包记录
        $result = RedPacket::getRedList($address, $type, $page, $pageSize);

        $result['received_quantity'] = $result['count'];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }

}