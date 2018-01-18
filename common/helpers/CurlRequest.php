<?php
namespace common\helpers;

class CurlRequest
{
    /**
     * curl通过jsonRpc获取数据
     * @param $url
     * @param $request_data
     * @param $method
     * @return mixed
     */
    public static function curl($url,$data,$type='post')
    {
        $json_data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($json_data)]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出,存放到变量中
        $ret =curl_exec($ch);
        curl_close ( $ch );
        return $ret;
    }
}