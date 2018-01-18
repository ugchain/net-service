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

    public function options($actionID)
    {
        return [
            "coin_name",
            "coin_type"
        ];
    }

    public function optionAliases()
    {
        return [
            'c' => 'coin_name',
            'y' => 'coin_type',
        ];
    }

    /**
     * console of eth-listen/listen-txid 根据txid获取blocknumber and gas_price
     * @return string
     */
    public function actionListenTxid()
    {
        echo "开始";
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
            //
            $current_data = ["jsonrpc"=>"2.0","method"=>"eth_getTransaction","params"=>[$list["app_txid"]],"id"=>"1"];
            $block_info = CurlRequest::curl(Yii::$app->params["eth_host"],$current_data);
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
            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($trade_info["blockNumber"],$trade_info["gasPrice"])){
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
        $data = ["jsonrpc"=>"2.0","method"=>"eth_blockNumber","params"=>[],"id"=>"1"];
        $new_block_data = CurlRequest::curl(Yii::$app->params["eth_host"],$data);
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
            if($newblock < $v["blocknumber"] + 12){
                continue;
            }
            $data = ["jsonrpc"=>"2.0","method"=>"eth_getTransactionReceipt","params"=>[$v['app_txid']],"id"=>"1"];
            $txid_info = CurlRequest::curl(Yii::$app->params["eth_host"],$data);
            if(!$txid_info){
                continue;
            }
            $txid_info = json_decode($txid_info,true);
            if(isset($txid_info["error"])){
                continue;
            }
            $gas_used = $txid_info["result"]["gasUsed"];
            //通知owners--暂时不写
            $owner_status = "1";
            if(!$owner_status){
                continue;
            }
            //更新数据库
            if(!CenterBridge::updateGasUsedAndStatusAndTime($v["app_txid"],$txid_info["result"]["gasUsed"],CenterBridge::SEND_SUCCESS)){
                echo "更新数据库失败";
                continue;
            }
        }
        echo "更新成功!";
    }

}