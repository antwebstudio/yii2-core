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

	public static function collect($array) {
		return new \ant\helpers\Collection($array);
	}
}