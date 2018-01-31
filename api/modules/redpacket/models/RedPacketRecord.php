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
        $recordInfo = RedPacketRecord::find()->where(['code' => $code, 'to_address' => $address])->one()->attributes;
        if (!$recordInfo) {
            return false;
        }
        //查询是否为领取状态
        if ($recordInfo['status'] == self::RECEIVED) {
            return $recordInfo;
        }

       return false;
    }

    /**
     * @param $id
     * @param $status
     * @param $txid
     * @param $amount
     *
     * @return int
     */
    public static function updateStatusAndTxidAndAmountByid($id, $status, $txid, $amount)
    {
        return RedPacketRecord::updateAll(["status" => $status, "txid" => $txid, "amount" => $amount], ["id" => $id]);
    }

}
