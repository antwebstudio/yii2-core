<?php
use ant\helpers\Collection;

class Ant {
	public static function call($callback, $params = []) {
		if (is_callable($callback)) {
			return call_user_func_array($callback, $params);
		}
		return $callback;
	}
	
	public static function config() {
		
		
	}

	public static function get($class) {
		return Yii::createObject($class);
	}

	public static function collect($array) {
		return new \ant\helpers\Collection($array);
	}

	public static function invokeMethod($object, $methodName, $arguments = []) {
		$class = new \ReflectionClass($object);
		$method = $class->getMethod($methodName);
		$method->setAccessible(true);
		return $method->invokeArgs($object, $arguments);
		
	}
	
	public static function getProperty($object, $propertyName) {
		$class = new \ReflectionClass($object);
		$property = $class->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($object);
	}
	
	public static function setProperty($object, $propertyName, $value) {
		$class = new \ReflectionClass($object);
		$property = $class->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->setValue($object, $value);
	}
}