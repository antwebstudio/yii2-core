<?php
namespace ant\console\components;

use Yii;
use yii\helpers\Console;
use Composer\Script\PackageEvent;

class Installer {
	public static function setup(\Composer\Script\Event $event) {
		$arguments = $event->getArguments();
		
		$options = self::getOptions($arguments);
		
		Console::output(print_r($options, 1));
		
		$path = '.';
		
		$continue = true;
		if (file_exists($path.'/.env')) {
			$continue = self::prompt('Application is already installed, continue? (yes to continue)', 'no');
			$continue = strtolower($continue) == 'yes';
		}
		
		if ($continue) {
					
			$basePath = self::getComposerBaseDir($event);
			$projectId = basename($basePath);
			
			Console::output('Application');
			Console::output('=================');

			Console::output('Project ID: '.$projectId);
			
			$applicationName = self::prompt('Application Name', $options['name']);	
			$theme = self::prompt('Theme', isset($options['theme']) ? $options['theme'] : $projectId);
			$baseUrl = self::prompt('Base URL', $options['baseUrl']);
			$developerEmail = self::prompt('Developer Email', 'chy1988@gmail.com');
			
			Console::output("\n");
			Console::output('Database');
			Console::output('=================');
			
			$host = self::prompt('Host', 'localhost');
			$port = self::prompt('Port', '3306');
			$dbname = self::prompt('DB Name', $options['db']);
			$username = self::prompt('Username', $options['dbUser']);
			$password = self::prompt('Password', $options['dbPassword']);
			$tablePrefix = self::prompt('Table Prefix', $options['dbPrefix']);
			
			//$path = \Yii::getAlias($path);
			//Console::output(__DIR__);
			copy($path.'/.env.dist', $path.'/.env');
			
			$path .= '/.env';
			self::setEnv($path, 'application_name', $applicationName);
			self::setEnv($path, 'dbname', $dbname);
			self::setEnv($path, 'db_username', $username);
			self::setEnv($path, 'db_password', $password);
			self::setEnv($path, 'db_host', $host);
			self::setEnv($path, 'db_port', $port);
			self::setEnv($path, 'db_table_prefix', $tablePrefix);
			
			self::setEnv($path, 'project_id', $projectId);
			self::setEnv($path, 'theme', $theme);
			self::setEnv($path, 'base_url', $baseUrl);
			self::setEnv($path, 'developer_email', $developerEmail);
			
			foreach($options as $name => $value) {
				self::setEnv($path, $name, $value);
			}
		}	
	}
	
	protected static function getOptions($arguments) {
		$options = [];
		
		foreach ($arguments as $arg) {
			$name = substr($arg, 2, strpos($arg, '=') - 2);
			$value = substr($arg, strpos($arg, '=') + 1);
			
			$options[$name] = $value;
		}
		return $options;
	}
	
	protected static function getOption($name, $arguments) {
		foreach ($arguments as $arg) {
			if (strpos($arg, '--'.$name.'=') !== false) {
				return substr($arg, strlen('--'.$name.'='));
			}
		}
	}
	
	protected static function getComposerBaseDir($event) {
		return dirname($event->getComposer()->getConfig()->get('vendor-dir'));
	}
	
	protected static function prompt($text, $default = null, $options = []) {
		$input = Console::prompt($text.': ('.$default.')', $options);
		return trim($input) != '' ? $input : $default;
	}
	
	protected static function setEnv($path, $variable, $value) {
		//$file = \Yii::getAlias($path);
		
		$content = file_get_contents($path);
		$content = preg_replace('/<'.$variable.'>/', $value, $content);
		file_put_contents($path, $content);
	}
}