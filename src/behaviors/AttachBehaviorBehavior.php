<?php

namespace ant\behaviors;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class AttachBehaviorBehavior extends Behavior {
	const DEFAULT_NAME = 'attachBehavior';
	
	public $config = ['@project/config/behaviors.php'];
	
	protected static $staticConfig = [];
	
	public function attach($owner) {
		parent::attach($owner);

		$class = get_class($owner);
		$owner->attachBehaviors($this->loadConfig());
	}

	public static function attachTo($class, $behaviors) {
		if (!isset(self::$staticConfig[$class])) self::$staticConfig[$class] = [];
		self::$staticConfig[$class] = ArrayHelper::merge(self::$staticConfig[$class], $behaviors);
	}

	public static function clear($class) {
		unset(self::$staticConfig[$class]);
	}

	public static function clearAll() {
		self::$staticConfig = [];
	}

	public static function config($class, $behaviors) {
		self::$staticConfig[$class] = $behaviors;
	}
	
	protected function loadConfig() {
		$className = get_class($this->owner);
		
		if (is_array($this->config)) {
			$config = [];
			foreach ($this->config as $configFile) {
				$config = ArrayHelper::merge(
					$config,
					require \Yii::getAlias($configFile)
				);
			}
		} else {	
			$config = require \Yii::getAlias($this->config);
		}
		$config = ArrayHelper::merge(
			$config,
			self::$staticConfig
		);

		if (isset($config[$className]) && is_callable($config[$className])) {
			$config[$className] = call_user_func_array($config[$className], [$this->owner]);
		}

		return isset($config[get_class($this->owner)]) ? $config[$className] : [];
	}
}