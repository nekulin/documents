<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

/**
 * @todo Можно подключить валидацию http://yiidreamteam.com/yii2/upload-behavior
 * Class SiteController
 *
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect('documents');
    }
}
