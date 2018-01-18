<?php
namespace api\modules\user\models;

use Yii;

class Trade extends \common\models\CenterBridge
{
    //创建数据
    public static function insertData($txid, $from, $to, $amount, $status, $time)
    {
        $model = new self();
        $model->app_txid = $txid;
        $model->from_address = $from;
        $model->to_address = $to;
        $model->amount = $amount;
        $model->status = $status;
        $model->addtime = $time;
        return $model->save();
    }

}
