<?php
namespace ant\validators;

class ArrayValidator extends \yii\validators\Validator {
	//public $skipOnEmpty = false;
	
	//public $allowFree = true;
	
	public $max;
	public $min;
	
	public function init() {
        parent::init();
		
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
	}
	
	public function validateAttribute($model, $attribute) {
        $value = (array) $model->{$attribute};
		
		if (isset($this->min) && count($value) < $this->min) {
			$this->addError($model, $attribute, \Yii::t('validator', '{attribute} should contain at least {min} items.', ['attribute' => $attribute, 'min' => $this->min]));
		}
		
		if (isset($this->max) && count($value) > $this->max) {
			$this->addError($model, $attribute, \Yii::t('validator', '{attribute} should contain at most {max} items.', ['attribute' => $attribute, 'max' => $this->max]));
		}
		
		/*if (!$this->allowFree && $value <= 0) {
            $this->addError($model, $attribute, $this->message, []);
		}*/
		
	}
}