<?php

namespace api\modules\rose\controllers;

use api\modules\rose\models\RoseTheme;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;
use yii\web\UploadedFile;
use api\modules\rose\models\Rose;
use api\modules\rose\models\RoseGive;

class RoseController extends  Controller
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
     * 创建玫瑰
     */
    public function actionCreateRose()
    {
        $Rose = new Rose();
        //主题ID
        $Rose->theme_id = Yii::$app->request->post("theme_id","1");
        //勋章名称
        $Rose->rose_name	 = Yii::$app->request->post("rose_name");
        //刻字内容
        $Rose->theme_name = Yii::$app->request->post("theme_name","");
        //勋章材质
        $Rose->material_type = Yii::$app->request->post("material_type","5");
        //价格
        $Rose->amount = Yii::$app->request->post("amount");
        //地址
        $Rose->address = Yii::$app->request->post("address");
        //判断参数
        if(!$Rose->theme_id || !$Rose->rose_name || !$Rose->amount || !$Rose->address){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
        //获取主题图片
        $theme_info = RoseTheme::getInfoById($Rose->theme_id);
        if(!$theme_info){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::ROSE_THEME_NOT_EXISTS);
        }
        $Rose->theme_img = $theme_info["img"];
        $Rose->theme_thumb_img = $theme_info["thumb_img"];
        $Rose->addtime = time();
        $Rose->status = RoseGive::SUCCESS;
        $Rose->token_id = OutputHelper::guid();
        //保存玫瑰表
        $status = $Rose->save();
        if(!$status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        //初始化玫瑰转赠表
        $give_status = RoseGive::insertData($Rose->address,$Rose->id,$Rose->address,RoseGive::SUCCESS);
        if(!$give_status){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::FALL);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
    }

    /**
     * 玫瑰-我的资产
     * @param address 地址
     * @return json
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
     * 玫瑰详情
     * @param rose_id 玫瑰ID
     */
    public function actionRoseDetail()
    {
        //玫瑰ID
        $rose_id = Yii::$app->request->post("rose_id");
        //page
        $page = Yii::$app->request->post("page", Yii::$app->params['pagination']['page']);
        //pageSize
        $pageSize = Yii::$app->request->post("pageSize", Yii::$app->params['pagination']['pageSize']);
        //判断是否为空 && 是否是数字
        if(!$rose_id || !is_numeric($rose_id)){
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }
//        //查询玫瑰基本数据
//        $medal_base_info = Medal::getInfoById($medal_id);
//        //查询玫瑰转增记录
        $medal_trade_info = RoseGive::getMedalGiveInfoByRoseId($rose_id, $page, $pageSize);
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
     * 玫瑰转赠
     */
    public function actionRoseGive()
    {
        //持有者
        $address = Yii::$app->request->post("owner_address", "");
        //玫瑰id
        $rose_id = Yii::$app->request->post("rose_id", "");
        //接收玫瑰者
        $recipient_address = Yii::$app->request->post("recipient_address", "");

        //校验持有者是否真实持有玫瑰
        if (empty(Rose::getMedalOwner($address, $rose_id))) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_INFO_ERROR);
        }

        //更新玫瑰持有者
        if (!Rose::updateMedalOwner($address, $rose_id, $recipient_address)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_UPDATE_ERROR);
        }
        //赠送记录
        if (RoseGive::insertData($address, $rose_id, $recipient_address, RoseGive::SUCCESS)) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS);
        }
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::MEDAL_GIVE_ADD_FAILED);
    }

    /**
     * 玫瑰交易历史
     */
    public function actionRoseHistory()
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