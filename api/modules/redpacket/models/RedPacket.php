<?php
namespace api\modules\redpacket\models;

use Yii;

class RedPacket extends \common\models\RedPacket
{

    /**
     * 创建红包
     */
    public static function saveRedPacket($data)
    {
        $model = new self();
        $model->title = $data["title"];
        $model->address = $data["address"];
        $model->amount = $data["amount"];
        $model->quantity = $data["quantity"];
        $model->theme_id = $data["theme_id"];
        $model->theme_img = $data["theme_img"];
        $model->theme_thumb_img = $data["theme_thumb_img"];
        $model->theme_share_img = $data["theme_share_img"];
        $model->txid = $data["hash"];
        $model->type = $data["type"];
        $model->status = 0;
        $model->addtime = time();
        $model->save();
        return $model->attributes['id'];
    }
}
