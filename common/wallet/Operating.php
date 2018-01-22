<?php
namespace common\wallet;
use Yii;
use common\models\CenterBridge;
use common\helpers\CurlRequest;
use common\helpers\OutputHelper;
use common\helpers\ErrorCodes;

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
        //blockNumber截取前两位0x
        $trade_info["blockNumber"] = substr($trade_info["blockNumber"],self::SUB_BIT);
        //16进制 转换为10进制 后 -12块获取最新块
        $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);
        if (isset($trade_info["gasUsed"])) {
            //gas_used截取前两位0x
            $trade_info["gasUsed"]= substr($trade_info["gasUsed"],self::SUB_BIT);
            //16进制 转换为10进制
            $trade_info["gasUsed"] = hexdec($trade_info["gasUsed"]);
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
        $new_block_data = CurlRequest::ChainCurl(Yii::$app->params["eth_host"],"eth_blockNumber",[]);
        //{"jsonrpc":"2.0","id":"1","result":"0xaa6"} result 是16进制 需要转换为10进制
        if(!$new_block_data){
            echo "eth返回块信息错误";die;
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
    public static function getNonceAssembleData($data, $gas_price, $ug_host, $function, $param)
    {
        //获取nonce值
        $nonce = CurlRequest::ChainCurl($ug_host, $function, $param);

        //组装数据
        $send_sign_data = [
            "address" => $data["address"],
            "value" => $data["amount"],
            "gasPrice" => $gas_price,
            "gas" => "0",
            "nonce" => $nonce
        ];

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
        if(!$sign_res){
            return false;
        }
        $sign_res_data = json_decode($sign_res,true);
        //链上广播交易
        $broadcasting = CurlRequest::ChainCurl($host, $function, ["data" => $sign_res_data["row_transaction"]]);
        if(!$broadcasting){
            return false;
        }
        return json_decode($broadcasting,true);
    }

    /**
     * 获取待确认信息
     * @param string $type
     * @param        $logFile
     *
     * @return array|bool
     */
    public static function getUnconfirmedList($type = CenterBridge::ETH_UG, $logFile)
    {
        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus($type);
        if (!$unsucc_info) {
            OutputHelper::writeLog($logFile, json_encode(["status" => self::LOG_UNLOCK_STATUS]));
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

        return $trade_info;
    }
}