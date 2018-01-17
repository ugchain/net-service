<?php
namespace api\modules\medal\controllers;


use api\modules\medal\models\Medal;
use Yii;
use yii\web\Controller;
use common\helpers\OutputHelper;

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
        $medal_list = Medal::getList($address,$page,$pageSzie);
        $medal_list['page'] = $page;
        $medal_list['pageSize'] = $pageSzie;
        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS,$medal_list);
    }

    /**
     * 勋章详情
     * @param medal_id 勋章ID
     */
    public function actionMedalDetail()
    {
        //勋章ID
        $medal_id = Yii::$app->request->post("medal");
        //判断是否为空
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
        $address = Yii::$app->request->post("owner_address", "");

        //校验持有者是否真实持有勋章

        //校验接收勋章者是否存在

    }
}