<?php
namespace console\controllers;

use common\helpers\OutputHelper;
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
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>1]));

        //查询数据信息待确认状态
        $unsucc_info = CenterBridge::getListByTypeAndStatus(CenterBridge::UG_ETH);
        if(!$unsucc_info){
            OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
            echo "暂无需要确认的数据";die();
        }
        //获取gas_price
        $gas_info = ExtraPrice::getList();
        $gas_price = $gas_info['gas_min_price'];
        foreach ($unsucc_info as $list)
        {
            $block_info = CurlRequest::ChainCurl(Yii::$app->params["ug_host"],"eth_getTransactionReceipt",[$list["app_txid"]]);
            if(!$block_info){
                continue;
            }
            $block_info = json_decode($block_info,true);
            if(isset($block_info["error"])){
                continue;
            }
            //todo 1:签名服务器做签名(返回txid) 2:去eth链上转账操作 3:更新数据库 status=3&&blockNumber&&owner_txid&&block_send_succ_time

            //获取nonce值 --暂时不获取
            $send_sign_data = [
                "address"=>$list["address"],
                "value"=>$list["amount"],
                "gasPrice"=>$gas_price,
                "gas"=>"20",
                "nonce"=>"111"
            ];
            $sign_res = CurlRequest::curl(Yii::$app->params["sign_host"]."/ethSign",$send_sign_data);
            if(!$sign_res){
                continue;
            }
            $sign_res_data = json_decode($sign_res,true);
            //eth链上广播交易
            $ug_res = CurlRequest::ChainCurl(Yii::$app->params["eth_host"],"eth_sendRawTransaction",["data"=>$sign_res_data["row_transaction"]]);
            if(!$ug_res){
                continue;
            }
            //返回owner_txid
            $ug_res_data = json_decode($ug_res,true);
            //blockNumber截取前两位0x && 16进制 转换为10进制

            $trade_info = OutputHelper::substrHexdec($block_info["result"], 1);

            //更新数据库
            if(!CenterBridge::updateBlockAndOwnerTxid($list["app_txid"],$trade_info["blockNumber"],$ug_res_data["result"])){
                OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
                echo "更新数据库失败";
                continue;
            }
        }
        OutputHelper::writeLog(Yii::$app->getRuntimePath() . '/uglisten.log',json_encode(["status"=>0]));
        echo "UG转账ETH结束".time();
    }
}