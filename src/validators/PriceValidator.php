<?php
namespace ant\validators;

class PriceValidator extends \yii\validators\Validator {
	public $skipOnEmpty = false;

	//public $pattern = '/^[0-9]{1,12}(\.[0-9]{0,4})?$/';
	
	public $allowFree = true;
	
	public function init() {
        parent::init();
		
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
	}

	public function clientValidateAttribute($model, $attribute, $view) {
		$message =  $this->formatMessage($this->message, [
			'attribute' => $model->getAttributeLabel($attribute),
		]);

		if (!$this->allowFree) {
			return '
				if (value <= 0) {
					messages.push("'.$message.'");
				} 
			';
		}
	}
	
	public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
		
		if (!$this->allowFree && $value <= 0) {
            $this->addError($model, $attribute, $this->message, []);
		}
	}
}