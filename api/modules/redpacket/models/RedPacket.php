<?php
namespace api\modules\redpacket\models;

use Yii;

class RedPacket extends \common\models\RedPacket
{
    const I_RECEIVED = 0;
    const I_SENT = 1;

    /**
     * 获取红包列表（我收到、我发出）
     * @param $address
     * @param $type
     * @param $page
     * @param $pageSize
     *
     * @return array
     */
    public static function getRedList($address, $type, $page, $pageSize)
    {
        $query = Yii::$app->db;
        $offset = ($page - 1) * $pageSize;
        if ($type == self::I_RECEIVED) {
            $sql = "SELECT `rr`.exchange_time, `rr`.rid, `rr`.amount, `rp`.theme_id, `rp`.title, `rp`.id FROM `ug_red_packet_record` as rr LEFT JOIN `ug_red_packet` as rp on rr.rid = rp.id 
                  where rr.to_address = '" . $address . "' and rr.status = '" . RedPacketRecord::EXCHANGE_SUCC . "' order by id desc limit " . $pageSize . " offset " . $offset;
        } else {
            $sql = "SELECT * FROM `ug_red_packet` where address = '" . $address . "' order by id desc limit " . $pageSize . " offset " . $offset;
        }
        $commond = $query->createCommand($sql);
        $list = $commond->queryAll();
        $count = count($list);
        //默认无下一页
        $is_next_page = "0";
        if ($count - ($page * $pageSize) >= 0) {
            $is_next_page = "1";//有下一页
        }

        $sum = 0;
       foreach ($list as $k => $v) {
           //获取已领人数
           $receive_count = RedPacketRecord::find()->where(['rid' => $v['id']])->count();
           $sum += $v['amount'];
           $list[$k]['receive'] = $receive_count;
       }

        return ['list' => $list, 'is_next_page' => $is_next_page, "count"=> $count, "page" => $page, "pageSize" => $pageSize, "received_amount" => $sum];
    }

    /**
     * 创建红包
     */
    public static function saveRedPacket($data)
    {
        $model = new self();
        $model->title = $data["title"];
        $model->address = $data["address"];
        $model->amount = $data["amount"];
        $model->quantity = $data["quantity"];
        $model->theme_id = $data["theme_id"];
        $model->theme_img = $data["theme_img"];
        $model->theme_thumb_img = $data["theme_thumb_img"];
        $model->theme_share_img = $data["theme_share_img"];
        $model->txid = $data["hash"];
        $model->type = $data["type"];
        $model->status = 0;
        $model->addtime = time();
        $model->save();
        return $model->attributes['id'];
    }

    /**
     * 更新红包记录状态值
     */
    public static function updateStatus($id,$status = "1")
    {
        $updateData = ["status"=>$status];
        switch ($id)
        {
            case 1:
                $updateData["fail_time"] = time();
                break;
            case 2:
                $updateData["create_succ_time"] = time();
                break;
            case 3:
                $updateData["finish_time"] = time();
                break;
            case 4:
                $updateData["expire_time"] = time();
                break;
            default:
                break;
        }
        return RedPacket::updateAll($updateData,["id"=>$id]);
    }
}
