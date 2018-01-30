<?php
namespace api\modules\redpacket\models;

use Yii;
use api\modules\redpacket\models\RedPacketRecord;

class RedPacket extends \common\models\RedPacket
{
    public static function getRedPacketInfo($id) {
        $result = self::findOne($id);

        $redpacketInfo = [
            'txid' => $result->txid,
            'title' => $result->title,
            'status' => $result->status,
            'quantity' => $result->quantity,
            'already_received_quantity' => count($result->redPacketRecords),
            'amount' => $result->amount,
            'already_received_amount' => 'TODO'
        ];
        $redPacketRecordList = [];
        $redPacketRecords = $result->redPacketRecords;
        foreach ($redPacketRecords as $redPacketRecord) {
            $redPacketRecordList[] = [
                'wx_name' => $redPacketRecord->wx_name,
                'wx_avatar' => $redPacketRecord->wx_avatar,
                'amount' => $redPacketRecord->amount
            ];
        }
        $redpacketInfo['redPacketRecordList'] = $redPacketRecordList;
        echo "<pre>";var_dump($redpacketInfo);exit;
    }

    public function getRedPacketRecords() {
        return $this->hasMany(RedPacketRecord::className(), ['rid' => 'id']);
    }
}
