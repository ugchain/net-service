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

    /**
     * 查询各个状态数据
     * @param string $app_txid
     * @param string $type
     * @param string $status
     * @return array
     */
    public static function getListByTypeAndStatus($type="1",$status="0")
    {
        return CenterBridge::find()
            ->where(["type"=>$type,"status"=>$status])
            ->asArray()->all();
    }

    /**
     * 更新blocknumber and gas_price
     * @param $block_number
     * @param $gas_price
     * @return bool
     */
    public static function updateBlockAndGasPrice($block_number,$gas_price)
    {
        $model = new self();
        $model->blocknumber = $block_number;
        $model->gas_price = $gas_price;
        return $model->save();
    }

    public static function getListByTypeAndStatusAndBlockNumber($type="1", $status="0")
    {
        return CenterBridge::find()
            ->where(["type"=>$type,"status"=>$status])
            ->andWhere(['not', ['blocknumber' => '0']])
            ->asArray()->all();
    }

    /**
     * 更新通知对方链成功
     * @param $app_txid
     * @param $gas_used
     * @param string $status
     * @return int
     */
    public static function updateGasUsedAndStatusAndTime($app_txid,$gas_used,$status="0")
    {
        return CenterBridge::updateAll(["gas_used"=>$gas_used,"status"=>$status,"block_send_succ_time"=>time()],["app_txid"=>$app_txid]);
    }
}
