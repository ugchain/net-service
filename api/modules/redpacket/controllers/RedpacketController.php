<?php
namespace api\modules\redpacket\controllers;

use api\modules\redpacket\models\RedPacket;
use api\modules\user\models\Trade;
use api\modules\redpacket\models\RedPacketRecord;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use common\wallet\Operating;
use yii\web\UploadedFile;

class RedpacketController extends  Controller
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access-behavior' => [
//                'class' => 'common\behavior\AccessBehavior',//验证签名
//            ]
//        ];
//    }

        /**
         * 红包兑换
         */
        public function actionExchange()
        {
            //兑换码
            $code = Yii::$app->request->post("code", "");
            //账户地址
            $address = Yii::$app->request->post("address", "");

            //校验账户和兑换码
            $result = RedPacketRecord::checkCodeAndAddress($address, $code);
            if (!$result) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::RED_PACKET_REDEMPTION);
            }
            $result['address'] = $result['to_address'];
            $result['app_txid'] = $result['txid'];
            //开始兑换红包
            //获取nince且组装签名数据
            $send_sign_data = Operating::getNonceAssembleData($result, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth_getTransactionCount", [Yii::$app->params["ug"]["owner_address"], "pending"]);

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_url"], $send_sign_data, Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction");

            //根据txid去块上确认
            $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);

            //上链成功,插入内部交易表同时修改红包记录表状态
            $trade_info['blockNumber'] = 0;
            $recordStatus = RedPacketRecord::REDEMPTION;
            $tradeStatus = Trade::CONFIRMED;
            if ($trade_info) {
                //截取blockNumber
                $trade_info = Operating::substrHexdec($trade_info["result"]);
                $tradeStatus = Trade::SUCCESS;
                $recordStatus = RedPacketRecord::EXCHANGE_SUCC;
            }

            if (!RedPacketRecord::updateStatusByTxid($result['txid'], $recordStatus)) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
            }
            if (!Trade::insertData($res_data["result"], $result["from_address"], $result["to_address"], $result["amount"], $tradeStatus, Trade::REDPACKET, $trade_info['blockNumber'])) {
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
            }

            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
        }

        /**
         * 红包详情
         */
        public function actionDetail()
        {

        }

}