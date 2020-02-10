<?php
namespace ant\helpers;

class Console extends \yii\helpers\Console {	
	public static function getArguments() {
		$arguments = $_SERVER['argv'];
		array_shift($arguments);
		
		$params = [];
		foreach ($arguments as $param) {
			preg_match('/^--([\w-]+)(?:=(.*))?$/', $param, $matches);
			
			$name = $matches[1];
			if (is_numeric(substr($name, 0, 1))) {
				throw new Exception('Parameter "' . $name . '" is not valid');
			}

			//if ($name !== Application::OPTION_APPCONFIG) {
				$params[$name] = isset($matches[2]) ? $matches[2] : true;
				//$prevOption = &$params[$name];
			//}
		}
		return $params;
	}
}