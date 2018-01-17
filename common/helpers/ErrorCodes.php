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
        ADDRESS_NOT_EXIST                                           =   20103,//地址缺失
        NICKNAM_EOVERSIZE                                           =   20104,//昵称过长
        ADDRESS_EXIST                                               =   20105,//地址存在
        TXID_EXIST                                                  =   20106,//交易ID已存在
        MEDAL_INFO_ERROR                                            =   20107;//勋章信息错误


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
        self::ADDRESS_NOT_EXIST                                     =>'地址无效',
        self::ADDRESS_EXIST                                         =>'地址存在',
        self::TXID_EXIST                                            =>'交易ID已存在',
        self::MEDAL_INFO_ERROR                                      =>'勋章信息错误',
    ];

}