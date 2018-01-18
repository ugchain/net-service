<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Trade extends ActiveRecord
{
    const CONFIRMED = 0;
    const SUCCESS = 1;
    const FAILED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_trade';
    }

}
