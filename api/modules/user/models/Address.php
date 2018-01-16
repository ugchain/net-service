<?php
namespace api\modules\user\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $nickname
 * @property string $address
 * @property integer $is_del
 * @property integer $addtime
 */

class Address extends \common\models\Address
{
    /**
     * 保存地址
     */
    public static function saveAddress($data)
    {
        $model = new self();
        $model->nickname = $data['nickname'];
        $model->address = $data['address'];
        $model->is_del = 0;
        $model->addtime = time();
        return $model->save();
    }

    /**
     * 查找地址是否存在
     */
    public static function getInfoByAddress($address)
    {
        return Address::find()->select("address,is_del")->where(["address"=>$address])->asArray()->one();
    }

    /**
     * 更新is_del 为正常状态
     */
    public static function updateAddressByIsDel($address)
    {
        return Address::updateAll(["is_del" => 0],['address'=>$address]);
    }
}
