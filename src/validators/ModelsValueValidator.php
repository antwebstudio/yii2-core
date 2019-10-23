<?php
namespace ant\validators;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;

class ModelsValueValidator extends Validator {
	
/* 
		rules is array;
		['attributename'] => [rule];
		models can be model->models[index]
		
        $rules[] = ['models', \ant\validators\ModelsValueValidator::className(), 
            'rules' => $modelsRules,
            or 'rules' =>
            			[
            			['0'] => [
		                        [['firstname'], 'required'], 
		                        [['firstname'], 'string', 'max' => 255],         
		                        [['firstname'], 'email'],
	                        ],
                        ],
                        ['1'] => [
		                        [['a'], 'required'], 
		                        [['b','firstname'], 'string', 'max' => 255],         
		                        [['c'], 'email'],
	                        ],
                        ],
                    << currently using
                        ['firstname'] => [
		  <execute this threee> [['value'], 'required'], 
		                        [['value'], 'string', 'max' => 255],         
		                        [['value'], 'email'],
	                        ],
                        ],
                        ['lastname'] => [
		  <execute this threee> [['value'], 'required'], 
		                        [['value'], 'string', 'max' => 255],         
		                        [['value'], 'email'],
	                        ],
                        ],
                    >>
            'attributeNames' => $userConfigNames,
            or => [ 'firstname', 'lastname' ]

            count($models) == count($rules)
        ];
	*/
	public $message;

	public $attributeNames = [];

	public $rules = []; 
	// ['firstname'] => [
		// [['value'], 'required'], 
		// [['value'], 'string', 'max' => 255],         
		// [['value'], 'email'],
		// ],
    // ],

	public $modelTobeCheck = 'models'; 
	//example array throw in, models['model']['value']
	//mean check model attribute, and use value as rules name

	public $errorKey = 'key'; 
	// addError key, use which layer, profile['data']['firstname'] => 
	//currently key and key2 available
	// data is key, firstname is key2

	public function init() {
		parent::init();
		
		if ($this->message === null) {
			$this->message = \Yii::t('yii', '{attribute} is invalid.');
		}
	}

	public function validateAttribute($model, $attribute) {
		if ($this->rules != null) {
			foreach ($this->attributeNames as $key => $value) {
					$tempModel = \yii\base\DynamicModel::validateData($model->{$this->modelTobeCheck}[$key], $this->rules[$value]);
					if (!$tempModel->validate()){
						foreach ($tempModel->errors as $key2 => $message) {
							// first str_replace : ucwords(attribute)

							// second replace : name of active record attribute is xxx_xxx
							// so the message message suppose showing Xxx xxx
							$message = str_replace(ucwords(key($model->{$this->modelTobeCheck}[$key])), ucwords(str_replace('_', ' ', $value)), $message[0]);
							$model->addError(${$this->errorKey}, $message);
						}
					}

			}
		}
		return null;
	}
}
