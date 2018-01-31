<?php
namespace api\modules\redpacket\models;

use Yii;

class RedPacketRecord extends \common\models\RedPacketRecord
{
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
