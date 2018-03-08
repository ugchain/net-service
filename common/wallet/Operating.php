<?php
namespace common\wallet;

use Yii;
use common\models\CenterBridge;
use common\helpers\CurlRequest;
use common\helpers\OutputHelper;
use common\helpers\ErrorCodes;
use common\models\RedPacket;
use common\models\RedPacketRecord;
use admin\modules\reward\models\Reward;

use common\models\Trade;

class Operating
{
    const SECURITY_BLOCK = 12;  //安全块
    const SUB_BIT = 2;          //截取位数
    const LOG_UNLOCK_STATUS = 0;//日志文件锁状态（开）
    const LOG_LOCK_STATUS = 1;  //日志文件锁状态（关）

    /**
     * 截取数据和进制转换
     * @param $trade_info
     *
     * @return mixed
     */
    public static function substrHexdec($trade_info)
    {
        if(isset($trade_info["blockNumber"])){
            //blockNumber截取前两位0x
            $trade_info["blockNumber"] = substr($trade_info["blockNumber"],self::SUB_BIT);
            //16进制 转换为10进制 后 -12块获取最新块
            $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);
        }
        if (isset($trade_info["gasUsed"])) {
            //gas_used截取前两位0x
            $trade_info["gasUsed"]= substr($trade_info["gasUsed"],self::SUB_BIT);
            //16进制 转换为10进制
            $trade_info["gasUsed"] = hexdec($trade_info["gasUsed"]);
        }
        if(isset($trade_info["result"])){
            $trade_info["result"] = substr($trade_info["result"],self::SUB_BIT);
            $trade_info["result"] = hexdec($trade_info["result"]);
        }

        return $trade_info;
    }

    /**
     * ug签名时gas值
     */
    public static function UgGasPrice()
    {
        return Yii::$app->params["ug"]["gas_price"];
    }

    /**
     * 获取最新安全块
     * @return number
     */
    public static function getNewSafetyBlock()
    {
        $new_block_data = CurlRequest::ChainCurl(Yii::$app->params["eth"]["eth_host"],"eth_blockNumber",[]);
        //{"jsonrpc":"2.0","id":"1","result":"0xaa6"} result 是16进制 需要转换为10进制
        if(!$new_block_data){
            return false;
        }
        //解析最新块
        $newblock_str = json_decode($new_block_data,true)["result"];
        //截取前两位0x
        $newblock_str = substr($newblock_str,self::SUB_BIT);
        //16进制 转换为10进制 后 -12块获取最新块
        return hexdec($newblock_str) - self::SECURITY_BLOCK;
    }

    /**
     * 获取nonce值且组装数据
     * @param $data
     * @param $gas_price
     * @param $host
     * @param $function
     * @param $param
     *
     * @return array
     */
    public static function getNonceAssembleData($data, $gas_price, $host, $function, $param)
    {
        //获取nonce值
        $nonce = CurlRequest::ChainCurl($host, $function, $param);
        if(!$nonce){
            return false;
        }
        //var_dump($nonce);die;
        $nonce_data = json_decode($nonce,true);
        $nonce_data = self::substrHexdec($nonce_data);
        //组装数据
        $send_sign_data = [
            "txId" => $data["app_txid"],
            "to" => $data["address"],
            "address" => $data["address"],
            "amount" => $data["amount"],
            "gasPrice" => $gas_price,
            "gas" => Yii::$app->params["eth"]["gas_limit"],
            "nonce" => (string)$nonce_data["result"]
        ];
       // var_dump($send_sign_data);die;
        return $send_sign_data;
    }

    /**
     * 获取签名且广播交易
     * @param $sign_host
     * @param $send_sign_data
     * @param $ug_host
     * @param $function
     *
     * @return bool|mixed
     */
    public static function getSignatureAndBroadcast($sign_host, $send_sign_data, $host, $function)
    {
        //获取离线签名
        $sign_res = CurlRequest::curl($sign_host, $send_sign_data);
        //写log
        OutputHelper::log("离线签名 ".json_encode($send_sign_data)." -- 签名返回信息: ".$sign_res, "internal_transfer");

        if(!$sign_res){
            return false;
        }
        $sign_res_data = json_decode($sign_res,true);
        if(isset($sign_res_data["status"])){
            return false;
        }

        //链上广播交易
        $broadcasting = CurlRequest::ChainCurl($host, $function, [$sign_res_data['data']["raw_transaction"]]);

        //写log
        OutputHelper::log("链上广播: ".$sign_res_data['data']["transaction_hash"]."-- 链上返回信息: ".$broadcasting, "internal_transfer");

        if(!$broadcasting){
            return false;
        }
        return json_decode($broadcasting,true);
    }

    /**
     * 获取待确认信息
     * @param string $type
     * @param        $logFile
     * @return array|bool
     */
    public static function getUnconfirmedList($type = CenterBridge::UG_ETH, $logFile)
    {
        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus($type);
        if (!$unsucc_info) {
            //OutputHelper::writeLog($logFile, json_encode(["status" => self::LOG_UNLOCK_STATUS]));
            return false;
        }
        return $unsucc_info;
    }

    public static function txidByTransactionInfo($host, $function, $param)
    {
        $block_info = CurlRequest::ChainCurl($host, $function, $param);
        if (!$block_info) {
            return false;
        }

        $block_info = json_decode($block_info,true);
        if (isset($block_info["error"])) {
            return false;
        }

        $trade_info = $block_info["result"];
        if($trade_info["blockNumber"] == null){
            return false;
        }
        if(isset($trade_info["status"])){
            if($trade_info["status"] != "0x1"){
                return false;
            }
        }

        return $trade_info;
    }

    /**
     * 修改表状态和时间
     * @param $type
     * @param $txid
     *
     * @return bool
     */
    public static function updateDataBytxid($type, $txid)
    {
        if ($type == Trade::CREATE_REDPACKET) {
            //更新红包表 status创建成功, create_succ_time创建成功时间 expire_time 过期时间
            $expire_time = time() + 86400;
            return RedPacket::updateAll(["status" => RedPacket::CREATE_REDPACKET_SUCC, "create_succ_time" => time(),"expire_time" => $expire_time], ["txid" => $txid]);
        } else if($type == Trade::OPEN_REDPACKET) {
            //更新红包记录表 status兑换成功, exchange_time兑换时间
            return RedPacketRecord::updateAll(["status" => RedPacketRecord::EXCHANGE_SUCC, "exchange_time" => time()], ["txid" => $txid]);
        } else if($type == Trade::BACK_REDPACKET) {
            //更新红包表 status已过期(不改变), expire_time过期时间
            return RedPacket::updateAll(["status" => RedPacket::REDPACKET_EXPIRED, "expire_time" => time()], ["txid" => $txid]);
        } else if($type == Trade::BACK_REDPACKET) {
            //更新奖励表
            return Reward::updateAll(["status" => Reward::REWARD_SUCCESS, "trade_time" => time()], ["app_txid" => $txid]);
        } else {
            return true;
        }
    }

}