<?php
namespace console\controllers;


use common\models\CenterBridge;
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
        echo "开始";
        //读取日志文件
        $ethlisten = file_get_contents(Yii::$app->getRuntimePath() . '/uglisten.log');
        $ethlistenlog = json_decode($ethlisten,true);
        if($ethlistenlog["status"] != 0){
            echo "正在执行中";die();
        }
        //写入执行状态status为1
        file_put_contents(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>1]));
        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus("2");
        if(!$unsucc_info){
            echo "暂无需要确认的数据";die();
        }
        foreach ($unsucc_info as $list)
        {
            $block_info = CurlRequest::EthCurl("eth_getTransactionReceipt",[$list["app_txid"]]);
            if(!$block_info){
                continue;
            }
            $block_info = json_decode($block_info,true);
            if(isset($block_info["error"])){
                continue;
            }
            //todo 1:签名服务器做签名 2:去eth链上转账操作返回txid后 3:更新数据库 status=3&&blockNumber&&owner_txid&&block_send_succ_time
            $owner_data = ["status"=>1,"owner_txid"=>"1111111"];
            $trade_info = $block_info["result"];
            //blockNumber截取前两位0x
            $trade_info["blockNumber"] = substr($trade_info["blockNumber"],2);
            //16进制 转换为10进制 后 -12块获取最新块
            $trade_info["blockNumber"] = hexdec($trade_info["blockNumber"]);

            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($list["app_txid"],$trade_info["blockNumber"],$trade_info["gasPrice"])){
                echo "更新数据库失败";
                continue;
            }
        }
        file_put_contents(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
        echo "更新结束";
    }
}