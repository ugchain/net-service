<?php
namespace admin\modules\reward\models;

use Yii;
use yii\db\ActiveRecord;


class Reward extends ActiveRecord
{
    const SENDUGC = 0;
    const REWARD_SUCCESS = 1;//状态成功
    const REWARD_FAIL = 2;//状态失败

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

    /**
     * 获取to
     */
    public static function getTo()
    {
        return Reward::find()->select("to_address")->where("app_txid=''")->asArray()->all();
    }

    //创建数据
    public static function updateData($txid, $from, $to, $amount, $status, $type = self::SENDUGC)
    {
        $time = time();
        return Reward::updateAll(["app_txid"=>$txid,'from_address'=>$from,'amount'=>$amount,'status'=>$status,'type'=>$type,'addtime'=>$time],["to_address"=>$to,'app_txid'=>'']);
    }
}
