<?php

namespace ant\behaviors;

class LayoutBehavior extends \yii\base\Behavior {
	// When defaultVisibility = true, those specified is NOT visible,
	// When defaultVisibility = false, those specified is visible.
	public $visibility;
	public $defaultVisibility = true;
	
	public function show($name, $content = null) {
		$toShow = $this->defaultVisibility;
		$route = $this->owner->context->route;
		
		if (isset($this->visibility)) {
			if (is_callable($this->visibility)) {
				$toShow = call_user_func_array($this->visibility, [$route, $name]);
			} else if (is_array($this->visibility)) {
				$toShow = isset($this->visibility[$route]) && in_array($name, $this->visibility[$route]) ? !$this->defaultVisibility : $this->defaultVisibility;
			} else {
				throw new \Exception('Invalid config. ');
			}
		}
		
		return $toShow && isset($content) ? $content : $toShow;
	}
}