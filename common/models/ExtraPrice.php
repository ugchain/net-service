<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "extra_price".
 *
 * @property integer $id
 * @property string $gas_min_price
 * @property string $gas_max_price
 * @property string $ug_extra_price
 * @property integer $addtime
 */

class ExtraPrice extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_extra_prcie';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addtime'], 'integer'],
            [['addtime'], 'required'],
            [['gas_min_price','gas_max_price','ug_extra_price'], 'string'],
        ];
    }

    public static function getList()
    {
        return ExtraPrice::find()->asArray()->one();
    }
}
