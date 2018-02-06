<?php
namespace api\modules\redpacket\models;

use common\helpers\OutputHelper;
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
     * @param $id intager 红包ID
     * @param $type boolean 是否fromWei
     * @return array
     */
    public static function getRedPacketInfoWithRecordList($id, $type = true)
    {
        $result = self::findOne($id);

        //红包详情
        $redpacketInfo = [
            'id' => $result->id,
            'txid' => $result->txid,
            'title' => $result->title,
            'status' => $result->status,
            'quantity' => $result->quantity,
            'theme_id' => $result->theme_id,
            'already_received_quantity' => count($result->redPacketRecords),
            'amount' => !$type ? OutputHelper::fromWei($result->amount) : $result->amount,
            'back_amount' => !$type ? OutputHelper::fromWei($result->back_amount) : OutputHelper::NumToString($result->back_amount),
            'already_received_amount' => 'TODO',
            'finish_time' => !empty($result->finish_time) ? date('m-d h:i', $result->finish_time) : '',
            'expire_time' => !empty($result->expire_time) ? date('m-d h:i', $result->expire_time) : '',
            'last_time' => self::timeTostring($result->expire_time),
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
                    $alreadyReceivedAmount += OutputHelper::fromWei($redPacketRecord->amount);
                    break;
            }
            $redPacketRecordList[] = [
                'wx_name' => $redPacketRecord->wx_name,
                'wx_avatar' => $redPacketRecord->wx_avatar,
                'amount' => !$type ? OutputHelper::fromWei($redPacketRecord->amount) : OutputHelper::NumToString($redPacketRecord->amount),
                'status' => $redPacketRecord->status,
                'time' => !empty($time) ? date('m-d H:i', $time) : ''
            ];
        }

        //获取数据库红包主题资源
        $redPacketTheme = $result->redPacketTheme;

        //拼装回返
        $redpacketInfo['already_received_amount'] =  !$type ? $alreadyReceivedAmount : OutputHelper::toWei($alreadyReceivedAmount);
        $redpacketInfo['theme_img'] = !empty($redPacketTheme->img) ? $redPacketTheme->img : '';
        $redpacketInfo['theme_thumb_img'] = !empty($redPacketTheme->thumb_img) ? $redPacketTheme->thumb_img : '';
        $redpacketInfo['theme_share_img'] = !empty($redPacketTheme->share_img) ? $redPacketTheme->share_img : '';
        $redpacketInfo['redPacketRecordList'] = $redPacketRecordList;
        $redpacketInfo['image_url'] = Yii::$app->params['image_url'];
        $redpacketInfo["share_url"] = Yii::$app->params["host"]."/redpacket/we-chat-red-packet/redirect-url?redpacket_id=".$result->id;


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
     * 将时间戳转换成剩余的小时分钟，只支持在24小时以内的转换
     * @param $time
     * @return string
     */
    private static function timeTostring($time)
    {
        $time = $time - time();

        $hour = 0;
        if ($time >= 3600) { // 如果大于1小时
            $hour = (int)($time / 3600);
            $time = $time % 3600; // 计算小时后剩余的毫秒数
        }

        $minute = (int)($time / 60); // 剩下的毫秒数都算作分

        return $hour.'时'.$minute.'分';
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
        $sum = 0;
        $count = 0;
        //获取领取个数
        if ($type == 0) {
            $where = ['to_address' => $address];
            $count = RedPacketRecord::find()->where($where)->count();
            $sum = RedPacketRecord::find()->where($where)->sum('amount');
        } else {
            $where = ['address' => $address];
            $count = RedPacket::find()->where($where)->count();
            $sum = RedPacket::find()->where($where)->sum('amount');
        }
        $sum = OutputHelper::NumToString($sum);

        $query = Yii::$app->db;
        $offset = ($page - 1) * $pageSize;
        if ($type == self::I_RECEIVED) {
            $sql = "SELECT `rr`.status, `rr`.to_address, `rr`.exchange_time, `rr`.expire_time, `rr`.rid, `rr`.amount, `rp`.theme_id, `rp`.title, `rp`.id FROM `ug_red_packet_record` as rr LEFT JOIN `ug_red_packet` as rp on rr.rid = rp.id 
                  where rr.to_address = '" . $address . "' and `rr`.status in ('" . RedPacketRecord::EXCHANGE_SUCC . "','". RedPacketRecord::REDEMPTION . "') order by id desc limit " . $pageSize . " offset " . $offset;
        } else {
            $sql = "SELECT * FROM `ug_red_packet` where address = '" . $address . "' order by id desc limit " . $pageSize . " offset " . $offset;
        }
        $commond = $query->createCommand($sql);
        $list = $commond->queryAll();
        //默认无下一页
        $is_next_page = "0";
        if ($count - ($page * $pageSize) >= 0) {
            $is_next_page = "1";//有下一页
        }

       foreach ($list as $k => $v) {
           //获取已领人数
           $receive_count = RedPacketRecord::find()->where(['rid' => $v['id']])->count();
           $theme = RedPacketTheme::find()->where(['id' => $v['theme_id']])->one();
           $list[$k]['theme_img'] = $theme['img'];
           $list[$k]['theme_thumb_img'] = $theme['thumb_img'];
           $list[$k]['theme_share_img'] = $theme['share_img'];
           $list[$k]['receive'] = $receive_count;
       }

        return ['list' => $list, 'is_next_page' => $is_next_page, "count"=> $count, "page" => $page, "pageSize" => $pageSize, "received_amount" => empty($sum)?0:$sum, 'received_quantity' => $count];
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
        switch ($status)
        {
            case 1:
                $updateData["fail_time"] = time();
                break;
            case 2:
                $updateData["create_succ_time"] = time();
                $updateData["expire_time"] = time() + 24 * 60 * 60;
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

    /**
     * 检查红包是否存在和是否过期
     * @param $packet_id
     *
     * @return bool
     */
    public static function checkRedPacketExistAndExpired($packet_id)
    {
        //查询红包是否存在
        $redPacketInfo = RedPacket::find()->where(['id' => $packet_id])->one();
        if (!$redPacketInfo) {
            return false;
        }
        $redPacketInfo = $redPacketInfo->attributes;
        //红包是否过期
        if ($redPacketInfo['status'] == RedPacket::REDPACKET_EXPIRED) {
            return false;
        }

        return $redPacketInfo;
    }
}
