<?php
namespace ant\behaviors;

use Yii;	

class ProjectContainerBehavior extends \yii\base\Behavior {
	public $defaultName;
	
	public $componentName = 'projectContainer';
	
	public function getModelName() {
		if (isset($this->container)) {
			$modelName = $this->container->getModelName($this->owner);
		}
		return isset($modelName) ? $modelName : $this->defaultName;
	}
	
	public function getContainer() {
		return Yii::$app->{$this->componentName};
	}
}