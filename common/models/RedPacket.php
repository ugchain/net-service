<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_red_packet".
 *
 * @property integer $packet_id
 * @property string $title
 * @property string $address
 * @property string $amount
 * @property string $quantity
 * @property integer $theme_id
 * @property string $txid
 * @property string $theme_img
 * @property string $theme_thumb_img
 * @property string $theme_share_img
 * @property integer $type
 * @property string $back_amount
 * @property integer $status
 * @property integer $addtime
 * @property integer $fail_time
 * @property integer $create_succ_time
 * @property integer $expire_time
 */
class RedPacket extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_red_packet';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['packet_id',"theme_id", 'status',"addtime","fail_time","create_succ_time","expire_time"], 'integer'],
            [["title","address","amount",'addtime'], 'required'],
            [['title','amount',"quantity","txid","theme_img","theme_thumb_img","theme_share_img","back_amount"], 'string'],
        ];
    }

}