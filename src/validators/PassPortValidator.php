<?php
namespace ant\validators;

class PassPortValidator extends \yii\validators\Validator {
	
	public $addError = null;

	public function init() {
        parent::init();
		
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
	}
	
	public function validateValue($value) {
        $passportFormat = str_replace(' ', '', $value);
        if (strlen($passportFormat) != 8 && strlen($passportFormat) != 9 ) {
            return ['Passport Format is wrong.', []];
        }
        if (preg_match('/[^1-9]/', $passportFormat[strlen($passportFormat) -1 ]) != 0 ) {
            return ['Passport Format is wrong. Last Digit Must not be 0 or numer', []];
        }
        return null;
	}

    public function validateAttribute($model, $attribute) {
        $value = $model->{$attribute};
        $passportFormat = str_replace(' ', '', $value);
        if (strlen($passportFormat) != 8 && strlen($passportFormat) != 9 ) {
            $this->addError($model, $attribute,'Characters must be passport format.');
        }
        if (preg_match('/[^1-9]/', $passportFormat[strlen($passportFormat) -1 ]) != 0 ) {
            $this->addError($model, $attribute,'Characters must be passport format. Last Digit Must not be 0 or numer');
        }

    }
}