<?php
namespace api\modules\medal\controllers;


use api\modules\medal\models\Medal;
use api\modules\medal\models\MedalGive;
use api\modules\user\models\Address;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;

class MedalController extends  Controller
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
     * 创建勋章
     */
    public function actionCreateMedal()
    {
        $model = new Medal();
        //主题ID
        $model->theme_id = Yii::$app->request->post("theme_id","1");
        //勋章名称
        $model->medal_name	 = Yii::$app->request->post("medal_name");
        //刻字内容
        $model->theme_name = Yii::$app->request->post("theme_name","");
        //勋章材质
        $model->material_type = Yii::$app->request->post("material_type","5");
        //价格
        $model->amount = Yii::$app->request->post("amount");
        //地址
        $model->address = Yii::$app->request->post("address");
        //判断参数
        if(!$model->medal_name || !$model->amount || !$model->address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //判断地址是否存在
//        $address_info = Address::getInfoByAddress($model->address);
//        if(!$address_info){
//            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ADDRESS_NOT_EXIST);
//        }
        //实例化文件
        $model->theme_img = UploadedFile::getInstanceByName('theme_img');
        $model->theme_thumb_img = UploadedFile::getInstanceByName('theme_thumb_img');
        //判断是否有图片上传
        if(!$model->theme_img || !$model->theme_thumb_img){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::UPLOAD_FILE_FALL);
        }
        //上传文件到服务器
        $model->upload();
        $model->addtime = time();
        $model->update_time = time();
        $model->status = MedalGive::SUCCESS;
        $model->token_id = OutputHelper::guid();
        //保存勋章表
        $status = $model->save();
        if(!$status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //初始化勋章转赠表
        $give_status = MedalGive::insertData($model->address,$model->id,$model->address,MedalGive::SUCCESS);
        if(!$give_status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 勋章-我的资产
     * @param address 地址
     */
    public function actionGetList()
    {
        //地址
        $address = Yii::$app->request->post("address", "");
        if (!$address) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //页数
        $page = Yii::$app->request->post("page",Yii::$app->params['pagination']['page']);
        //展示数量
        $pageSzie = Yii::$app->request->post("pageSzie",Yii::$app->params['pagination']['pageSize']);
        //查询列表
        $medal_list = Medal::getList($address,$page,$pageSzie);
        $medal_list['page'] = $page;
        $medal_list['pageSize'] = $pageSzie;
        $medal_list['image_url'] = Yii::$app->params['image_url'];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$medal_list);
    }

    /**
     * 勋章详情
     * @param medal_id 勋章ID
     */
    public function actionMedalDetail()
    {
        //勋章ID
        (int)$medal_id = Yii::$app->request->post("medal_id");
        //page
        $page = Yii::$app->request->post("page", Yii::$app->params['pagination']['page']);
        //pageSize
        $pageSize = Yii::$app->request->post("pageSize", Yii::$app->params['pagination']['pageSize']);
        //判断是否为空 && 是否是数字
        if(!$medal_id || !is_numeric($medal_id)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
//        //查询勋章基本数据
//        $medal_base_info = Medal::getInfoById($medal_id);
//        //查询勋章转增记录
        $medal_trade_info = MedalGive::getMedalGiveInfoByMedalId($medal_id, $page, $pageSize);
//        //初始化创始人地址
//        $medal_base_info["founder"] = $medal_base_info["address"];
        $give_data = [];
        //存在信息是则取第一条
        if($medal_trade_info['list']){
            //转赠历史
            foreach ($medal_trade_info['list'] as $key=>$trade_info){
                $give_data[$key]["address"] = $trade_info["to_address"];
                $give_data[$key]["addtime"] = $trade_info["addtime"];
            }
        }
        //组装数据
        $data = ["list"=>$give_data, "is_next_page" => $medal_trade_info['is_next_page'], "count" => $medal_trade_info['count'], 'page' => $page, 'pageSize' => $pageSize];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$data);
    }

    /**
     * 勋章转赠
     */
    public function actionMedalGive()
    {
        //持有者
        $address = Yii::$app->request->post("owner_address", "");
        //勋章id
        (int)$medal_id = Yii::$app->request->post("medal_id", "");
        //接收勋章者
        $recipient_address = Yii::$app->request->post("recipient_address", "");
        if (!$address || !$medal_id || !$recipient_address) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }

        //校验持有者是否真实持有勋章
        if (empty(Medal::getMedalOwner($address, $medal_id))) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_INFO_ERROR);
        }

        //更新勋章持有者
        if (!Medal::updateMedalOwner($address, $medal_id, $recipient_address)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_UPDATE_ERROR);
        }
        //赠送记录
        if (MedalGive::insertData($address, $medal_id, $recipient_address, MedalGive::SUCCESS)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_GIVE_ADD_FAILED);
    }

    /**
     * 勋章交易历史
     */
    public function actionMedalHistory()
    {
        //地址
        $address = Yii::$app->request->post("address", "");
        $page = Yii::$app->request->post("page", "1");
        $pageSize = Yii::$app->request->post("pageSize", "10");

        if(!$address) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        $result = [];
        //获取数据
        $result = MedalGive::getList($address, $page, $pageSize);
        $result['image_url'] = Yii::$app->params['image_url'];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);

    }
}