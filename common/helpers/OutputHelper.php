<?php
namespace common\helpers;
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
     * 数字转为字符串（包括科学计数法）
     */
    public static function NumToStr($num){
        if(false !== stripos($num, "e")){
            $num = number_format($num,10,'.','');
        }
        if(false !== stripos($num, ".")){
            while (eregi("0$", $num)){
                $num = rtrim($num,'0');
            };
        }
        return (string)$num;
    }
}