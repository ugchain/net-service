<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "medal_give".
 *
 * @property integer $medal_id
 * @property string $owner_address
 * @property string $recipient_address
 * @property integer $status
 * @property integer $addtime
 */

class MedalGive extends ActiveRecord
{
    const TURN_INCREASE = 0;
    const SUCCESS = 1;
    const FAILED = 2;

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
    public function rules()
    {
        return [
            [['medal_id','addtime',"status"], 'integer'],
            [['medal_id','from_address','addtime'], 'required'],
            [['from_address','to_address'], 'string'],
        ];
    }

}
