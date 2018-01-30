<?php
namespace api\modules\redpacket\models;

use Yii;

class RedPacketRecord extends \common\models\RedPacketRecord
{
    /**
     * 检查红包code和address是否存在
     * @param $code
     * @param $address
     */
    public static function checkCodeAndAddress($address, $code)
    {
        $info = RedPacketRecord::find()->where(['code' => $code, 'to_address' => $address])->one();
        var_dump($info);die;
    }
}
