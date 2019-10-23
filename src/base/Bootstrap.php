<?php
namespace ant\base;

class Bootstrap {
	const FRONTEND = 'frontend';
	const BACKEND = 'backend';
	const CONSOLE = 'console';
	const API = 'api';
	const TEST = 'test';
	
	public $files = [
		self::FRONTEND => 'web-frontend.php',
		self::BACKEND => 'web-backend.php',
		self::CONSOLE => 'console.php',
		self::API => 'api.php',
		self::TEST => 'test.php',
	];
	
	public function load($name, $projectPath, $storagePath) {
		$path = dirname(__DIR__).'/bootstrap';
		require($path.'/'.$this->files[$name]);
	}
	
	public static function run($type, $projectPath, $storagePath) {
		return (new self)->load($type, $projectPath, $storagePath);
	}
}