<?php
namespace api\modules\user\models;

use Yii;

class CenterBridge extends \common\models\CenterBridge
{
    //创建数据
    public static function insertData($txid, $address, $type, $amount, $gasPrice, $status, $time)
    {
        $model = new self();
        $model->app_txid = $txid;
        $model->address = $address;
        $model->amount = $amount;
        $model->gas_price = (string)$gasPrice;
        $model->type = $type;
        $model->status = $status;
        $model->addtime = $time;
        return $model->save();
    }

    //查询txid是否存在
    public static function getTxidInfo($txid)
    {
        return CenterBridge::find()->select("*")->where(["app_txid" => $txid])->asArray()->one();
    }

    //返回划转记录
    public static function getList($address, $page, $pageSize)
    {
        $query = CenterBridge::find();
        $query->where(["address" => $address]);
        $query->orderBy('addtime DESC');
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
        return ['list' => $index_list, 'is_next_page' => $is_next_page,"count"=>$count];
    }


}
