<?php

namespace api\modules\redpacket\controllers;

use api\modules\redpacket\models\PacketOfflineSign;
use common\helpers\RewardData;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\redpacket\models\RedPacket;
use api\modules\user\models\Trade;
use api\modules\redpacket\models\RedPacketRecord;
use common\helpers\CurlRequest;
use common\wallet\Operating;
use common\helpers\Reward;

class RedpacketController extends  Controller
{

    public $enableCsrfValidation = false;

    //红包获取最大最小值
    const MAX = 1.4;
    const MIN = 0.6;
    public $REPACK_STATUS;
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
        //接收参数&&验证参数
        $data = self::getParams();
        //创建红包
        $packet_id = RedPacket::saveRedPacket($data);
        if(!$packet_id){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //组装创建红包的签名数据
            $sign_data = [
                "packet_id" => $packet_id,
                "address" => $data["from_address"],
                "raw_transaction" => $data["raw_transaction"],
                "type" => "0",
            ];
            //保存创建红包的签名
            $sign_save_status = PacketOfflineSign::saveOfflineSign($sign_data);
            //保存交易历史记录
            $trade_save_status = Trade::insertData($data["hash"], $data["from_address"], $data["to_address"], $data["amount"],Trade::CONFIRMED,Trade::CREATE_REDPACKET);
            if(!$sign_save_status || !$trade_save_status){
                $transaction->rollBack();
            }
            //提交事务
            $transaction->commit();
        }catch (\Exception $e) {
            $transaction->rollBack();
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //事务结束
        //红包计算公示(红包ID：{2,3,4,5})
        $redis_data = [];
        $amount = (string)OutputHelper::fromWei($data["amount"]);
        $average_amount = $amount / $data['quantity'];
        if($data["type"] == 0){
            for($i=0;$i<$data["quantity"];$i++){
                $redis_data[$i] = $average_amount;
            }
        }else{
            //随机红包分配
            $max = $average_amount * self::MAX;
            $min = $average_amount * self::MIN;
            $redis_data = self::random_red($amount,$data["quantity"],$max,$min);
        }
        $this->REPACK_STATUS = 0;
        //存放redis
        $rewardData = new RewardData();
        $rewardData->set($packet_id,$redis_data);
        //发送离线签名数据
        $res_data = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction", [$data['raw_transaction']]);
        if(!$res_data){
            RedPacket::updateStatus($packet_id,"1");
            $this->REPACK_STATUS = 1;
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_SEND_CHAIN_FALL,["status"=>$this->REPACK_STATUS]);
        }
        //检测是否上链--成功5%
        $block_info = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$data["hash"]]);
        if($block_info){
            $block_info = json_decode($block_info,true);
            if(!isset($block_info["error"]) && $block_info["result"]["blockNumber"] != null){
                //blockNumber 不为空
                //检测上链成功,更新红包状态为status=2 && ug_trade 交易记录改为交易成功
                RedPacket::updateStatus($packet_id,"2");
                //blockNumber截取前两位0x && 16进制 转换为10进制
                $trade_info = Operating::substrHexdec($block_info["result"]);
                Trade::updateBlockAndStatusBytxid($data["hash"], $trade_info["blockNumber"], Trade::SUCCESS);
                $this->REPACK_STATUS = 2;
            }
        }
        $repack_info = RedPacket::getPacketInfoById($packet_id);
        //组装返回数据
        $return_data = [
            "share_url"=>Yii::$app->params["host"]."/redpacket/we-chat-red-packet/redirect-url?redpacket_id=".$packet_id,
            "id"=>$packet_id,
            "status"=>$this->REPACK_STATUS,
            "expire_time"=>$repack_info["expire_time"],
            "create_succ_time"=>$repack_info["create_succ_time"],
        ];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$return_data);
    }

    /**
     * 创建红包时接收参数,处理参数
     * @return mixed
     */
    private function getParams()
    {
        //var_dump(Yii::$app->request->post());die;
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
        //发地址
        $data['from_address'] = Yii::$app->request->post("from_address", "");
        //接收地址
        $data['to_address'] = Yii::$app->request->post("to_address", "");
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
        if(!$data['title'] || !$data['theme_id'] || !$data['from_address'] ||!$data["to_address"] || !$data['amount'] || !$data['quantity'] || !$data['raw_transaction'] || !$data['hash']){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }

        //验证红包数量
        if ($data['quantity'] > 200) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //验证红包title

        return $data;
    }

    /**
     * 生成随机红包算法
     */
    private function random_red($total, $num, $max, $min)
    {
        #总共要发的红包金额，留出一个最大值;
        $total = $total - $max;
        $reward = new Reward();
        $result_merge = $reward->splitReward($total, $num, $max - 0.01, $min);
        sort($result_merge);
        $result_merge[1] = $result_merge[1] + $result_merge[0];
        $result_merge[0] = $max * 100;
        foreach ($result_merge as &$v) {
            $v = floor($v) / 100;
        }
        return $result_merge;
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
        //类型 1获取红包信息；2兑换红包
        $type = Yii::$app->request->post("type", "1");

        //校验账户和兑换码
        $result = RedPacketRecord::checkCodeAndAddress($code);
        if ($result == 1) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_NOT_EXIST);
        }
        if ($result == 2) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_OPEN);
        }
        //查询红包是否存在&红包是否过期
        if (!$redPacketInfo = RedPacket::checkRedPacketExistAndExpired($result['rid'])) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_EXPIRED);
        }

        if ($type == 2) {
            //获取nince且组装签名数据
            $result['address'] = $address;
            $result['app_txid'] = ''; //空的
//            $result['amount'] = OutputHelper::NumToString($result['amount'] * pow(10, 18));
//            $result['amount'] = $result['amount'];
            $send_sign_data = Operating::getNonceAssembleData($result, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth_getTransactionCount", [Yii::$app->params["ug"]["red_packet_address"], "pending"]);

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_red_packet"], $send_sign_data, Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction");
            if (isset($res_data['error'])) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::TRANSACTION_FAIL);
            }

            //根据txid去块上确认
            $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);

            //组装入库数据
            $recordStatus = RedPacketRecord::REDEMPTION;
            $tradeStatus = Trade::CONFIRMED;
            if ($trade_info) {
                //截取blockNumber
                $trade_info = Operating::substrHexdec($trade_info["blockNumber"]);
                $tradeStatus = Trade::SUCCESS;
                $recordStatus = RedPacketRecord::EXCHANGE_SUCC;
            }

            //修改红包记录表状态
            if (!RedPacketRecord::updateStatusAndTxidByid($result['id'], $recordStatus, $res_data["result"], $address)) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
            }
            //插入内部交易表
            if (!Trade::insertData($res_data["result"], Yii::$app->params["ug"]["red_packet_address"], $result["to_address"], $result["amount"], $tradeStatus, Trade::OPEN_REDPACKET, empty($trade_info['blockNumber'])?0:$trade_info['blockNumber'])) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
            }

            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, ['id' => $result['rid'], 'amount' => $result['amount']]);
        } else {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, ['id' => $redPacketInfo['id'], 'title' => $redPacketInfo['title']]);
        }

    }

    /**
     * 红包详情
     */
    public function actionDetail()
    {
        //红包id
        $id = Yii::$app->request->post("id", "");

        //获取红包详情数据
        $result = RedPacket::getRedPacketInfoWithRecordList($id);
        if($result["status"] == 0){
            //检测是否上链--成功5%
            $block_info = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$result["txid"]]);
            if($block_info){
                $block_info = json_decode($block_info,true);
                //blockNumber 不为空
                if(!isset($block_info["error"]) && $block_info["result"]["blockNumber"] != null){
                    //检测上链成功,更新红包状态为status=2 && ug_trade 交易记录改为交易成功
                    RedPacket::updateStatus($result["id"],"2");
                    //blockNumber截取前两位0x && 16进制 转换为10进制
                    $trade_info = Operating::substrHexdec($block_info["result"]);
                    Trade::updateBlockAndStatusBytxid($result["txid"], $trade_info["blockNumber"], Trade::SUCCESS);
                    $result["status"] = "2";
                }
            }
        }

        //返回数据
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
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

        //组装返回数据
        $result['image_url'] = Yii::$app->params['image_url'];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }

    /**
     * 监听红包是否上块
     */
    public function actionListenRepacket()
    {
        $packet_id = Yii::$app->request->post("packet_id","");
        if(!$packet_id){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //获取红包数据
        $repacket_info = RedPacket::getPacketInfoById($packet_id);
        if(!$repacket_info){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        if($repacket_info["status"] != 0) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        $this->REPACK_STATUS = 0;
        //检测是否上链--成功5%
        $block_info = CurlRequest::ChainCurl(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$repacket_info["hash"]]);
        if($block_info){
            $block_info = json_decode($block_info,true);
            //blockNumber 不为空
            if(!isset($block_info["error"]) && $block_info["result"]["blockNumber"] != null){
                //检测上链成功,更新红包状态为status=2 && ug_trade 交易记录改为交易成功
                RedPacket::updateStatus($packet_id,"2");
                //blockNumber截取前两位0x && 16进制 转换为10进制
                $trade_info = Operating::substrHexdec($block_info["result"]);
                Trade::updateBlockAndStatusBytxid($repacket_info["hash"], $trade_info["blockNumber"], Trade::SUCCESS);
                $this->REPACK_STATUS = 1;
            }
        }
        $data = ["status"=> $this->REPACK_STATUS];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
    }

}