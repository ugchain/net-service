<?php
namespace common\helpers;
use Yii;
use common\models\CenterBridge;

class OutputHelper
{

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

}