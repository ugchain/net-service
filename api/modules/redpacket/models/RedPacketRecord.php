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
        //查询红包记录是否存在
        $info = RedPacketRecord::find()->where(['code' => $code, 'to_address' => $address, 'status' => 1])->one()->attributes;
        //查询是否已经兑换

        //查询红包是否已过期

        var_dump($info);die;
    }
}
