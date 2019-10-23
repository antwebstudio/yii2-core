<?php
namespace ant\widgets;

use yii\helpers\Html;

class MultipleChoice extends \yii\widgets\InputWidget {
	public $model;
	public $attribute;
	public $data;
	public $options;
	
	public $otherLabel = 'Other: ';
	public $showOther = false;
	
	public function run() {
		return Html::activeCheckboxList($this->model, $this->attribute, $this->data, $this->options).$this->renderOther();
	}
	
	public function renderOther() {
		if (!$this->showOther) return;
			
		$otherCheckboxValue = Html::getAttributeValue($this->model, $this->attribute.'[other]');
		
		return Html::activeCheckbox($this->model, $this->attribute.'[other]', [
			'value' => trim($otherCheckboxValue) != '' ? $otherCheckboxValue : 1,
			'label' => $this->otherLabel.' '.Html::activeTextInput($this->model, $this->attribute.'[other]'),
		]);
	}
}