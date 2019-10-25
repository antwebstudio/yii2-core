<?php
namespace ant\moduleManager\backend\controllers;

class DefaultController extends \yii\web\Controller {
	public function actionIndex() {
		return $this->render($this->action->id);
	}
}