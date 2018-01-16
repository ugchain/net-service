<?php
namespace common\helpers;

/**
 * Class ErrorCodes
 * @package common\components
 * 错误码公共类
 */
class ErrorCodes
{
    CONST
        SUCCESS                                                     =   0,
        FALL                                                        =   1,

        SYSTEM_NOT_POST                                             =   10001,//当前请求方式有误

        //
        PARAM_NOT_EXIST                                             =   20101,//参数缺失
        SIGN_NOT_TRUE                                               =   20102,//签名错误
        COIN_NOT_EIST                                               =   20103,//币名不能为空
        GET_USERID_ERROR                                            =   20104;//获取用户id失败


    /**
     * 错误提示信息
     * @return array
     */
    public static $ERR_MSG = [

        self::SUCCESS                                               =>'成功',
        self::FALL                                                  =>'失败',
        self::SYSTEM_NOT_POST                                       =>'当前请求方式有误',
        self::PARAM_NOT_EXIST                                       =>'参数缺失',
        self::SIGN_NOT_TRUE                                         =>'签名错误',
        self::COIN_NOT_EIST                                         =>'币名不能为空',
        self::GET_USERID_ERROR                                      =>'获取用户id失败',

    ];

}