<?php
namespace api\modules\user\controllers;

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
}