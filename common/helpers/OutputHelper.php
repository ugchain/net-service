<?php
namespace common\helpers;
use Yii;
use common\models\CenterBridge;
use yii\log\Logger;

class OutputHelper
{

    /**
     * 组装返回数据
     */
    public static function ouputErrorcodeJson($code,$data=[])
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
        file_put_contents($logUrl, $status);
        return true;
    }

    /**
     * 生成唯一ID
     */
    public static function guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true) . time()));
        $uuid =
            substr($charid, 0, 12) .
            substr($charid, 8, 12) .
            substr($charid, 12, 12) .
            substr($charid, 16, 12) .
            substr($charid, 20, 12) .
            substr($charid, 24, 4);
        return $uuid;
    }
    /**
     * 科学计数法数字转为字符串
     */
    public static function NumToString($num){
        if(false !== stripos($num, "e")){
            $num = number_format($num,10,'.','');
        }

        if(false !== stripos($num, ".")){
            while (preg_match("/0$/i", $num)){
                $num = rtrim($num,'0');
            };
        }
        $num = rtrim($num,'.');
        return (string)$num;
    }

    /**
     * web3 ether to wei
     * @param $number
     */
    public static function fromWei($number)
    {
        return $number / pow(10,18);
    }


    /**
     * web3 wei to ether
     * @param $number
     */
    public static function toWei($number)
    {
        return $number * pow(10,18);
    }

    /**
     * log日志
     */
    public static function log($message = "", $filename, $level = Logger::LEVEL_INFO)
    {
        $date = "时间 : ".date("Y-m-d H:i:s",time())."---";
        Yii::getLogger()->log($date.$message, $level,$filename);
        return true;
    }

}