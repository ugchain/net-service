<?php
namespace api\modules\user\controllers;


use Yii;
use yii\web\Controller;

class UserController extends  Controller
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access-behavior' => [
                'class' => 'common\behavior\AccessBehavior',//验证签名
            ]
        ];
    }
    public function actionTest()
    {
        echo "user Test";
    }
}