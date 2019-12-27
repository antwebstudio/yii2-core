<?php
namespace ant\widgets;

use Yii;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Json;

class ActiveForm extends \yii\widgets\ActiveForm
{
	public static function begin($config = [])
	{
		if (isset(Yii::$app->params['bsVersion']) && Yii::$app->params['bsVersion'] >= 4) {
			return \yii\bootstrap4\ActiveForm::begin();
		} else {
			return \yii\bootstrap\ActiveForm::begin();
		}
	}
	
	public static function end()
	{
		if (isset(Yii::$app->params['bsVersion']) && Yii::$app->params['bsVersion'] >= 4) {
			return \yii\bootstrap4\ActiveForm::end();
		} else {
			return \yii\bootstrap\ActiveForm::end();
		}
	}
}