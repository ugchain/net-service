<?php

namespace common\behavior;

use common\helpers\OutputHelper;
use Yii;
use yii\base\ActionFilter;

/**
 * Class AccessBehavior
 * @Usage:
 *  'access-behavior' => [
 *      'class' => 'common\behavior\AccessBehavior',
 *      'only' => ['comment-like']
 *  ]
 * @package common\behavior
 */
class AccessBehavior extends ActionFilter
{

    //处理post请求
    public function beforeAction($action)
    {
        //判断请求方式
        if(!Yii::$app->request->post()){
            OutputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SYSTEM_NOT_POST);
        }
        //加密解析
        $data = Yii::$app->request->post();
        if(!$data){
            OutputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        $sign = '';
        $string = '';
        foreach ($data as $key=>$param){
            if($key =='sign'){
                $sign = $param;
                unset($data[$key]);
            }
            $string .= $param.".";
        }
        $string = trim($string,'.');
        if($sign != md5($string)){
            OutputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        return true;
    }
}