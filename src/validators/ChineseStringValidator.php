<?php
namespace ant\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;
use SteelyWing\Chinese\Chinese;

class ChineseStringValidator extends Validator {
    public $autoConvert = true;

    public function init() {
        if (!isset($this->message)) {
            $this->message = '{attribute} must have simplified chinese word only. ';
        }
    }
    
    public function validateAttribute($model, $attribute) {
        $value =  $model->{$attribute};
        $compareValue = (new Chinese)->to(Chinese::CHS, $value);

        if ($value != $compareValue && !$this->autoConvert) {
            $model->addError($attribute, Yii::t('app', $this->message, ['attribute' => $model->getAttributeLabel($attribute)]));
        } else {        
            $model->{$attribute} = $compareValue;
        }
    }
}