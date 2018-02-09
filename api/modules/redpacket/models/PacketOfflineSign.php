<?php
namespace api\modules\redpacket\models;

use Yii;

/**
 * This is the model class for table "ug_packet_offline_signature".
 *
 * @property integer $packet_id
 * @property string $address
 * @property string $raw_transaction
 * @property integer $type
 * @property integer $addtime
 */
class PacketOfflineSign extends \common\models\PacketOfflineSign
{

    /**
     * 添加签名数据日志
     */
    public static function saveOfflineSign($data)
    {
        $model = new self();
        $model->packet_id = $data["packet_id"];
        $model->address = $data["address"];
        $model->raw_transaction = $data["raw_transaction"];
        $model->type = $data["type"];
        $model->addtime = time();
        return $model->save();

    }
}
