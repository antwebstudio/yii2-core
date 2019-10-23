<?php
namespace ant\behaviors;

use yii\helpers\ArrayHelper;

class DynamicAttribute extends \yii\base\Behavior {
	public $getter;
	public $attributes = [];
	public $rules;
	public $attributeLabels;
	public $formAttributes;
	public $throwExceptionIfNotDefined = true;
	
	protected $_value = [];

	public function init() {
		if (!is_array($this->attributes)) {
			throw new \Exception('Property attributes must be an array. ');
		}
	}
	
	public function getDynamicSafeAttributes() {
		$safe = [];
		foreach ($this->attributes as $key => $value) {
			if (!is_callable($value)) {
				$safe[] = is_int($key) ? $value : $key;
			}
		}
		return $safe;
	}
	
	public function getDynamicAttributeRules() {
		$rules = [];
		
		if (isset($this->rules)) {
			$rules = is_callable($this->rules) ? call_user_func_array($this->rules, [$this]) : $this->rules;
			return $rules;
		}
		return [];
	}
	
	public function getDynamicAttributeLabels() {
		return isset($this->attributeLabels) ? $this->attributeLabels : [];
	}
	
	public function getDynamicAttribute($name) {
		if (isset($this->getter) && is_callable($this->getter)) {	
			return call_user_func_array($this->getter, [$this->owner, $name]);
		} else if (isset($this->attributes[$name]) && is_callable($this->attributes[$name])) {
			return call_user_func_array($this->attributes[$name], [$this->owner]);
		} else if ($this->hasDynamicAttribute($name)) {
			return isset($this->_value[$name]) ? $this->_value[$name] : null;
		}
		
		if ($this->throwExceptionIfNotDefined) throw new \Exception('Dynamic attribute "'.$name.'" is not exist. ');
	}

	public function getDynamicAttributes() {

		$attributes = [];
		foreach ($this->attributes as $key => $name) 
		{
			if (!is_string($name) && is_callable($name)) {
				 $attributes[$key] = call_user_func_array($name, [$this]);
				 
			} else if ($this->hasDynamicAttribute($name)) {
				//throw new \Exception(print_r($name,1));
				
				$attributes[$name] = isset($this->_value[$name]) ? $this->_value[$name] : null;
			}
		}

		return $attributes;
	}
	
	public function getDynamicFormAttributes($returnAttributes = null) {
		$returnAttributes = (array) $returnAttributes;
		
		$attributes = $this->formAttributes;
		
		if (isset($returnAttributes)) {
			return \yii\helpers\ArrayHelper::filter($attributes, $returnAttributes);
		} else {
			return $attributes;
		}
	}
	
	public function setDynamicAttribute($name, $value) {
		if ($this->hasDynamicAttribute($name)) {
			$this->_value[$name] = $value;
		}
	}
	
	public function hasDynamicAttribute($name) {
		return in_array($name, $this->attributes) || array_key_exists($name, $this->attributes);
	}
}