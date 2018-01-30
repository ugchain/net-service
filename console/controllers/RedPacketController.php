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
use api\modules\user\models\Trade;
use api\modules\redpacket\models\RedPacketRecord;
/**
 * Class EthLisenController By eth-ug监听确认服务
 * @package console\controller
 */
class RedPacketController extends Controller
{

    /**
     * 检查Ug内部红包交易转账（创建红包、兑换红包、退还红包）
     * 根据txid到链上获取交易信息，获取blocknumber
     * 更新数据库blocknumber && status && trade_time
     */
    public function actionListenTxid()
    {
        echo "UG内部转账红包开始".time().PHP_EOL;
        //读取日志文件
        //OutputHelper::readLog(dirname(__DIR__) . "/locklog/ugTradeListen.log");

        //写入执行状态status为1
        //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_LOCK_STATUS]));

        //获取数据库中待确认信息
        $unsucc_info = Trade::getInfoByStatus(Trade::CONFIRMED, Trade::REDPACKET);
        if (!$unsucc_info) {
            //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
            echo "暂无红包交易数据！".PHP_EOL;die;
        }

        foreach ($unsucc_info as $info) {
            //根据交易id获取订单信息
            $block_info = Operating::txidByTransactionInfo(Yii::$app->params['ug']["ug_host"],
                "eth_getTransactionReceipt", [$info["app_txid"]]);
            if (!$block_info) {
                continue;
            }

            //blockNumber截取前两位0x && 16进制 转换为10进制
            $trade_info = Operating::substrHexdec($block_info);

            //更新数据库
            if (!Trade::updateBlockAndStatusBytxid($info["app_txid"], $trade_info["blockNumber"], Trade::SUCCESS)) {
                echo "更新数据库交易表失败".PHP_EOL;
                continue;
            }

            if (!RedPacketRecord::updateStatusByTxid($info["app_txid"], RedPacketRecord::EXCHANGE_SUCC)) {
                echo "更新数据库红包记录表失败".PHP_EOL;
                continue;
            }
        }

        //OutputHelper::writeLog(dirname(__DIR__) . "/locklog/ugTradeListen.log",json_encode(["status" => Operating::LOG_UNLOCK_STATUS]));
        echo "UG内部转账红包结束".time().PHP_EOL;
    }
}