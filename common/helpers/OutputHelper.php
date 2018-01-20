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
     */
    public static function writeLog($logUrl, $status)
    {
        file_put_contents($logUrl ,json_encode(["status" => $status]));
        return true;
    }

    /**
     * 截取数据和进制转换
     */
    public static function substrHexdec($trade_info)
    {
        //blockNumber截取前两位0x
        $trade_info["blockNumber"] = substr($trade_info["blockNumber"],2);
        //16进制 转换为10进制 后 -12块获取最新块
        $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);

        //gas_price截取前两位0x
        $trade_info["gasPrice"] = substr($trade_info["gasPrice"],2);
        //16进制 转换为10进制
        $trade_info["gasPrice"] = hexdec($trade_info["gasPrice"]);

        return $trade_info;
    }
}