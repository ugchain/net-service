<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "medal_give".
 *
 * @property integer $rose_id
 * @property string $owner_address
 * @property string $recipient_address
 * @property integer $status
 * @property integer $addtime
 */

class RoseGive extends ActiveRecord
{
    const TURN_INCREASE = 0;
    const SUCCESS = 1;
    const FAILED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_rose_give';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rose_id','addtime',"status"], 'integer'],
            [['rose_id','from_address','addtime'], 'required'],
            [['from_address','to_address'], 'string'],
        ];
    }

}
