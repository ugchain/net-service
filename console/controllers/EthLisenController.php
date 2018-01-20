<?php
namespace console\controllers;


use common\models\CenterBridge;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
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
        $ethlisten = file_get_contents(Yii::$app->getRuntimePath() . '/ethlisten.log');
        $ethlistenlog = json_decode($ethlisten,true);
        if($ethlistenlog["status"] != 0){
            echo "正在执行中";die();
        }
        //写入执行状态status为1
        file_put_contents(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status"=>1]));
        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus();
        if(!$unsucc_info){
            echo "暂无需要确认的数据";die();
        }
        foreach ($unsucc_info as $list)
        {
            $block_info = CurlRequest::EthCurl("eth_getTransactionByHash",[$list["app_txid"]]);
            if(!$block_info){
                continue;
            }
            //var_dump($block_info);die;
            $block_info = json_decode($block_info,true);
            if(isset($block_info["error"])){
                continue;
            }
            $trade_info = $block_info["result"];
            if($trade_info["blockNumber"] == null){
                continue;
            }
            //blockNumber截取前两位0x
            $trade_info["blockNumber"] = substr($trade_info["blockNumber"],2);
            //16进制 转换为10进制 后 -12块获取最新块
            $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);

            //gas_price截取前两位0x
            $trade_info["gasPrice"] = substr($trade_info["gasPrice"],2);
            //16进制 转换为10进制
            $trade_info["gasPrice"] = hexdec($trade_info["gasPrice"]);
            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($list["app_txid"],$trade_info["blockNumber"],$trade_info["gasPrice"])){
                echo "更新数据库失败";
                continue;
            }
        }
        file_put_contents(Yii::$app->getRuntimePath() . '/ethlisten.log',json_encode(["status"=>0]));
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
        $new_block_data = CurlRequest::EthCurl("eth_blockNumber",[]);
        //{"jsonrpc":"2.0","id":"1","result":"0xaa6"} result 是16进制 需要转换为10进制
        if(!$new_block_data){
            echo "eth返回块信息错误";die;
        }
        //解析最新块
        $newblock_str = json_decode($new_block_data,true)["result"];
        //截取前两位0x
        $newblock_str = substr($newblock_str,2);
        //16进制 转换为10进制 后 -12块获取最新块
        $newblock = hexdec($newblock_str) - 12;
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
            //gas_used截取前两位0x
            $txid_info["result"]["gasUsed"]= substr($txid_info["result"]["gasUsed"],2);
            //16进制 转换为10进制
            $txid_info["result"]["gasUsed"] = hexdec($txid_info["result"]["gasUsed"]);

            //todo 1:签名服务器做签名api 2:去ug链上转账操作返回txid后(api) 3:ug网络确认(api)直接更新数据库状态为转账成功
            $owner_status = "1";
            if(!$owner_status){
                continue;
            }
            //更新数据库
            if(!CenterBridge::updateGasUsedAndStatusAndTime($v["app_txid"],$txid_info["result"]["gasUsed"],CenterBridge::LISTEN_CONFIRM_SUCCESS,"1111")){
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
        //获取最新块
        $new_block_data = CurlRequest::EthCurl("eth_blockNumber");
        //{"jsonrpc":"2.0","id":"1","result":"0xaa6"} result 是16进制 需要转换为10进制
        if(!$new_block_data){
            echo "eth返回块信息错误";die;
        }
        //解析最新块
        $newblock_str = json_decode($new_block_data,true)["result"];
        //截取前两位0x
        $newblock_str = substr($newblock_str,2);
        //16进制 转换为10进制 后 -12块获取最新块
        $newblock = hexdec($newblock_str) - 12;
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
                //blockNumber截取前两位0x
                $trade_info["blockNumber"] = substr($trade_info["blockNumber"],2);
                //16进制 转换为10进制 后 -12块获取最新块
                $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);
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