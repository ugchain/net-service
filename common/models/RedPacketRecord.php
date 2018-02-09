<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class RedPacketRecord extends ActiveRecord
{
    const RECEIVED = 1;//已领取
    const REDEMPTION = 2;//兑换中
    const EXCHANGE_FAIL = 3;//兑换失败
    const EXCHANGE_SUCC = 4;//兑换成功
    const EXPIRED = 5;//已过期

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_red_packet_record';
    }

}
