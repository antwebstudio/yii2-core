<?php
namespace ant\widgets;

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use ant\event\models\Organizer;

class AddNewOptionWidget extends \yii\bootstrap\Widget {
	protected $_modal;
	
	public function init() {
		
        $model = new Organizer();
		if ($model->load(\Yii::$app->request->post())) {
			if ($model->save()) {
				$model = new Organizer();
			}
		}
		
		$this->_modal = Modal::begin([
			//'toggleButton' => ['label' => 'Add Organier', 'class' => 'btn btn-default'],
		]);
		
		$form = ActiveForm::begin();
		Pjax::begin(['formSelector' => '#'.$form->id]);
		echo $form->field($model, 'name')->textInput(['maxlength' => true]);
		echo $form->field($model, 'description')->textarea();
		echo Html::submitButton('Create', ['class' => 'btn btn-primary']);
		Pjax::end();
		ActiveForm::end();
			
		Modal::end();
		
		return $this;
	}
	
	public function getModal() {
		return $this->_modal;
	}
}