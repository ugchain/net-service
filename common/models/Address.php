<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $nickname
 * @property string $address
 * @property integer $is_del
 * @property integer $addtime
 */

class Address extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_del', 'addtime'], 'integer'],
            [['address'], 'required'],
            [['address','nickname'], 'string'],
        ];
    }
}
