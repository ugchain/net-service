<?php
namespace api\modules\redpacket\models;

use common\helpers\RewardData;
use common\helpers\OutputHelper;
use Yii;

class RedPacketRecord extends \common\models\RedPacketRecord
{
    /**
     * @var intger
     * 红包领取记录已经领取状态
     */
    const REDPACKET_RECORD_STATUS_TORECEIVE = 1;

    /**
     * @var intger
     * 红包领取记录兑换中状态
     */
    const REDPACKET_RECORD_STATUS_REDEMPTION = 2;

    /**
     * @var intger
     * 红包领取记录兑换失败状态
     */
    const REDPACKET_RECORD_STATUS_EXCHANGEFAILED = 3;

    /**
     * @var intger
     * 红包领取记录兑换成功状态
     */
    const REDPACKET_RECORD_STATUS_EXCHANGESUCCESS = 4;

    /**
     * @var intger
     * 红包领取记录过期状态
     */
    const REDPACKET_RECORD_STATUS_EXPIRED = 5;

    /**
     * @var string
     * 生成红包口令需要的slat
     */
    static public $_salt;

    /**
     * 检查红包code和address是否存在
     * @param $code
     * @param $address
     */
    public static function checkCodeAndAddress($address, $code)
    {
        //查询红包记录
        $recordInfo = RedPacketRecord::find()->where(['code' => $code, 'to_address' => $address])->one()->attributes;
        if (!$recordInfo) {
            return false;
        }
        //查询是否为领取状态
        if ($recordInfo['status'] == self::RECEIVED) {
            return $recordInfo;
        }

       return false;
    }

    /**
     * @param $id
     * @param $status
     * @param $txid
     * @param $amount
     *
     * @return int
     */
    public static function updateStatusAndTxidByid($id, $status, $txid)
    {
        return RedPacketRecord::updateAll(["status" => $status, "txid" => $txid], ["id" => $id]);
    }

    /**
     * 生成微信红包兑换码，根据微信用户openid、红包id和salt组合的md5值再截取前9位生成
     * @return bool|string
     */
    public function grenerateRedpacketCode()
    {
        $this->code = "UaG3C".substr(md5($this->openid.$this->rid.$this->salt), 1, 9);
    }

    /**
     * 领取一个红包金额，并累加领取次数
     * @return string
     */
    public function setInfoWithRedpacket()
    {
        //一个红包一个微信用户职能领取一次
        $redPacketRecordCountForCurrentOpenid = self::find()
            ->where("rid=".$this->rid." and openid='".$this->openid."'")
            ->count();
        if ($redPacketRecordCountForCurrentOpenid != 0) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_EXIST);
        }

        //红包是否以被领光
        $redPacket = RedPacket::findOne($this->rid);
        if ($redPacket->already_received_quantity >= $redPacket->quantity) {
            output::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_LED_LIGHT);
        }

        //如果红包状态为0创建红包和1链上失败则不能领取
        if ($redPacket->status == 0 || $redPacket->status == 1) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_GRAD_FAIL);
        }

        //去redis获取红包金额
        $rewardData = new RewardData();
        $this->amount = $rewardData->get($this->rid);

        //增加一次领取次数，如果正好领取完更改红包状态为已完成
        $redPacket->already_received_quantity = $redPacket->already_received_quantity+1;
        if ($redPacket->already_received_quantity >= $redPacket->quantity && $redPacket->status != 3 && $redPacket->status != 4) {
            $redPacket->status = 3;
        }

        //获取红包表的来源地址
        $this->from_address = $redPacket->address;

        $redPacket->save();

        //判断是否获取到红包金额
        if (!$this->amount) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_GRAD_FAIL);
        }
    }

    /**
     * 获取当前用户的红包状态
     *
     * @param $rid
     * @param $openid
     * @return int
     */
    public static function getRedPacketRecordInfo($rid, $openid)
    {
        $record = self::find()->where("rid=$rid and openid='$openid'")->one();
        $info['state'] = null;
        $info['code'] = !empty($record->code) ? $record->code : '';
        $info['amount'] = !empty($record->amount) ? $record->amount : '';
        if (empty($record)) {
            $redPacket = RedPacket::findOne($rid);
            if ($redPacket->status == 3) {
                $info['state'] = 3; //以领光
            } elseif ($redPacket->status == 4) {
                $info['state'] = 4; //已过期
            }
        } else {
            switch ($record->status) {
                case self::REDPACKET_RECORD_STATUS_TORECEIVE:
                case self::REDPACKET_RECORD_STATUS_REDEMPTION:
                case self::REDPACKET_RECORD_STATUS_EXCHANGEFAILED:
                    $info['state'] = 1; //以领取、未兑换
                    break;
                case self::REDPACKET_RECORD_STATUS_EXCHANGESUCCESS:
                    $info['state'] = 2; //以兑换
                    break;
                case self::REDPACKET_RECORD_STATUS_EXPIRED:
                    $info['state'] = 4; //已结束
                    break;
            }
        }

        return $info;
    }

    /**
     * [getter]获取微信红包微信口令的盐
     *
     * @return string
     */
    public function getSalt()
    {
        if (empty(self::$_salt)) {
            if (($salt = Yii::$app->params['wecat_redpacket_config']['salt']) == false) throw new InvalidParamException("redpacket salt does not exist.");
            self::$_salt = urlencode($salt);
        }

        return self::$_salt;
    }
}
