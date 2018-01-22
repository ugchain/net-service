<?php
namespace api\modules\user\controllers;

use api\modules\user\models\Advertise;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use api\modules\user\models\Address;

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
        $result = 'YES';
        if (!Advertise::getAdvertiseInfoByAddress($address)) {
            $result = 'NO';
        }


        //返回值
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);
    }
}