<?php
namespace api\modules\medal\models;

use Yii;

class MedalGive extends \common\models\Medal
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

}
