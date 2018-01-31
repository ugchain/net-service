<?php
namespace api\modules\redpacket\models;

use Yii;

class RedPacketRecord extends \common\models\RedPacketRecord
{
    /**
     * @var intger
     * 红包领取记录已经领取状态
     */
    const REDPACKET_RECORD_STATUS_TORECEIVE = 1;

    /**
     * @var intger
     * 红包领取记录兑换中状态
     */
    const REDPACKET_RECORD_STATUS_REDEMPTION = 2;

    /**
     * @var intger
     * 红包领取记录兑换失败状态
     */
    const REDPACKET_RECORD_STATUS_EXCHANGEFAILED = 3;

    /**
     * @var intger
     * 红包领取记录兑换成功状态
     */
    const REDPACKET_RECORD_STATUS_EXCHANGESUCCESS = 4;

    /**
     * @var intger
     * 红包领取记录过期状态
     */
    const REDPACKET_RECORD_STATUS_EXPIRED = 5;

    /**
     * 检查红包code和address是否存在
     * @param $code
     * @param $address
     */
    public static function checkCodeAndAddress($address, $code)
    {
        //查询红包记录
        $info = RedPacketRecord::find()->where(['code' => $code, 'to_address' => $address])->one()->attributes;
        //查询是否已经兑换
        if ($info['status'] == self::RECEIVED) {
            return $info;
        }

       return false;
    }

    public static function updateStatusAndTxidByid($id, $status, $txid)
    {
        return RedPacketRecord::updateAll(["status" => $status, 'txid' => $txid], ['id' => $id]);
    }

}
