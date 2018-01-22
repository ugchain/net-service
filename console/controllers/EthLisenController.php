<?php
namespace console\controllers;

use common\models\CenterBridge;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
use common\helpers\OutputHelper;
use common\Wallet\Operating;
use yii\db\Exception;
use yii\log\Logger;
/**
 * Class EthLisenController By eth-ug监听确认服务
 * @package console\controller
 */
class EthLisenController extends Controller
{

    /**
     * console of eth-listen/listen-txid 根据txid获取blocknumber and gas_price
     * @return string
     */
    public function actionListenTxid()
    {
        echo "eth-ug状态监听";
        //读取日志文件
        OutputHelper::readLog(Yii::$app->getRuntimePath() . "/ethlisten.log");

        //写入执行状态status为1
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库中待确认信息
        $unsucc_info = Operating::getUnconfirmedList(CenterBridge::ETH_UG, Yii::$app->getRuntimePath() . '/ethlisten.log');
        if (!$unsucc_info) {
            echo "暂无交易数据！";
        }

        foreach ($unsucc_info as $list)
        {
            $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["eth"]["eth_host"], "eth_getTransactionReceipt", [$list["app_txid"]]);
            if (!$trade_info) {
                continue;
            }
            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($trade_info);

            //更新数据库
            if (!CenterBridge::updateBlockAndGasUsed($list["app_txid"], $trade_info["blockNumber"], $trade_info["gasUsed"])) {
                OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log', json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
                echo "更新数据库失败";
                continue;
            }
        }
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log', json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "更新结束";
    }

    /**
     * console of eth-listen/listen-blocknumber
     * 1:根据最新安全块,确认上块,
     * 2:获取离线签名 && 广播交易
     * 3:更新 status数据 && (块上成功时间 || 发送给owners成功时间)
     * @return string
     */
    public function actionListenBlocknumber()
    {
        echo "开始";

        //16进制 转换为10进制 后 -12块获取最新块
        $safetyBlock = Operating::getNewSafetyBlock();

        //获取blocknumber不为0且状态为待确认状态
        if (!$trade_info = CenterBridge::getListByTypeAndStatusAndBlockNumber()) {
            echo "暂无区块信息";die;
        }

        foreach ($trade_info as  $k=> $v) {
            //判断是否超过安全块
            if ($safetyBlock < $v["from_block"]) {
                continue;
            }

            //todo 1:签名服务器做签名api 2:去ug链上转账操作返回txid后(api) 3:ug网络确认(api)直接更新数据库状态为转账成功
            //获取nince且组装签名数据
            $send_sign_data = Operating::getNonceAssembleData($v, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth.getTransactionCount", [$v['address']]);

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_url"], $send_sign_data, Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction");
            if (!$res_data) {
                continue;
            }

            //根据txid去块上确认
            $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);
            if (!$trade_info) {
                continue;
            }

            //截取blockNumber
            $trade_info = Operating::substrHexdec($trade_info["result"]);

            //更新数据库
            if(!CenterBridge::updateStatusAndTime($v["app_txid"], CenterBridge::LISTEN_CONFIRM_SUCCESS, $res_data["result"], $trade_info["blockNumber"])){
                echo "更新数据库失败";
                continue;
            }
        }
        echo "更新成功!";
    }

    /**
     * console of eth-listen/Listen-owner-execution-status
     * 1:获取数据库状态为:3的数据 类型为:ug-eth
     * 2:根据地址和金额扫eth块
     * 3:修改状态 && 时间 owner_txid
     * @return string
     */
    public function actionListenOwnerExecutionStatus()
    {
        echo "开始";
        //获取数据库状态为:3的数据 类型为:ug-eth owner_id 不为空
        $info = CenterBridge::getListByTypeAndStatusAndOwnerTxid();
        if(!$info){
            echo "暂无处理数据";die;
        }
        //获取最新安全块
        $safetyBlock = Operating::getNewSafetyBlock();
        foreach ($info as $owner){
            if ($owner['to_block'] == 0) {
                //根据owner_txid获取交易详细信息
                $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["eth"]["eth_host"], "eth_getTransactionByHash", [$owner["owner_txid"]]);
                if (!$trade_info) {
                    continue;
                }

                //blockNumber截取前两位0x && 16进制 转换为10进制
                $trade_info = Operating::substrHexdec($trade_info);

                if ($safetyBlock < $trade_info["blockNumber"]) {
                    CenterBridge::updateBlockByTxid($owner["owner_txid"], $trade_info["blockNumber"]);
                } else {
                    CenterBridge::updateBlockAndTimeByTxid($owner["owner_txid"], $trade_info["blockNumber"]);
                }
            } else {
                if ($safetyBlock > $owner["to_block"]) {
                    CenterBridge::updateListenSuccTimeByTxid($owner["owner_txid"]);
                }
            }
        }
        echo "更新成功";
    }

}