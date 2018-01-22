<?php
namespace api\modules\user\models;

use Yii;

class Trade extends \common\models\Trade
{
    //创建数据
    public static function insertData($txid, $from, $to, $amount, $status,$block_number ="0")
    {
        $time = time();
        $model = new self();
        $model->app_txid = $txid;
        $model->from_address = $from;
        $model->to_address = $to;
        $model->amount = $amount;
        $model->blocknumber = $block_number;
        $model->status = $status;
        $model->addtime = $time;
        $model->trade_time = $time;
        return $model->save();
    }

    /**
     * 查询交易记录
     */
    public static function getRecordByAddress($address,$page="1",$pageSize="10")
    {
        $query = Trade::find();
        $query->where(['or' , ['=' , 'from_address' , $address] , ['=' , 'to_address' , $address]]);
        $query->orderBy("id DESC");
        //分页
        $count = $query->count();
        $offset = ($page - 1) * $pageSize;
        $query->offset($offset)->limit($pageSize);
        $index_list = $query->asArray()->all();
        //默认无下一页
        $is_next_page = "0";
        if ($count - ($page * $pageSize) >= 0) {
            $is_next_page = "1";//有下一页
        }
        return ['list' => $index_list, 'is_next_page' => $is_next_page,"count"=>$count,"page"=>$page,"pageSize"=>$pageSize];
    }

    //查询txid是否存在
    public static function getTxidInfo($txid)
    {
        return Trade::find()->select("*")->where(["app_txid" => $txid])->asArray()->one();
    }

}
