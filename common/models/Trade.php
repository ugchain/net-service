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

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'addtime','trade_time'], 'integer'],
            [['from_address','to_address','amount','addtime'], 'required'],
            [['app_txid','ug_txid','from_address','to_address','amount','blocknumber'], 'string'],
        ];
    }
}
