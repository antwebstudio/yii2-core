<?php
namespace ant\validators;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\base\Model;
use \yii\validators\Validator;

class MultipleChoiceRequiredValidator extends yii\validators\RequiredValidator {

	/*public function init() {
		parent::init();
		
		if ($this->message === null) {
			$this->message = \Yii::t('yii', '{attribute} is invalid.');
		}
	}*/
	protected function validateValue($value)
    {
		if (is_array($value)) {
			foreach ($value as $v) {
				if (trim($v) !== '') {
					return null;
				}
			}
			
			// All are empty
			if ($this->requiredValue === null) {
				return [$this->message, []];
			}
			return [$this->message, [
				'requiredValue' => $this->requiredValue,
			]];
		} else {
			return parent::validateValue($value);
		}
	}
	
	/*public function validateAttribute($model, $attribute) {
		if (is_array($model->{$attribute})) {
			foreach ($model->{$attribute} as $value) {
				if (trim($value) !== '') {
					return true;
				}
			}
			$model->addError($attribute, $this->message);
		} else {
			if (!isset($model->{$attribute})) $model->{$attribute} = [];
			//throw new \Exception('Attribute "'.$attribute.'" is not an array. ');
		}
	}*/
}
