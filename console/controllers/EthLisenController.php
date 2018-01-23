<?php
namespace console\controllers;

use common\models\CenterBridge;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
use common\helpers\OutputHelper;
use common\wallet\Operating;
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
        echo "eth-ug状态监听".time().PHP_EOL;
        //读取日志文件
        //OutputHelper::readLog(dirname(__DIR__). "/locklog/ethListen.log");

        //写入执行状态status为1
       // OutputHelper::writeLog(dirname(__DIR__) . '/locklog/ethListen.log',json_encode(["status" => Operating::LOG_LOCK_STATUS]));
        //获取数据库中待确认信息
        $unsucc_info = Operating::getUnconfirmedList(CenterBridge::ETH_UG, Yii::$app->getRuntimePath() . '/ethListen.log');
        if (!$unsucc_info) {
            //OutputHelper::writeLog(dirname(__DIR__) . '/locklog/ethListen.log', json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无交易数据！".PHP_EOL;die;
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
                echo "更新数据库失败".PHP_EOL;
                continue;
            }
        }
        //OutputHelper::writeLog(dirname(__DIR__) . '/locklog/ethListen.log', json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "更新结束".time().PHP_EOL;
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
        echo "开始".time().PHP_EOL;
        //读取日志文件
       // OutputHelper::readLog(dirname(__DIR__) . "/locklog/blockNumListen.log");

        //写入执行状态status为1
        //OutputHelper::writeLog( dirname(__DIR__) . "/locklog/blockNumListen.log",json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //16进制 转换为10进制 后 -12块获取最新块
        $safetyBlock = Operating::getNewSafetyBlock();
        if(!$safetyBlock){
            //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/blockNumListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "获取安全块错误".PHP_EOL;die();
        }
        //获取blocknumber不为0且状态为待确认状态
        if (!$trade_info = CenterBridge::getListByTypeAndStatusAndBlockNumber()) {
           // OutputHelper::writeLog(dirname(__DIR__) . "/locklog/blockNumListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无区块信息".PHP_EOL;die;
        }

        foreach ($trade_info as  $k=> $v) {
            //判断是否超过安全块
            if ($safetyBlock < $v["from_block"]) {
                continue;
            }

            //todo 1:签名服务器做签名api 2:去ug链上转账操作返回txid后(api) 3:ug网络确认(api)直接更新数据库状态为转账成功
            //获取nince且组装签名数据
            $send_sign_data = Operating::getNonceAssembleData($v, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth_getTransactionCount", [Yii::$app->params["ug"]["owner_address"], "pending"]);
            if (!$send_sign_data) {
                echo "获取nince且组装签名数据";
                continue;
            }

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_url"], $send_sign_data, Yii::$app->params["ug"]["ug_host"], "eth_sendRawTransaction");
            if (isset($res_data['error'])) {
                echo "数据获取签名且广播交易错误";
                continue;
            }

            //根据txid去块上确认
            $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);
            if (!$trade_info) {
                CenterBridge::updateStatus($v["app_txid"], CenterBridge::SEND_SUCCESS, $res_data["result"]);
                echo "txid去块上确认失败";
                continue;
            }

            //截取blockNumber
            $trade_info = Operating::substrHexdec($trade_info["result"]);

            //更新数据库
            if(!CenterBridge::updateStatusAndTime($v["app_txid"], CenterBridge::LISTEN_CONFIRM_SUCCESS, $res_data["result"], $trade_info["blockNumber"])){
                echo "更新数据库失败".PHP_EOL;
                continue;
            }
        }

        //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/blockNumListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "更新成功!".time().PHP_EOL;
    }

    /**
     * console of eth-listen/Listen-owner-execution-status
     * 1:获取数据库状态为: 3 的数据 类型为:ug-eth
     * 2:根据地址和金额扫eth块
     * 3:修改状态 && 时间 owner_txid
     * @return string
     */
    public function actionListenOwnerExecutionStatus()
    {
        echo "开始".time().PHP_EOL;
        //读取日志文件
        //OutputHelper::readLog(dirname(__DIR__) . "/locklog/executionListen.log");

        //写入执行状态status为1
        //OutputHelper::writeLog(dirname(__DIR__) . '/locklog/executionListen.log',json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库状态为:3的数据 类型为:ug-eth owner_id 不为空
        $info = CenterBridge::getListByTypeAndStatusAndOwnerTxid();
        if(!$info){
            OutputHelper::writeLog(dirname(__DIR__) . '/locklog/executionListen.log',json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无处理数据".PHP_EOL;die;
        }

        //获取最新安全块
        $safetyBlock = Operating::getNewSafetyBlock();
        if(!$safetyBlock){
           // OutputHelper::writeLog(dirname(__DIR__) . '/locklog/executionListen.log',json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "获取安全块错误".PHP_EOL;die();
        }
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

        //OutputHelper::writeLog(dirname(__DIR__) . '/locklog/executionListen.log',json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "更新成功".time().PHP_EOL;
    }

}