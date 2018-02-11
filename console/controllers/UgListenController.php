<?php
namespace console\controllers;

use api\modules\user\models\Trade;
use common\helpers\OutputHelper;
use api\modules\redpacket\models\RedPacket;
use api\modules\redpacket\models\RedPacketRecord;
use common\wallet\Operating;
use common\models\CenterBridge;
use common\models\ExtraPrice;
use function GuzzleHttp\Psr7\str;
use Yii;
use yii\console\Controller;
use common\helpers\CurlRequest;
use api\modules\redpacket\models\PacketOfflineSign;
/**
 * Class UgLisenController By ug-eth监听确认服务
 * @package console\controller
 */
class UgListenController extends Controller
{

    /**
     * console of ug-listen/listen-txid 根据txid获取blocknumber and gas_price 状态改为3
     * @return string
     */
    public function actionListenTxid()
    {
        echo "UG转账ETH开始".time().PHP_EOL;
        //读取日志文件
        //OutputHelper::readLog(dirname(__DIR__). "/locklog/ugListen.log");

        //写入执行状态status为1
        //OutputHelper::writeLog(dirname(__DIR__). "/locklog/ugListen.log",json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库中待确认信息
        $unsucc_info = Operating::getUnconfirmedList(CenterBridge::UG_ETH, __DIR__ . '/ugListen.log');
        if (!$unsucc_info) {
            //OutputHelper::writeLog(dirname(__DIR__). "/locklog/ugListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无交易数据！".PHP_EOL;die;
        }
        //获取gas_price
        $gas_info = ExtraPrice::getList();
        $ug_free_rate = $gas_info['ug_extra_free'];
        foreach ($unsucc_info as $list)
        {
            //根据交易id获取订单信息
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params['ug']["ug_host"], "eth_getTransactionByHash", [$list["app_txid"]]);
            //写log
            OutputHelper::log("ug-eth转账获取订单信息脚本: " . $list["app_txid"] . "--链上返回信息: " . json_encode($block_info),"cross_chain");
            if (!$block_info) {
                continue;
            }
            //多次判断是否上块
            $receipt_info = CurlRequest::ChainCurl(Yii::$app->params['ug']["ug_host"],"eth_getTransactionReceipt",[$list["app_txid"]]);
            //写log
            OutputHelper::log("ug-eth转账确认上块脚本: " . $list["app_txid"] . "--链上返回信息: " . $receipt_info,"cross_chain");
            if(!$receipt_info){
                continue;
            }
            $receipt_info = json_decode($receipt_info,true);
            //代表上链失败
            if($block_info["gas"] == $receipt_info["result"]["gasUsed"]){
                //直接更新数据库块上失败
                CenterBridge::updateFallByStatus($list["app_txid"], CenterBridge::FAILED_BLOCK);
                continue;
            }
            //获取ug_free
            $ug_free = number_format(hexdec(substr($block_info["input"],10)),"0",",","");
            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($block_info);

            //todo 1:签名服务器做签名(返回txid) 2:去eth链上转账操作 3:更新数据库 status=3&&blockNumber&&owner_txid&&block_send_succ_time
            //gasPrice
            $gas_price = (int)ceil($ug_free * $ug_free_rate / Yii::$app->params["eth"]["gas_limit"]);
            //获取nonce值且组装数据
            $send_sign_data = Operating::getNonceAssembleData($list, $gas_price, Yii::$app->params["eth"]["eth_host"], "eth_getTransactionCount", [Yii::$app->params["eth"]["owner_address"], "pending"]);
            if (!$send_sign_data) {
                continue;
            }

            //根据组装数据获取签名且广播交易
            $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["eth"]["eth_sign_url"], $send_sign_data, Yii::$app->params["eth"]["eth_host"], "eth_sendRawTransaction");
            if (!$res_data || isset($res_data['error'])) {
                continue;
            }
            //更新数据库
            if(!CenterBridge::updateBlockAndOwnerTxidAndStatusAndUgFree($list["app_txid"], $trade_info["blockNumber"], $res_data["result"], $ug_free, CenterBridge::SEND_SUCCESS)){
                echo "更新数据库失败".PHP_EOL;
                continue;
            }
        }

        //OutputHelper::writeLog(dirname(__DIR__). "/locklog/ugListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "UG转账ETH结束".time().PHP_EOL;
    }

    /**
     * 检查Ug内部交易转账（内部交易转账、创建红包、兑换红包、退还红包）
     * 根据txid到链上获取交易信息，获取blocknumber
     * 更新数据库blocknumber && status && trade_time
     */
    public function actionListenTradeTxid()
    {
        echo "UG内部转账开始".time().PHP_EOL;
        //读取日志文件
        //OutputHelper::readLog(dirname(__DIR__) . "/locklog/ugTradeListen.log");

        //写入执行状态status为1
        //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库中待确认信息
        $unsucc_info = Trade::getInfoByStatus(Trade::CONFIRMED);
        if (!$unsucc_info) {
            //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无交易数据！".PHP_EOL;die;
        }

        foreach ($unsucc_info as $info) {
            //根据交易id获取订单信息
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params['ug']["ug_host"],
                "eth_getTransactionReceipt", [$info["app_txid"]]);
            //写log
            OutputHelper::log("UG内部转账脚本: " . $info["app_txid"] . "--链上返回信息: " . json_encode($block_info),"internal_transfer");
            if (!$block_info) {
                continue;
            }

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($block_info);

            //更新数据库
            if(!Trade::updateBlockAndStatusBytxid($info["app_txid"], $trade_info["blockNumber"], Trade::SUCCESS)){
                echo "更新数据库交易表失败".PHP_EOL;
                continue;
            }

            /**
             * 根据type更新表，记录类型；0内部交易转账；1创建红包交易；2拆红包交易转账；3退换红包交易转账
             * 根据txid，更新status、time
             */
            if (!Operating::updateDataBytxid($info['type'], $info["app_txid"])) {
                echo "更新数据库失败".PHP_EOL;
                continue;
            }
        }

        //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "UG内部转账结束".time().PHP_EOL;
    }

    /**
     * 确认eth-ug 监听owner发送成功未上链的信息
     * 根据txid到链上获取交易信息，获取blocknumber
     * 更新数据库to_block && status && block_listen_succ_time
     */
    public function actionListenEthUgConfirm()
    {
        echo "ETH-UG确认开始".time().PHP_EOL;
        //获取数据库中eth-ug发送成功的信息
        $info = CenterBridge::find()->where(["type"=>"1","status"=>"3"])->asArray()->all();
        if(!$info){
            echo "暂无信息".PHP_EOL;die();
        }
        foreach ($info as $v)
        {
            //根据交易id获取订单信息
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params['ug']["ug_host"], "eth_getTransactionByHash", [$v["owner_txid"]]);
            //写log
            OutputHelper::log("ETH-UG确认脚本: " . $v["owner_txid"] . "--链上返回信息: " . json_encode($block_info),"cross_chain");
            if (!$block_info) {
                echo "监听失败".PHP_EOL;
                continue;
            }
            //多次判断是否上块
            $receipt_info = CurlRequest::ChainCurl(Yii::$app->params['ug']["ug_host"],"eth_getTransactionReceipt",[$v["owner_txid"]]);
            //写log
            OutputHelper::log("ETH-UG多次确认脚本: " . $v["owner_txid"] . "--链上返回信息: " . $receipt_info,"cross_chain");
            if(!$receipt_info){
                echo "监听确认失败".PHP_EOL;
                continue;
            }
            $receipt_info = json_decode($receipt_info,true);
            //代表上链失败
            if($block_info["gas"] == $receipt_info["result"]["gasUsed"]){
                //直接更新数据库块上失败
                CenterBridge::updateFallByStatus($v["app_txid"], CenterBridge::LISTEN_CONFIRM_FAILED);
                echo "ug链上监听失败".PHP_EOL;
                continue;
            }
            $trade_info = Operating::substrHexdec($block_info);

            //更新数据库
            if(!CenterBridge::updateAll(["status"=>CenterBridge::LISTEN_CONFIRM_SUCCESS,"block_listen_succ_time"=>time(),"to_block"=>$trade_info["blockNumber"]],["owner_txid"=>$v["owner_txid"]])){
                echo "更新数据库失败".PHP_EOL;
                continue;
            }
            echo "更新数据库成功".PHP_EOL;
        }
        echo "ETH转账UG确认结束".time().PHP_EOL;
    }

    /**
     * 检查红包超过24小时后过期操作
     * 1.查询数据库状态为(2)创建成功的数据，获取create_succ_time
     * 2.create_succ_time + 24小时 < time() 过期，修改红包表和红包记录表状态为已过期
     */
    public function actionListenRedPacketCreateSuccTime()
    {
        echo "红包监听过期开始".time().PHP_EOL;

        //获取数据库中红包创建成功的数据
        $unsucc_info = RedPacket::getRedPacketListByStatus();
        if (!$unsucc_info) {
            //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无红包数据！".PHP_EOL;die;
        }

        foreach ($unsucc_info as $info) {
            //create_succ_time + 86400 < time() 过期
            if (($info['create_succ_time'] + 86400) <= time()) {
                //检索该红包是否存在记录
                $list = RedPacketRecord::find()->where(['rid' => $info['id']])->asArray()->all();//->andWhere(['!=', 'status', RedPacketRecord::EXCHANGE_SUCC])->asArray()->all();
                if (count($list) > 0) {
                    //查询已兑换的钱数
                    $count_exchange = RedPacketRecord::find()->select("sum(amount) as amount")->where(['rid' => $info['id'],"status"=>RedPacketRecord::EXCHANGE_SUCC])->asArray()->one();
                    //退还过期红包金额给发红包账户
                    $amount = OutputHelper::NumToString($info["amount"]);
                    if($count_exchange && $count_exchange["amount"] != 0){
                        $exchange = OutputHelper::NumToString($count_exchange["amount"]);
                        $amount = OutputHelper::NumToString($info["amount"] - $exchange);
                    }
                    $result =[
                        "app_txid" => $info["txid"],
                        "address" => $info["address"],
                        "amount" =>$amount,
                    ];
                    //组装签名所需数据
                    $send_sign_data = Operating::getNonceAssembleData($result, Yii::$app->params["ug"]["gas_price"], Yii::$app->params["ug"]["ug_host"], "eth_getTransactionCount", [Yii::$app->params["ug"]["red_packet_address"], "pending"]);
                    //根据组装数据获取签名且广播交易
                    $res_data = Operating::getSignatureAndBroadcast(Yii::$app->params["ug"]["ug_sign_red_packet"], $send_sign_data, Yii::$app->params["ug"]["ug_sign_red_packet"], "eth_sendRawTransaction");
                    if (!$res_data || isset($res_data['error'])) {
                        echo "广播交易失败".PHP_EOL;
                        continue;
                    }
                    //根据红包id，更新红包表状态为过期，修改退还金额
                    if (!RedPacket::updateAll(["back_amount" => $amount, "status" => RedPacket::REDPACKET_EXPIRED], ['id' => $info['id']])) {
                        echo "更新数据库红包表失败".PHP_EOL;
                        continue;
                    }
                    //根据红包id，更新红包记录表状态为已过期
                    if(RedPacketRecord::find()->where(["status" => [RedPacketRecord::RECEIVED,RedPacketRecord::EXCHANGE_FAIL]])->asArray()->all()){
                        if (!RedPacketRecord::updateAll(["status" => RedPacketRecord::EXPIRED], "rid = " . $info['id'] . " and status != " . RedPacketRecord::EXCHANGE_SUCC)) {
                            echo "更新数据库红包记录表失败".PHP_EOL;
                            //continue;
                        }
                    }
                    //根据txid去块上确认
                    $trade_info['blockNumber'] = 0;
                    $tradeStatus = Trade::CONFIRMED;
                    $trade_info = Operating::txidByTransactionInfo(Yii::$app->params["ug"]["ug_host"], "eth_getTransactionReceipt", [$res_data["result"]]);
                    if ($trade_info) {
                        //截取blockNumber
                        $trade_info = Operating::substrHexdec($trade_info["blockNumber"]);
                        $tradeStatus = Trade::SUCCESS;
                    }
                    $blocknumber = isset($trade_info["blockNumber"]) ? isset($trade_info["blockNumber"]) : "0";
                    //插入交易记录表
                    if (!Trade::insertData($res_data["result"], Yii::$app->params["ug"]["red_packet_address"], $info["address"], $amount, $tradeStatus, Trade::BACK_REDPACKET, $blocknumber)) {
                        echo "插入交易记录表失败".PHP_EOL;
                        continue;
                    }
                }
            }
        }
        echo "红包监听过期结束".time().PHP_EOL;
    }

}
