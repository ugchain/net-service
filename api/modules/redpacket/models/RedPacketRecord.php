<?php
namespace api\modules\redpacket\models;

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
    const REDPACKET_CODE_SLAT = "h23o4n4fdgbvzxa31ond3al12ai";

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
        return substr(md5($this->openid.$this->rid.self::REDPACKET_CODE_SLAT), 1, 9);
    }
}
