<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $app_txid
 * @property string $chain_txid
 * @property string $address
 * @property string $amount
 * @property string $from_block
 * @property string $to_block
 * @property string $gas_price
 * @property string $gas_used
 * @property string $owner_txid
 * @property integer $addtime
 * @property integer $type
 * @property integer $status
 * @property integer $block_succ_time
 * @property integer $block_fall_time
 * @property integer $block_send_succ_time
 * @property integer $block_send_fall_time
 * @property integer $block_listen_succ_time
 * @property integer $block_listen_fall_time
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
    const ETH_UG = 1;
    const UG_ETH = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'center_bridge';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','status',"addtime","block_succ_time","block_fall_time","block_send_succ_time","block_send_fall_time","block_listen_succ_time","block_listen_fall_time"], 'integer'],
            [['address','amount','addtime'], 'required'],
            [['app_txid','chain_txid','address','amount','from_block','to_block','gas_price','gas_used','owner_txid'], 'string'],
        ];
    }

    /**
     * 查询各个状态数据
     * @param string $app_txid
     * @param string $type
     * @param string $status
     * @return array
     */
    public static function getListByTypeAndStatus($type="1", $status = self::CONFIRMED)
    {
        return CenterBridge::find()
            ->where(["type" => $type,"status" => $status,'from_block' => '0'])
            ->asArray()->all();
    }

    /**
     * 更新blocknumber and OwnerTxid
     * @param $block_number
     * @param $gas_price
     * @return bool
     */
    public static function updateBlockAndOwnerTxid($app_txid, $block_number, $owner_txid)
    {
        return CenterBridge::updateAll(["from_block" => $block_number, "owner_txid" => $owner_txid],["app_txid" => $app_txid]);
    }

    /**
     * 更新blocknumber and OwnerTxid
     * @param $block_number
     * @param $gas_price
     * @return bool
     */
    public static function updateBlockAndGasPrice($app_txid, $block_number, $gas_price)
    {
        return CenterBridge::updateAll(["from_block" => $block_number],["app_txid" => $app_txid]);
    }

    public static function getListByTypeAndStatusAndBlockNumber($type = "1", $status = self::CONFIRMED)
    {
        return CenterBridge::find()
            ->where(["type"=>$type,"status"=>$status])
            ->andWhere(['not', ['from_block' => '0']])
            ->asArray()->all();
    }

    /**
     * 更新通知对方链成功
     * @param $app_txid
     * @param $gas_used
     * @param string $status
     * @return int
     */
    public static function updateGasUsedAndStatusAndTime($app_txid,$gas_used,$status="0",$owner_txid,$to_block)
    {
        return CenterBridge::updateAll(["gas_used"=>$gas_used,"status"=>$status,"block_send_succ_time"=>time(),"owner_txid"=>$owner_txid,"to_block"=>$to_block],["app_txid"=>$app_txid]);
    }

    public static function getListByTypeAndStatusAndOwnerTxid()
    {
        return CenterBridge::find()
            ->where(["type"=>"2","status"=>"3"])
            ->andWhere(['not', ['owner_txid' => '']])
            ->asArray()->all();
    }

    public static function updateBlockByTxid($owner_txid,$to_block)
    {
        return CenterBridge::updateAll(["to_block"=>$to_block],["owner_txid"=>$owner_txid]);
    }

    /**
     * 当to_block为空时,大于安全块直接成功
     * @param $owner_txid
     * @param $to_block
     * @return int
     */
    public static function updateBlockAndTimeByTxid($owner_txid,$to_block)
    {
        return CenterBridge::updateAll(["to_block"=>$to_block,"status"=>self::LISTEN_CONFIRM_SUCCESS,"block_listen_succ_time"=>time()],["owner_txid"=>$owner_txid]);
    }

    /**
     * 最终执行成功
     * @param $owner_txid
     * @return int
     */
    public static function updateListenSuccTimeByTxid($owner_txid)
    {
        return CenterBridge::updateAll(["status"=>self::LISTEN_CONFIRM_SUCCESS,"block_listen_succ_time"=>time()],["owner_txid"=>$owner_txid]);
    }
}
