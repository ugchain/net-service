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
    const INTERNAL = 0;  //内部转账交易
    const CREATE_REDPACKET = 1;  //创建红包交易转账
    const OPEN_REDPACKET = 2;  //拆红包交易转账
    const BACK_REDPACKET = 3;  //退还红包交易转账

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
//    public function rules()
//    {
//        return [
//            [['type', 'status', 'addtime', 'trade_time'], 'integer'],
//            [['from_address', 'to_address', 'amount', 'addtime'], 'required'],
//            [['app_txid', 'ug_txid', 'from_address', 'to_address', 'amount', 'blocknumber'], 'string'],
//        ];
//    }

    /**
     * 获取交易详细信息
     * @param int $status
     * @return array|ActiveRecord[]
     */
    public static function getInfoByStatus($status = self::CONFIRMED, $type = 0)
    {
        return Trade::find()
            ->where(["status" => $status, 'blocknumber' => '0', 'type' => $type])
            ->asArray()->all();
    }

    /**
     * 根据txid修改blocknumber & status & time
     * @param $txid
     * @param $blockNumber
     * @param $status
     */
    public static function updateBlockAndStatusBytxid($txid, $blockNumber, $status)
    {
        return Trade::updateAll(["blocknumber" => $blockNumber, "status" => $status, "trade_time" => time()], ["app_txid" => $txid]);
    }

}
