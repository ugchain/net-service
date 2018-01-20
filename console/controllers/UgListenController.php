<?php
namespace console\controllers;

use common\helpers\OutputHelper;
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
        echo "UG转账ETH开始".time();
        //读取日志文件
        OutputHelper::readLog(Yii::$app->getRuntimePath() . "/uglisten.log");

        //写入执行状态status为1
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>1]));

        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus(CenterBridge::UG_ETH);
        if(!$unsucc_info){
            OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
            echo "暂无需要确认的数据";die();
        }
        foreach ($unsucc_info as $list)
        {
            $block_info = CurlRequest::UgCurl("eth_getTransactionReceipt",[$list["app_txid"]]);
            if(!$block_info){
                continue;
            }
            $block_info = json_decode($block_info,true);
            if(isset($block_info["error"])){
                continue;
            }
            //todo 1:签名服务器做签名(返回txid) 2:去eth链上转账操作 3:更新数据库 status=3&&blockNumber&&owner_txid&&block_send_succ_time
            $owner_data = ["status"=>1,"owner_txid"=>"1111111"];

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = OutputHelper::substrHexdec($block_info["result"], 1);

            //更新数据库
            if(!CenterBridge::updateBlockAndGasPrice($list["app_txid"],$trade_info["blockNumber"],$trade_info["gasPrice"])){
                OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
                echo "更新数据库失败";
                continue;
            }
        }
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
        echo "UG转账ETH结束".time();
    }
}