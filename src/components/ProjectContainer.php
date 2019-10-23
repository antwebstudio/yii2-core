<?php
namespace ant\components;

class ProjectContainer extends \yii\base\Component {
	public $modelName;
	
	public function getModelName($model) {
		$className = get_class($model);
		
		if (isset($this->modelName[$className])) {
			return $this->modelName[$className];
		}
	}
}