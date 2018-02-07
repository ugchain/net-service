<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "address".
 *
 * @property integer $theme_id
 * @property string $token_id
 * @property string $theme_img
 * @property string $theme_thumb_img
 * @property string $theme_share_img
 * @property string $rose_name
 * @property string $theme_name
 * @property integer $material_type
 * @property string $amount
 * @property string $address
 * @property integer $status
 * @property integer $addtime
 */


class Rose extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_rose';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id','material_type', 'addtime'], 'integer'],
            [['theme_thumb_img','theme_img',"token_id",'address','rose_name','addtime'], 'required'],
            [['token_id','theme_img','theme_thumb_img','rose_name','theme_name','amount','theme_share_img','address'], 'string'],
        ];
    }

}
