<?php
namespace ant\validators\malaysia;

class IcValidator extends \yii\validators\Validator {
	
	public $addError = null;

	public function init() {
        parent::init();
		
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
	}
	
	public function validateValue($value) {
		if (strlen($value) != 12 && $this->addError == null) {
			return ['Format Malaysia IC wrong.',[]];
		} else {
			$numbers = preg_match('/[^0-9]/', $value);
			if ($numbers == 0) {
				return null;
			} else {
				return ['Format Malaysia IC wrong.',[]];
			}
		}
	}

	public function validateAttribute($model, $attribute) {
        $value = $model->{$attribute};
        
        if (strlen($value) != 12) {
            $this->addError($model, $attribute,'Format Malaysia IC wrong. 12 characters');
		} else {
			$numbers = preg_match('/[^0-9]/', $value);
			if ($numbers == 0) {
			} else {
				$this->addError($model, $attribute,'Format Malaysia IC wrong. Numberic only');
			}
		}
    }
}