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

class CenterBridge extends ActiveRecord
{

    const CONFIRMED = 0;
    const SUCCESS_BLOCK = 1;
    const FAILED_BLOCK = 2;
    const SEND_SUCCESS = 3;
    const SEND_FAILED = 4;
    const LISTEN_CONFIRM_SUCCESS = 5;
    const LISTEN_CONFIRM_FAILED = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'center_bridge';
    }

//    /**
//     * 参数规则
//     * @inheritdoc
//     */
//    public function rules()
//    {
//        return [
//            [['is_del', 'addtime'], 'integer'],
//            [['address'], 'required'],
//            [['address','nickname'], 'string'],
//        ];
//    }
}
