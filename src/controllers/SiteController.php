<?php
namespace ant\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'timezone' => [
                'class' => 'yii2mod\timezone\TimezoneAction',
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
			'contact' => [
				'class' => 'ant\support\actions\CreateAction',
			],
        ];
    }

    public function actionPage($page) {
        return $this->render($page);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
		return $this->render('index');
    }
	
	public function actionLanguage($language = 'en') {
		\Yii::$app->session['language'] = $language;
		\Yii::$app->language = $language;
		return $this->redirect(\Yii::$app->request->referrer);
	}
	
	public function actionRedirect($url) {
		return $this->redirect($url);
	}
}
