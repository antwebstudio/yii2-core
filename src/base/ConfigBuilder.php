<?php
namespace ant\base;

class ConfigBuilder {
	const FRONTEND = 'web-frontend';
	const BACKEND = 'web-backend';
	const CONSOLE = 'cli-console';
	const API = 'api';
	const TEST = 'test';
	const TEST_FUNCTIONAL = 'test-functional';
	
	public function files() {
		return [
			'web-frontend' => [
				dirname(__DIR__) . '/config/web-frontend.php',
				YII_PROJECT_BASE_PATH . '/config/web-frontend.php',
			],
			'web-backend' => [
				dirname(__DIR__) . '/config/web-backend.php',
				YII_PROJECT_BASE_PATH . '/config/web-backend.php',
			],
			'cli-console' => [
				dirname(__DIR__) . '/config/cli-console.php',
				YII_PROJECT_BASE_PATH . '/config/console.php',
			],
			'test' => [
				dirname(__DIR__) . '/config/common.php',
				//YII_PROJECT_BASE_PATH . '/config/common.php',
				dirname(__DIR__) . '/config/console.php',
				//YII_PROJECT_BASE_PATH . '/config/console.php',
				//dirname(__DIR__) . '/tests/codeception/config/console.php',
				//YII_PROJECT_BASE_PATH . '/tests/config/local.php',
			],
			'api' => [
				//dirname(__DIR__) . '/config/common.php',
				//YII_PROJECT_BASE_PATH . '/config/common.php',
				dirname(__DIR__) . '/config/web-api.php',
				YII_PROJECT_BASE_PATH . '/config/web-api.php',
			],
			self::TEST_FUNCTIONAL => [
				YII_PROJECT_BASE_PATH . '/config/web-frontend.php',
				dirname(__DIR__) . '/config/test-functional.php',
				//YII_PROJECT_BASE_PATH . '/config/test.php',
			],
		];
	}
	
	public static function load($name) {
		return (new self)->loadConfig($name);
	}
	
	public function loadConfig($name) {
		$config = [];
		$files = $this->getConfigFiles($name);
		foreach ($files as $file) {
			$config = \yii\helpers\ArrayHelper::merge($config,
				require($file)
			);
		}
		return $config;
	}
	
	protected function getConfigFiles($name) {
		$files = $this->files();
		return $files[$name];
	}
}