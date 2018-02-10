<?php
namespace api\modules\user\controllers;

use api\modules\user\models\Advertise;
use common\helpers\CurlRequest;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\user\models\Address;
use api\modules\medal\models\Medal;
use api\modules\rose\models\Rose;
use common\wallet\Operating;

class UserController extends  Controller
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access-behavior' => [
//                'class' => 'common\behavior\AccessBehavior',//验证签名
//            ]
//        ];
//    }
    /**
     * 创建/导入用户地址
     */
    public function actionCreateUser()
    {
        //昵称
        $nickname = Yii::$app->request->post("nickname","");
        //地址
        $address = Yii::$app->request->post("address","");
        //检查昵称是否超过50位
        if(strlen($nickname) > 50){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::NICKNAM_EOVERSIZE);
        }
        //检查地址位数及空
        if(!$address || strlen($address) != 42){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
        }
        //查询数据库中是否存在
        $address_info = Address::getInfoByAddress($address);
        if(!$address_info){
            //组装数据
            $data = ['nickname'=>$nickname,"address"=>$address];
            $status = Address::saveAddress($data);
            if(!$status){
                outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
            }
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
        }
        //存在时
        if($address_info['is_del'] == 0){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_EXIST);
        }
        //更新is_del
        if(!Address::updateAddressByIsDel($address)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 广告位申请
     */
    public function actionCreateAdvertise()
    {
        //手机号
        $phone = Yii::$app->request->post("phone","");
        //地址
        $address = Yii::$app->request->post("address","");

        if(!$phone || !$address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //判断手机号
        if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PHONE_WRONGFOL);
        }
        //判断是否申请过
        if(Advertise::getAdvertiseInfoByAddressAndPhone($address,$phone)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADVERTISE_EXIST);
        }
        //保存
        if(!Advertise::saveAdvertise($address,$phone)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 地址申请限制
     */
    public function actionCheckAddressAdvert()
    {
        //地址
        $address = Yii::$app->request->post("address","");

        //判断地址是否申请过
        $result['is_applied'] = 'YES';
        if (!Advertise::getAdvertiseInfoByAddress($address)) {
            $result['is_applied'] = 'NO';
        }

        //返回值
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }

    /**
     * 我的所有虚拟资产
     */
    public function actionVirtualAssetsList()
    {
        //地址
        $address = Yii::$app->request->post("address","");
        $page = Yii::$app->request->post("page","1");
        $pageSize = Yii::$app->request->post("pageSize","10");
        if(!$address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }

        //查询勋章列表
        $medal_list = Medal::getList($address, $page, $pageSize);
        //查询玫瑰列表
        $rose_list = Rose::getList($address, $page, $pageSize);

        //数组合并
        $list = array_merge($rose_list['list'], $medal_list['list']);

        if (empty($list)) {
            $result['list'] = $list;
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
        }
        //排序字段
        $sort = array(
            'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'addtime',       //排序字段
        );
        $arrSort = array();
        foreach($list AS $uniqid => $row){
            foreach($row AS $key => $value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);
        }

        foreach ($list as $k => $v) {
            $list[$k]['type'] = isset($v['medal_name'])?"1":"2";
            $list[$k]['name'] = isset($v['medal_name'])?$v['medal_name']:$v['rose_name'];
        }

        $result['list'] = $list;
        $result['image_url'] = Yii::$app->params['image_url'];

        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);

    }
    public function actionGetBalance()
    {
        echo 111;die;
        //eth owner balance
        $eth_owner_address = "d63397d3515e2748e73fcaa542444a963d7ab7ee";
        $eth_host = "http://eth.mainnet.ugchain.org";

        //eth上ugc的余额
        $eth_input = "0x70a08231000000000000000000000000".$eth_owner_address;

        //ug owner balance
        $ug_owner_address = "0x69de549161a1965102f64d07ce39e9b2780998c1";
        $ug_host = "http://ugc.mainnet.ugchain.org";

        //eth上eth的余额
        $eth_balance = "";
        $res_eth_balance = CurlRequest::ChainCurl($eth_host,"eth_getBalance",["0x".$eth_owner_address,"latest"]);

        if($res_eth_balance){
            $res_eth_balance = json_decode($res_eth_balance,true);
            $res_eth_balance = Operating::substrHexdec($res_eth_balance);
            $eth_balance = $res_eth_balance["result"];
            $eth_balance = OutputHelper::fromWei($eth_balance);
            $eth_balance = OutputHelper::NumToString($eth_balance);
            //$eth_balance = number_format($eth_balance);
        }
        //获取eth上ugc的余额
        $data = [
            "from" => "0x".$eth_owner_address,
            "to"   => "0xf485c5e679238f9304d986bb2fc28fe3379200e5",
            "data" => $eth_input,
            "gas" => "0x0",
            "gasPrice"=> "0x0",
            "value"=> "0x0"
        ];
        //get balance
        $res_ugc_balance = CurlRequest::ChainCurl($eth_host,"eth_call",[$data,'latest']);
        $eth_ugc_balance = "";
        if($res_ugc_balance){
            $res_ugc_balance = json_decode($res_ugc_balance,true);
            $res_ugc_balance = Operating::substrHexdec($res_ugc_balance);
            $eth_ugc_balance = $res_ugc_balance["result"];
            $eth_ugc_balance = OutputHelper::fromWei($eth_ugc_balance);
            $eth_ugc_balance = OutputHelper::NumToString($eth_ugc_balance);
            $eth_ugc_balance = number_format($eth_ugc_balance);
        }


        //get balance
        $res_balance = CurlRequest::ChainCurl($ug_host,"eth_getBalance",[$ug_owner_address, "latest"]);
        $ug_balance = "";
        if($res_balance){
            $res_balance = json_decode($res_balance,true);
            $res_balance = Operating::substrHexdec($res_balance);
            $ug_balance = $res_balance["result"];
            $ug_balance = OutputHelper::fromWei($ug_balance);
            $ug_balance = OutputHelper::NumToString($ug_balance);
            $ug_balance = number_format($ug_balance);
        }
        //获取以太坊块高度
        $res_eth_block = CurlRequest::ChainCurl($eth_host,"eth_blockNumber");
        $eth_block = "";
        if($res_eth_block){
            $res_eth_block = json_decode($res_eth_block,true);
            $res_eth_block = Operating::substrHexdec($res_eth_block);
            $eth_block = $res_eth_block["result"];
            $eth_block = OutputHelper::NumToString($eth_block);
            $eth_block = number_format($eth_block);
        }
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no"><title>监控余额</title></head>
<body>';
        echo "以太坊eth余额：".$eth_balance."&emsp;&emsp;&emsp;<span style='color: red'>(警戒值:小于0.5时通知钉钉群)</span><br />";
        echo "以太坊ugc余额：".$eth_ugc_balance."&emsp;&emsp;&emsp;<span style='color: red'>(警戒值:小于5000时通知钉钉群)</span><br />";
        echo "ug网络余额：".$ug_balance."&emsp;&emsp;&emsp;<span style='color: red'>(警戒值:小于50万时通知钉钉群)</span><br />";
        echo "以太坊块高度：".$eth_block;
echo "</body></html>";

    }
}