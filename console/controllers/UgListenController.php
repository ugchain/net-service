<?php
namespace console\controllers;

use common\helpers\OutputHelper;
use common\Wallet\Operating;
use common\models\CenterBridge;
use common\models\ExtraPrice;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
/**
 * Class UgLisenController By ug-eth监听确认服务
 * @package console\controller
 */
class UgListenController extends Controller
{

    /**
     * console of ug-listen/listen-txid 根据txid获取blocknumber and gas_price
     * @return string
     */
    public function actionListenTxid()
    {
        echo "UG转账ETH开始".time();
        //读取日志文件
        OutputHelper::readLog(Yii::$app->getRuntimePath() . "/uglisten.log");

        //写入执行状态status为1
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库中待确认信息
        $unsucc_info = Operating::getUnconfirmedList(CenterBridge::UG_ETH, Yii::$app->getRuntimePath() . '/uglisten.log');
        if (!$unsucc_info) {
            echo "暂无交易数据！";
        }
        //获取gas_price
        $gas_info = ExtraPrice::getList();
        $gas_price = $gas_info['gas_min_price'];
        foreach ($unsucc_info as $list)
        {
            //根据交易id获取订单信息
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params['ug']["ug_host"], "eth_getTransactionReceipt", [$list["app_txid"]]);
            if (!$block_info) {
                continue;
            }

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($block_info);

            //todo 1:签名服务器做签名(返回txid) 2:去eth链上转账操作 3:更新数据库 status=3&&blockNumber&&owner_txid&&block_send_succ_time
            //获取nonce值且组装数据
            $send_sign_data = Operating::getNonceAssembleData($list, $gas_price, Yii::$app->params["eth"]["eth_host"], "eth.getTransactionCount", [$list['address']]);

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["eth"]["eth_sign_url"], $send_sign_data, Yii::$app->params["eth"]["eth_host"], "eth_sendRawTransaction");
            if (!$res_data) {
                continue;
            }

            //更新数据库
            if(!CenterBridge::updateBlockAndOwnerTxid($list["app_txid"], $trade_info["blockNumber"], $res_data["result"])){
                OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
                echo "更新数据库失败";
                continue;
            }
        }

        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "UG转账ETH结束".time();
    }
}