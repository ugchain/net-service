<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_red_packet".
 *
 * @property integer $id
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
    const CREATE_REDPACKET = 0;//创建红包
    const CREATE_REDPACKET_FAIL = 1;//创建红包失败
    const CREATE_REDPACKET_SUCC = 2;//创建红包成功
    const REDPACKET_FINISHED = 3;//红包已领完
    const REDPACKET_EXPIRED = 4;//红包已过期

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
            [['id',"theme_id", 'status',"addtime","fail_time","create_succ_time","expire_time"], 'integer'],
            [["title","address","amount",'addtime'], 'required'],
            [['title','amount',"quantity","txid","theme_img","theme_thumb_img","theme_share_img","back_amount"], 'string'],
        ];
    }

    /**
     * 获取一条红包记录
     */
    public static function getPacketInfoById($id)
    {
        return RedPacket::find()->where(["id"=>$id])->asArray()->one();
    }
}