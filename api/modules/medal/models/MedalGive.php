<?php
namespace api\modules\medal\models;

use Yii;

class MedalGive extends \common\models\MedalGive
{

    //创建数据
    public static function insertData($address, $medal_id, $recipient_address, $status)
    {
        $model = new self();
        $model->medal_id = $medal_id;
        $model->owner_address = $address;
        $model->recipient_address = $recipient_address;
        $model->status = $status;
        $model->addtime = time();
        return $model->save();
    }

}
