<?php
namespace api\modules\user\models;

use Yii;

/**
 * This is the model class for table "ug_advertise".
 *
 * @property integer $id
 * @property string $address
 * @property string $phone
 * @property integer $addtime
 */

class Advertise extends \common\models\Advertise
{
    /**
     * 保存广告位申请
     */
    public static function saveAdvertise($address,$phone)
    {
        $model = new self();
        $model->address = $address;
        $model->phone   = $phone;
        $model->addtime = time();
        return $model->save();
    }

    /**
     * 判断是否申请过
     */
    public static function getAdvertiseInfoByAddressAndPhone($address,$phone)
    {
        return Advertise::find()->where(["address"=>$address,"phone"=>$phone])->asArray()->one();
    }

    /**
     * 判断是否申请过
     */
    public static function getAdvertiseInfoByAddress($address)
    {
        return Advertise::find()->where(["address"=>$address])->asArray()->one();
    }
}
