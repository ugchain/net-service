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
        PARAM_NOT_EXIST                                             =   20101,//

        SIGN_NOT_TRUE                                               =   20102,//签名错误
        ADDRESS_NOT_EXIST                                           =   20103,//地址缺失
        NICKNAM_EOVERSIZE                                           =   20104,//昵称过长
        ADDRESS_EXIST                                               =   20105,//地址存在
        TXID_EXIST                                                  =   20106,//交易ID已存在
        UPLOAD_FILE_FALL                                            =   20107,//上传图片失败
        MEDAL_INFO_ERROR                                            =   20107,//勋章信息错误
        MEDAL_UPDATE_ERROR                                          =   20108,//勋章地址更新错误
        MEDAL_GIVE_ADD_FAILED                                       =   20109,//转增记录添加失败
        PHONE_WRONGFOL                                              =   20110,//手机号不合法
        ADVERTISE_EXIST                                             =   20111,//广告已申请
        RED_PACKET_REDEMPTION                                       =   20112,//红包兑换中
        TRANSACTION_FAIL                                            =   20113,//交易失败
        RED_PACKET_NOT_EXIST                                        =   20114,//红包不存在
        RED_PACKET_EXPIRED                                          =   20115,//红包已过期
        RED_PACKET_EXIST                                            =   20116,//红包以领取
        RED_PACKET_GRAD_FAIL                                        =   20117,//红包领取失败
        RED_PACKET_LED_LIGHT                                        =   20118,//红包以领光
        ROSE_THEME_NOT_EXISTS                                       =   20119,//玫瑰主题未获取到
        RED_PACKET_QUANTITY_EXCEEDED                                =   20120,//红包数量超出限制
        RED_PACKET_TITLE_EXCEEDED                                   =   20121,//红包名称超出限制
        RED_PACKET_OPEN                                             =   20122,//您已经拆过这个红包啦
        RED_PACKET_SEND_CHAIN_FALL                                  =   20122;//上链失败




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
        self::UPLOAD_FILE_FALL                                      =>'上传图片失败',
        self::MEDAL_INFO_ERROR                                      =>'勋章信息错误',
        self::MEDAL_UPDATE_ERROR                                    =>'勋章地址更新错误',
        self::MEDAL_GIVE_ADD_FAILED                                 =>'转增记录添加失败',
        self::PHONE_WRONGFOL                                        =>'手机号不合法',
        self::ADVERTISE_EXIST                                       =>'广告已申请',
        self::RED_PACKET_REDEMPTION                                 =>'兑换码无效',
        self::TRANSACTION_FAIL                                      =>'交易失败',
        self::RED_PACKET_NOT_EXIST                                  =>'没有这个红包哦',
        self::RED_PACKET_EXPIRED                                    =>'红包已过期',
        self::RED_PACKET_EXIST                                      =>'红包以领取',
        self::RED_PACKET_GRAD_FAIL                                  =>'红包领取失败',
        self::RED_PACKET_LED_LIGHT                                  =>'红包以领光',
        self::ROSE_THEME_NOT_EXISTS                                 =>'主题缺失',
        self::RED_PACKET_QUANTITY_EXCEEDED                          =>'红包数量超出限制',
        self::RED_PACKET_TITLE_EXCEEDED                             =>'红包名称超出限制',
        self::RED_PACKET_OPEN                                       =>'您已经拆过这个红包啦',
        self::RED_PACKET_SEND_CHAIN_FALL                            =>'发送交易上链失败',
    ];

}