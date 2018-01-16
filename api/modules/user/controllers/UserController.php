<?php
namespace api\modules\user\controllers;


use Yii;
use yii\web\Controller;

class UserController extends  Controller
{

    public $enableCsrfValidation = false;
    public function actionTest()
    {
        echo "user Test";
    }
}