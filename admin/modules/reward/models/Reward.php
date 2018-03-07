<?php
namespace admin\modules\reward\models;

use Yii;
use yii\db\ActiveRecord;


class Reward extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_reward';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
//    public function rules()
//    {
//        return [
//            [['type', 'status', 'addtime', 'trade_time'], 'integer'],
//            [['from_address', 'to_address'], 'required'],
//            [['app_txid', 'from_address', 'to_address', 'amount', 'blocknumber'], 'string'],
//        ];
//    }
    /**
     * 创建奖励
     */
    public static function saveReward($data)
    {
        foreach ($data as $k => $v){
            $newdata[$k] = [$v];
        }
        $key=['to_address'];
        $res= \Yii::$app->db->createCommand()->batchInsert(Reward::tableName(), $key, $newdata)->execute();//执行批量添加
        return $res;

    }


}
