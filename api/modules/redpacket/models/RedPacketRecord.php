<?php
namespace api\modules\redpacket\models;

use common\helpers\RewardData;
use common\helpers\OutputHelper;
use Yii;
use yii\db\Exception;

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
     * 检查红包code是否存在
     * @param $code
     * @param $address
     */
    public static function checkCodeAndAddress($code)
    {
        //查询红包记录
        $recordInfo = RedPacketRecord::find()->where(['code' => $code])->one();
        if (!$recordInfo) {
            return 1;
        }
        $recordInfo = $recordInfo->attributes;
        //查询是否为领取状态
        if ($recordInfo['status'] == self::RECEIVED) {
            return $recordInfo;
        }

       return 2;
    }

    /**
     * @param $id
     * @param $status
     * @param $txid
     * @param $amount
     *
     * @return int
     */
    public static function updateStatusAndTxidByid($id, $status, $txid, $to_address)
    {
        return RedPacketRecord::updateAll(["status" => $status, "txid" => $txid, "to_address" => $to_address], ["id" => $id]);
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
            throw new Exception(\common\helpers\ErrorCodes::RED_PACKET_OPEN);
        }

        //红包是否以被领光
        $redPacket = RedPacket::findOne($this->rid);
        if ($redPacket->already_received_quantity >= $redPacket->quantity) {
            throw new Exception(\common\helpers\ErrorCodes::RED_PACKET_NULL);
        }

        //如果红包状态为0创建红包和1链上失败则不能领取
        if ($redPacket->status == 0 || $redPacket->status == 1) {
            throw new Exception(\common\helpers\ErrorCodes::RED_PACKET_GRAD_FAIL);
        }

        //去redis获取红包金额
        $rewardData = new RewardData();
        $this->amount = OutputHelper::NumToString($rewardData->get($this->rid));
        //立即删除这个金额
        $rewardData->delete($this->rid);

        //增加一次领取次数，如果正好领取完更改红包状态为已完成
        $redPacket->already_received_quantity = $redPacket->already_received_quantity+1;
        if ($redPacket->already_received_quantity >= $redPacket->quantity && $redPacket->status != 3 && $redPacket->status != 4) {
            $redPacket->status = 3;
            $redPacket->finish_time = time();
        }

        //获取红包表的来源地址
        $this->from_address = $redPacket->address;

        $redPacket->save();
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
        $redPacket = RedPacket::findOne($rid);

        //拼装当前用户的红包信息
        $info['state'] = null;
        $info['code'] = !empty($record->code) ? $record->code : '';
        $info['amount'] = !empty($record->amount) ? OutputHelper::fromWei($record->amount) : '';

        //如果当前用户与其他用户红包都被提取成功则状态为已结束
        //需求更改，临时保存，下次迭代删除 [2018 02-09 am]
        //$receieNotSuccessNums = self::find()->where("rid=$rid and status!=4")->count();
        //if ($redPacket->already_received_quantity >= $redPacket->quantity && !$receieNotSuccessNums) {
            //$info['state'] = 4;
            //return $info;
        //}

        //判断当前红包与当前用户领取状态
        if (empty($record)) {
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
