<?php
namespace api\modules\user\models;

use Yii;

class CenterBridge extends \common\models\CenterBridge
{
    //创建数据
    public static function insertData($txid, $address, $type, $status, $time)
    {
        $model = new self();
        $model->app_txid = $txid;
        $model->address = $address;
        $model->type = $type;
        $model->status = $status;
        $model->addtime = $time;
        return $model->save();
    }

    //查询txid是否存在
    public static function getTxidInfo($txid)
    {
        return CenterBridge::find()->select("id, status")->where(["app_txid" => $txid])->asArray()->one();
    }

    //返回划转记录
    public static function getList($address)
    {
        return CenterBridge::find()->select("*")->where(["address" => $address])->asArray()->all();
    }


}
