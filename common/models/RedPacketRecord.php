<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class RedPacketRecord extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_red_packet_record';
    }

}
