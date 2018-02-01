<?php
namespace api\modules\redpacket\models;

use Yii;
use api\modules\redpacket\models\RedPacketRecord;
use api\modules\redpacket\models\RedPacketTheme;

class RedPacket extends \common\models\RedPacket
{
    const I_RECEIVED = 0;
    const I_SENT = 1;

    /**
     * 根据红包ID获取此红包详情与此红包领取记录
     *
     * @param $id 红包ID
     * @return array
     */
    public static function getRedPacketInfoWithRecordList($id)
    {
        $result = self::findOne($id);

        //红包详情
        $redpacketInfo = [
            'id' => $result->id,
            'txid' => $result->txid,
            'title' => $result->title,
            'status' => $result->status,
            'quantity' => $result->quantity,
            'already_received_quantity' => count($result->redPacketRecords),
            'amount' => $result->amount,
            'back_amount' => $result->back_amount,
            'already_received_amount' => 'TODO',
            'finish_time' => !empty($result->finish_time) ? date('m-d h:i', $result->finish_time) : '',
            'expire_time' => !empty($result->expire_time) ? date('m-d h:i', $result->expire_time) : '',
            'last_time' => date('H时i分', $result->expire_time - time()),
            'current_time' => date('m-d h:i', time())
        ];

        //初始化此红包领取记录列表
        $redPacketRecordList = [];
        //初始化兑换成功的金额
        $alreadyReceivedAmount = 0;
        //获取数据库红包领取记录资源，循环获得需要的每个红包记录数据
        $redPacketRecords = $result->redPacketRecords;
        foreach ($redPacketRecords as $redPacketRecord) {
            //根据状态动态获取微信用户红包时间，领取状态为已领取、兑换中、兑换失败、已过期则显示领取时间，领取状态为兑换成功则显示兑换时间
            //如果状态为兑换成功，则统计兑换总UGC
            switch ($redPacketRecord->status) {
                case RedPacketRecord::REDPACKET_RECORD_STATUS_TORECEIVE:
                case RedPacketRecord::REDPACKET_RECORD_STATUS_REDEMPTION:
                case RedPacketRecord::REDPACKET_RECORD_STATUS_EXCHANGEFAILED:
                case RedPacketRecord::REDPACKET_RECORD_STATUS_EXPIRED:
                    $time = $redPacketRecord->addtime;
                    break;
                case RedPacketRecord::REDPACKET_RECORD_STATUS_EXCHANGESUCCESS:
                    $time = $redPacketRecord->exchange_time;
                    $alreadyReceivedAmount += $redPacketRecord->amount;
                    break;
            }
            $redPacketRecordList[] = [
                'wx_name' => $redPacketRecord->wx_name,
                'wx_avatar' => $redPacketRecord->wx_avatar,
                'amount' => $redPacketRecord->amount,
                'status' => $redPacketRecord->status,
                'time' => !empty($time) ? date('m-d s:i', $time) : ''
            ];
        }

        //获取数据库红包主题资源
        $redPacketTheme = $result->redPacketTheme;

        //拼装回返
        $redpacketInfo['already_received_amount'] = $alreadyReceivedAmount;
        $redpacketInfo['theme_img'] = !empty($redPacketTheme->img) ? $redPacketTheme->img : '';
        $redpacketInfo['theme_thumb_img'] = !empty($redPacketTheme->thumb_img) ? $redPacketTheme->thumb_img : '';
        $redpacketInfo['theme_share_img'] = !empty($redPacketTheme->share_img) ? $redPacketTheme->share_img : '';
        $redpacketInfo['redPacketRecordList'] = $redPacketRecordList;
        $redpacketInfo['image_url'] = Yii::$app->params['image_url'];

        return $redpacketInfo;
    }

    public function getRedPacketRecords()
    {
        return $this->hasMany(RedPacketRecord::className(), ['rid' => 'id']);
    }

    public function getRedPacketTheme()
    {
        return $this->hasOne(RedPacketTheme::className(), ['id' => 'theme_id']);
    }


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
            $sql = "SELECT `rr`.status, `rr`.exchange_time, `rr`.expire_time, `rr`.rid, `rr`.amount, `rp`.theme_id, `rp`.title, `rp`.id FROM `ug_red_packet_record` as rr LEFT JOIN `ug_red_packet` as rp on rr.rid = rp.id 
                  where rr.to_address = '" . $address . "' and `rr`.status in ('" . RedPacketRecord::EXCHANGE_SUCC . "','". RedPacketRecord::REDEMPTION . "') order by id desc limit " . $pageSize . " offset " . $offset;
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
           $theme = RedPacketTheme::find()->where(['id' => $v['theme_id']])->one();
           $list[$k]['theme_img'] = $theme['img'];
           $list[$k]['theme_thumb_img'] = $theme['thumb_img'];
           $list[$k]['theme_share_img'] = $theme['share_img'];
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
        $model->address = $data["from_address"];
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
     * 根据状态获取红包列表
     * @param $status
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRedPacketList($status)
    {
        return RedPacket::find()->where(['status' => $status])->asArray()->all();
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
