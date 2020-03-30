<?php
namespace ant\validators;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\base\Model;
use \yii\validators\Validator;
use yii\validators\ValidationAsset;

class SerializableDataValidator extends Validator {
	
	// protected $_validator;

	/* 
		Example of usage:

		['data', \ant\validators\SerializableDataValidator::className(), 
		'rules' => [
			[['firstname'], 'required'], 
			[['contact','firstname'], 'string', 'max' => 255],         [['firstname'], 'email'],
		],

		NOTE:
		To activate client side validation of this validator, please set fieldClass of the active form to: ant\widgets\ActiveField
	*/
	public $message;

	public $rules = [];

	public $skipOnEmpty = false;

	public function init() {
		parent::init();
		if ($this->message === null) {
			$this->message = \Yii::t('yii', '{attribute} is invalid.');
		}
	}
	
    public function clientValidateAttribute($model, $attribute, $view, $fullAttributeName = null)
    {
		if (!isset($fullAttributeName)) throw new \Exception('ActiveField used in form do not support this validator client script. Please add [\'fieldClass\' => \'ant\\widgets\\ActiveField\'] to ActiveForm options to use this validator client script. ');
		
        ValidationAsset::register($view);
        $options = $this->getClientOptions($model, $attribute);
		
		$validators = $this->createValidators();
		
		$html = '';
		
		if (isset($fullAttributeName) && !preg_match(\yii\helpers\Html::$attributeRegex, $fullAttributeName, $matches)) {
            throw new \InvalidArgumentException('Attribute name must contain word characters only: '.$fullAttributeName);
        }
		$subAttribute = isset($matches) ? $matches[3] : $attribute;
		
		foreach ($validators as $validator) {
			foreach ($validator->attributes as $validatorAttribute) {
				if ($subAttribute == '['.$validatorAttribute.']') {
					$html .= $validator->clientValidateAttribute($model, $attribute.'['.$validatorAttribute.']', $view);
				}
			}
		}
		return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute)
    {
        $options = [];
        /*if ($this->requiredValue !== null) {
            $options['message'] = $this->formatMessage($this->message, [
                'requiredValue' => $this->requiredValue,
            ]);
            $options['requiredValue'] = $this->requiredValue;
        } else {
            $options['message'] = $this->message;
        }
        if ($this->strict) {
            $options['strict'] = 1;
        }

        $options['message'] = $this->formatMessage($options['message'], [
            'attribute' => $model->getAttributeLabel($attribute),
        ]);*/

        return $options;
    }
	
	protected function createValidators()
    {
        $validators = new ArrayObject();
        foreach ($this->rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
    }

	protected function getDynamicAttributes() {
		$dynamicAttributes = [];
		foreach ($this->rules as $rule) {
			if (is_array($rule[0])) {
				foreach ($rule[0] as $attribute) {
					$dynamicAttributes[$attribute] = null;	
				}
			} else {
				$dynamicAttributes[$rule[0]] = null;
			}
		}

		return $dynamicAttributes;
	}

	public function validateAttribute($model, $attribute) {
		if ($this->rules != null) {

		/**
			// $model->currency, but invite record does not have $model->currency
			// new strucutre added currency, old data cant resend invite
			// after update it will work properly if no this code
			// when model data value is blank for send email, will error
			// Getting unknown property: yii\base\DynamicModel::currency

			//if open code here, data empty can be resend out.
			//but when update, the user_config table will not store any data,
			// hence the data of discount_rate etc cant be update anymore after register.
		**/
		
			/**if ($model->isNewRecord == false) {
				foreach ($model->data as $key => $value) {
					if ($value == null) {
						return null;
					}
				}
			} **/

			// $c = $model->{$attribute};
			// $arr = [];
			// foreach ($this->rules as $key => $value) {
			// 	# code...

			// 	foreach ($value as $a => $b) {
			// 		echo "<pre>";
			// 		//print_r($b);
			// 		echo "</pre>";
			// 		if ($b[0] == key($c)  ) {
			// 			$arr[] = $key;
			// 		}
			// 	}
			// }
			// echo "<pre>";
			// 	print_r($arr);
			// echo "</pre>";

			// $arr2 = [];
			// foreach ($arr as $key => $value) {
			// 	$arr2[] = $this->rules[$key];
			// }

			// $this->rules = $arr2;

			if (!is_array($model->{$attribute})) {
				if (!isset($model->{$attribute})) $model->{$attribute} = [];
				//throw new \Exception('Attribute "'.$attribute.'" is not an array. ');
			}
			$tempModel = \yii\base\DynamicModel::validateData(ArrayHelper::merge($this->dynamicAttributes, (array) $model->{$attribute}), $this->rules);
			
			if ($tempModel->hasErrors()){
				foreach ($tempModel->errors as $key => $message) {
					foreach ($message as $m) {
						$model->addError('data.' . $key, $m);
					}
				}
			}
		}
	}
}
