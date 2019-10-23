<?php

namespace ant\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class ConfigurableModuleBehavior extends Behavior {
	public $types = [];
    protected $_formModel = [];

    public function setFormModels(array $configs) {
        foreach ($configs as $name => $config) {
            $this->_formModel[$name] = $config;
        }
    }
	
	public function getTypes($groupName = 'default') {
		return $this->types[$groupName];
	}

    public function getFormModel($name = 'default', $configs = []) {
        if (!isset($this->_formModel[$name]) || !is_array($this->_formModel[$name])) {
			$formModels = $this->owner->formModels();
			
			if (!isset($formModels[$name]) || !is_array($formModels[$name])) {
				throw new \Exception('Invalid form model config: "'.$name.'"');
			} else {
				$config = $formModels[$name];
			}
		} else {
			$config = $this->_formModel[$name];
		}

        $config = ArrayHelper::merge($config, $configs);
        return Yii::createObject($config);
    }
	
	public function configureModel($model, $name = 'default', $configs = []) {
        if (!is_array($this->_formModel[$name])) throw new \Exception('Invalid form model config: "'.$name.'"');

        $config = ArrayHelper::merge($this->_formModel[$name], $configs);
		ArrayHelper::remove($config, 'class');
        return Yii::configure($model, $config);
		
	}
	
	public function formModels() {
		return [];
	}
}