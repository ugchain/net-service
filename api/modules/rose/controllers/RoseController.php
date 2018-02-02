<?php

namespace api\modules\medal\controllers;

use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;
use api\modules\rose\models\Rose;
use api\modules\rose\models\RoseGive;

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
    public function actionCreateRose()
    {
        $model = new Rose();
        //主题ID
        $model->theme_id = Yii::$app->request->post("theme_id","1");
        //勋章名称
        $model->rose_name	 = Yii::$app->request->post("rose_name");
        //刻字内容
        $model->theme_name = Yii::$app->request->post("theme_name","");
        //勋章材质
        $model->material_type = Yii::$app->request->post("material_type","5");
        //价格
        $model->amount = Yii::$app->request->post("amount");
        //地址
        $model->address = Yii::$app->request->post("address");
        //判断参数
        if(!$model->rose_name || !$model->amount || !$model->address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
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
        $model->status = RoseGive::SUCCESS;
        $model->token_id = base64_encode(time().rand(0,9));
        //保存玫瑰表
        $status = $model->save();
        if(!$status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //初始化玫瑰转赠表
        $give_status = RoseGive::insertData($model->address,$model->id,$model->address,RoseGive::SUCCESS);
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
        $address = Yii::$app->request->post("address");
        //页数
        $page = Yii::$app->request->post("page",Yii::$app->params['pagination']['page']);
        //展示数量
        $pageSzie = Yii::$app->request->post("pageSzie",Yii::$app->params['pagination']['pageSize']);
        //查询列表
        $medal_list = Rose::getList($address,$page,$pageSzie);
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
        $medal_id = Yii::$app->request->post("medal_id");
        //page
        $page = Yii::$app->request->post("page", Yii::$app->params['pagination']['page']);
        //pageSize
        $pageSize = Yii::$app->request->post("pageSize", Yii::$app->params['pagination']['pageSize']);
        //判断是否为空 && 是否是数字
        if(!$medal_id || !is_numeric($medal_id)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
//        //查询玫瑰基本数据
//        $medal_base_info = Medal::getInfoById($medal_id);
//        //查询玫瑰转增记录
        $medal_trade_info = RoseGive::getMedalGiveInfoByMedalId($medal_id, $page, $pageSize);
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
        $medal_id = Yii::$app->request->post("medal_id", "");
        //接收勋章者
        $recipient_address = Yii::$app->request->post("recipient_address", "");

        //校验持有者是否真实持有勋章
        if (empty(Rose::getMedalOwner($address, $medal_id))) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_INFO_ERROR);
        }

        //更新勋章持有者
        if (!Rose::updateMedalOwner($address, $medal_id, $recipient_address)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_UPDATE_ERROR);
        }
        //赠送记录
        if (RoseGive::insertData($address, $medal_id, $recipient_address, RoseGive::SUCCESS)) {
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
        $result = RoseGive::getList($address, $page, $pageSize);
        $result['image_url'] = Yii::$app->params['image_url'];
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, $result);

    }
}