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
