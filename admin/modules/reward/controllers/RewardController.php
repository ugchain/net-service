<?php

namespace admin\modules\reward\controllers;

use Yii;
use yii\web\Controller;
use admin\modules\reward\models\reward;

class RewardController extends  Controller
{
    public $enableCsrfValidation = false;
    /**
     * 添加to地址
     */
    public function actionIndex()
    {
        if(Yii::$app->request->isPost){
            $tos = Yii::$app->request->post('to');
            $data = explode(' ',$tos);
            reward::saveReward($data);
        }
        return $this->render('index.html');
    }

}