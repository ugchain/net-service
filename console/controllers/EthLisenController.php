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

        foreach ($unsucc_info as $list)
        {
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params["eth_host"], "eth_getTransactionByHash", [$list["app_txid"]]);
            if (!$block_info) {
                continue;
            }

            $trade_info = $block_info["result"];
            if ($trade_info["blockNumber"] == null) {
                continue;
            }

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($block_info["result"], 1);

            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($list["app_txid"], $trade_info["blockNumber"], $trade_info["gasPrice"])){
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
     * 2:且获取gas_used
     * 3:通知owners
     * 4:更新 status数据  && gas_used && (块上成功时间 || 发送给owners成功时间)
     * @return string
     */
    public function actionListenBlocknumber()
    {
        echo "开始";

        //16进制 转换为10进制 后 -12块获取最新块
        $newblock = Operating::getNewSafetyBlock();

        //获取blocknumber不为0且状态为待确认状态
        if (!$trade_info = CenterBridge::getListByTypeAndStatusAndBlockNumber()) {
            echo "暂无区块信息";die;
        }

        foreach ($trade_info as  $k=> $v) {
            if ($newblock < $v["from_block"] + Operating::SECURITY_BLOCK) {
                continue;
            }

            //根据txid获取交易详细信息
            $txid_info = Operating::txidByTransactionInfo(Yii::$app->params["eth_host"], "eth_getTransactionReceipt", [$v["app_txid"]]);
            if (!$txid_info) {
                continue;
            }

            //截取gasUsed
            $txid_info = Operating::substrHexdec($txid_info["result"], Operating::SUBSTR_TYPE_GASUSED);

            //todo 1:签名服务器做签名api 2:去ug链上转账操作返回txid后(api) 3:ug网络确认(api)直接更新数据库状态为转账成功
            //获取nince且组装签名数据
            $send_sign_data = Operating::getNonceAssembleData($v, Operating::UgGasPrice(), Yii::$app->params["eth_host"], "eth.getTransactionCount", [$v['address']]);

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["sign_host"]."/ugSign", $send_sign_data, Yii::$app->params["ug_host"], "eth_sendRawTransaction");
            if (!$res_data) {
                continue;
            }
            //txid去块上确认
            $ug_block_listen = CurlRequest::ChainCurl(Yii::$app->params["ug_host"],"eth_getTransactionReceipt",[$res_data["result"]]);
            //这个地方不会出错,秒级
            if(!$ug_block_listen){
                continue;
            }
            $ug_block_listen = json_decode($ug_block_listen,true);
            if(isset($ug_block_listen["error"])){
                continue;
            }
            $trade_info = $ug_block_listen["result"];
            if($trade_info["blockNumber"] == null){
                continue;
            }

            //更新数据库
            if(!CenterBridge::updateGasUsedAndStatusAndTime($v["app_txid"], $txid_info["result"]["gasUsed"], CenterBridge::LISTEN_CONFIRM_SUCCESS, $res_data["result"], $trade_info["blockNumber"])){
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
        $newblock = Operating::getNewSafetyBlock();
        foreach ($info as $owner){
            if ($owner['to_block'] == 0) {
                //根据owner_txid获取交易详细信息
                $block_info = Operating::txidByTransactionInfo(Yii::$app->params["eth_host"], "eth_getTransactionByHash", [$owner["owner_txid"]]);
                if (!$block_info) {
                    continue;
                }

                $trade_info = $block_info["result"];
                if ($trade_info["blockNumber"] == null) {
                    continue;
                }

                //blockNumber截取前两位0x && 16进制 转换为10进制
                $trade_info = Operating::substrHexdec($block_info["result"], 1);

                if ($newblock < $trade_info["blockNumber"]) {
                    CenterBridge::updateBlockByTxid($owner["owner_txid"], $trade_info["blockNumber"]);
                } else {
                    CenterBridge::updateBlockAndTimeByTxid($owner["owner_txid"], $trade_info["blockNumber"]);
                }
            } else {
                if ($newblock > $owner["to_block"]) {
                    CenterBridge::updateListenSuccTimeByTxid($owner["owner_txid"]);
                }
            }
        }
        echo "更新成功";
    }

}