<?php

namespace ant\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class ConfigurableModelBehavior extends Behavior {
	public $modelName;
	
	// When defaultVisibility = true, those specified is NOT visible,
	// When defaultVisibility = false, those specified is visible.
	public $visibility;
	public $defaultVisibility = true;
	public $extraRules = [];
	public $extraAttributeLabels = [];
	
	public function getCombinedAttributeLabels($labels) {
		$extra = is_callable($this->extraAttributeLabels) ? call_user_func_array($this->extraAttributeLabels, [$this->owner]) : $this->extraAttributeLabels;
		return ArrayHelper::merge($labels, $extra);
	}
	
	public function getCombinedRules($rules) {
		$extraRules = is_callable($this->extraRules) ? call_user_func_array($this->extraRules, [$this->owner]) : $this->extraRules;
		return ArrayHelper::merge($rules, $extraRules);
	}
	
	public function isFieldShow($attribute) {
		$toShow = $this->defaultVisibility;
		
		if (isset($this->visibility)) {
			if (is_callable($this->visibility)) {
				$toShow = call_user_func_array($this->visibility, [$attribute]);
			} else if (is_array($this->visibility)) {
				$toShow = in_array($attribute, $this->visibility) ? !$this->defaultVisibility : $this->defaultVisibility;
			} else {
				throw new \Exception('Invalid config. ');
			}
		}
		
		return $toShow;
		
	}
}