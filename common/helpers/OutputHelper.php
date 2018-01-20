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
}