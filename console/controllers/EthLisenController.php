<?php
namespace console\controllers;

use common\models\CenterBridge;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
use common\helpers\OutputHelper;
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
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status"=>1]));

        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus(CenterBridge::ETH_UG);
        if(!$unsucc_info){
            echo "暂无需要确认的数据";die();
        }
        foreach ($unsucc_info as $list)
        {
            $block_info = CurlRequest::EthCurl("eth_getTransactionByHash",[$list["app_txid"]]);
            if(!$block_info){
                continue;
            }

            $block_info = json_decode($block_info,true);
            if(isset($block_info["error"])){
                continue;
            }
            $trade_info = $block_info["result"];
            if($trade_info["blockNumber"] == null){
                continue;
            }

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = OutputHelper::substrHexdec($block_info["result"], 1);

            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($list["app_txid"],$trade_info["blockNumber"],$trade_info["gasPrice"])){
                OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status"=>0]));
                echo "更新数据库失败";
                continue;
            }
        }
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status"=>0]));
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
        //1：获取最新安全块
        $newblock = OutputHelper::getNewSafetyBlock();

        //获取blocknumber不为0且状态为待确认状态
        if (!$trade_info = CenterBridge::getListByTypeAndStatusAndBlockNumber()) {
            echo "暂无区块信息";die;
        }
        foreach ($trade_info as  $k=> $v) {
            if($newblock < $v["from_block"] + 12){
                continue;
            }
            $txid_info = CurlRequest::EthCurl("eth_getTransactionReceipt",[$v['app_txid']]);
            if(!$txid_info){
                continue;
            }
            $txid_info = json_decode($txid_info,true);
            if(isset($txid_info["error"])){
                continue;
            }

            //截取gasUsed
            $txid_info = OutputHelper::substrHexdec($txid_info["result"], 2);

            //todo 1:签名服务器做签名api 2:去ug链上转账操作返回txid后(api) 3:ug网络确认(api)直接更新数据库状态为转账成功
            $owner_status = "1";
            if(!$owner_status){
                continue;
            }
            //更新数据库
            if(!CenterBridge::updateGasUsedAndStatusAndTime($v["app_txid"], $txid_info['gasUsed'], CenterBridge::LISTEN_CONFIRM_SUCCESS, "1111")){
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
        $newblock = OutputHelper::getNewSafetyBlock();

        foreach ($info as $owner){
            if($owner['to_block'] == 0){
                $block_info = CurlRequest::EthCurl("eth_getTransactionByHash",[$owner["owner_txid"]]);
                if(!$block_info){
                    continue;
                }
                $block_info = json_decode($block_info,true);
                if(isset($block_info["error"])){
                    continue;
                }
                $trade_info = $block_info["result"];
                if($trade_info["blockNumber"] == null){
                    continue;
                }

                //blockNumber截取前两位0x && 16进制 转换为10进制
                $trade_info = OutputHelper::substrHexdec($block_info["result"], 1);

                if($newblock < $trade_info["blockNumber"]){
                    CenterBridge::updateBlockByTxid($owner["owner_txid"],$trade_info["blockNumber"]);
                }else{
                    CenterBridge::updateBlockAndTimeByTxid($owner["owner_txid"],$trade_info["blockNumber"]);
                }
            }else{
                if($newblock > $owner["to_block"]){
                    CenterBridge::updateListenSuccTimeByTxid($owner["owner_txid"]);
                }
            }
        }
        echo "更新成功";
    }

}