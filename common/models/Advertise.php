<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_advertise".
 *
 * @property integer $id
 * @property string $address
 * @property string $phone
 * @property integer $addtime
 */

class Advertise extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_advertise';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addtime'], 'integer'],
            [['address','phone','addtime'], 'required'],
            [['address','phone'], 'string'],
        ];
    }
}
