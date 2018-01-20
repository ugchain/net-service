<?php
namespace common\helpers;
use Yii;
class OutputHelper
{
    /**
     * 组装返回数据
     */
    public static function  ouputErrorcodeJson($code,$data=[])
    {
        header('Content-type:text/json;charset=utf-8');
        $errorcode['code'] = $code;
        $errorcode['message'] = ErrorCodes::$ERR_MSG[$code];
        $errorcode['data'] = $data;
        echo json_encode($errorcode);die;
    }

    /**
     * 读取log
     * @param $logFileName
     * @return bool
     */
    public static function readLog($logFileName)
    {
        $ethlisten = file_get_contents($logFileName);
        $ethlistenlog = json_decode($ethlisten,true);
        if($ethlistenlog["status"] != 0){
            echo "正在执行中";die();
        }
        return true;
    }

    /**
     * 写入log
     * @param $logUrl
     * @param $status
     *
     * @return bool
     */
    public static function writeLog($logUrl, $status)
    {
        file_put_contents($logUrl ,json_encode(["status" => $status]));
        return true;
    }

    /**
     * 截取数据和进制转换
     * @param $trade_info
     *
     * @return mixed
     */
    public static function substrHexdec($trade_info, $type = 1)
    {
        if ($type == 1) {
            //blockNumber截取前两位0x
            $trade_info["blockNumber"] = substr($trade_info["blockNumber"],2);
            //16进制 转换为10进制 后 -12块获取最新块
            $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);

            //gas_price截取前两位0x
            $trade_info["gasPrice"] = substr($trade_info["gasPrice"],2);
            //16进制 转换为10进制
            $trade_info["gasPrice"] = hexdec($trade_info["gasPrice"]);

        } else {
            //gas_used截取前两位0x
            $trade_info["gasUsed"]= substr($trade_info["gasUsed"],2);
            //16进制 转换为10进制
            $trade_info["gasUsed"] = hexdec($trade_info["gasUsed"]);
        }

        return $trade_info;
    }

    /**
     * 获取最新安全块
     * @return number
     */
    public static function getNewSafetyBlock()
    {
        $new_block_data = CurlRequest::EthCurl("eth_blockNumber",[]);
        //{"jsonrpc":"2.0","id":"1","result":"0xaa6"} result 是16进制 需要转换为10进制
        if(!$new_block_data){
            echo "eth返回块信息错误";die;
        }
        //解析最新块
        $newblock_str = json_decode($new_block_data,true)["result"];
        //截取前两位0x
        $newblock_str = substr($newblock_str,2);
        //16进制 转换为10进制 后 -12块获取最新块
        return hexdec($newblock_str) - 12;
    }

}