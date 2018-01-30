<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_packet_offline_signature".
 *
 * @property integer $packet_id
 * @property string $address
 * @property string $raw_transaction
 * @property integer $type
 * @property integer $addtime
 */
class PacketOfflineSign extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_packet_offline_signature';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['packet_id',"type", 'addtime'], 'integer'],
            [['addtime'], 'required'],
            [['address','raw_transaction'], 'string'],
        ];
    }

}
