<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class MedalGive extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_medal_give';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
//    public function rules()
//    {
//        return [
//            [['theme_id','material_type', 'addtime'], 'integer'],
//            [['theme_thumb_img','theme_img',"token_id",'address','medal_name','addtime'], 'required'],
//            [['token_id','theme_img','theme_thumb_img','medal_name','theme_name','amount','address'], 'string'],
//        ];
//    }

}
