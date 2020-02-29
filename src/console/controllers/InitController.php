<?php

namespace ant\console\controllers;

use Yii;
use yii\helpers\Console;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class InitController extends \yii\console\Controller {
	public $path = '@vendor/antweb/yii2-core/src/config/.env.dist';
	public $templatePath = '@vendor/antweb/yii2-core/template';
	public $to = '@root';
	public $basePath = '@root';
	public $force = false;
	
	protected $params = [];
	
	protected $prompt = ['applicationName', 'dbHost', 'dbPort', 'dbName', 'dbUsername', 'dbPassword', 'dbTablePrefix', 'baseUrl', 'theme'];
	
	protected $defaultValues = [
		'debug' => 'true',
		'env' => 'dev',
		'maintenance' => 'false',
		'emailAdmin' => 'chy1988@antwebstudio.com',
		'emailRobot' => 'noreply@antwebstudio.com',
		'smtpHost' => 'mail.antwebstudio.com',
		'smtpPort' => '26',
		'smtpUsername' => 'robot@antwebstudio.com',
		'smtpPassword' => 'antwebstudio',
		'smtpEncryption' => '',
		'applicationName' => '',
		'dbHost' => 'localhost',
		'dbPort' => '3306',
		'dbUsername' => 'root',
		'dbPassword' => 'root',
		'dbName' => '',
		'dbTablePrefix' => '',
		'baseUrl' => '',
		'backendTheme' => 'adminlte3',
		'theme' => '',
		'useTranslateManager' => 'false',
	];
	
	public function __get($name) {
		try {
			parent::__get($name);
		} catch (\Exception $ex) {
			return isset($this->params[$name]) ? $this->params[$name] : null;
		}
	}
	
	public function __set($name, $value) {
		try {
			parent::__set($name, $value);
		} catch (\Exception $ex) {
			$this->params[$name] = $value;
		}
	}

	public function options($actionId)
    {
		$options = array_keys($this->defaultValues);
		$options[] = 'force';
        return ArrayHelper::merge(parent::options($actionId), $options);
    }
	
	public function actionIndex() {
		$this->generateEnvFile();
		$this->copyProjectTemplate();
	}
	
	public function actionInstall() {
		$this->generateEnvFile();
	}
	
	protected function getLabel($name) {
		return Inflector::camel2words($name);
	}
	
	protected function getParams() {
		$basePath = Yii::getAlias($this->basePath);
		$projectId = basename($basePath);
		
		$projectId = $this->promptValue('projectId', $this->getLabel('projectId'), $projectId);
		$this->defaultValues['dbName'] = $projectId;
		$this->defaultValues['theme'] = $projectId;
		$this->defaultValues['applicationName'] = Inflector::camel2words($projectId);
		
		foreach ($this->prompt as $prompt) {
			if (!isset($this->params[$prompt])) {
				$this->promptValue($prompt, $this->getLabel($prompt), isset($this->defaultValues[$prompt]) ? $this->defaultValues[$prompt] : null);
			}
		}
		
		$this->params = ArrayHelper::merge($this->defaultValues, $this->params);
	}
	
	protected function copyProjectTemplate() {
		$path = Yii::getAlias($this->templatePath);
		$to = Yii::getAlias($this->to);
		
		$all = true;
		
		foreach ($this->getFileList($path) as $file) {
			$this->copyFile($path.'/'.$file, $to.'/'.$file, $all, []);
		}
	}
	
	protected function getFileList($root, $basePath = '') {
		$files = [];
		$handle = opendir($root);
		while (($path = readdir($handle)) !== false) {
			if ($path === '.git' || $path === '.svn' || $path === '.' || $path === '..') {
				continue;
			}
			$fullPath = "$root/$path";
			$relativePath = $basePath === '' ? $path : "$basePath/$path";
			if (is_dir($fullPath)) {
				$files = array_merge($files, $this->getFileList($fullPath, $relativePath));
			} else {
				$files[] = $relativePath;
			}
		}
		closedir($handle);
		return $files;
	}

	
	protected function copyFile($source, $target, &$all, $params) {
		if (!is_file($source)) {
			echo "       skip $target ($source not exist)\n";
			return true;
		}
		if (is_file($target)) {
			if (file_get_contents($source) === file_get_contents($target)) {
				echo "  unchanged $target\n";
				return true;
			}
			if ($all) {
				echo "  overwrite $target\n";
			} else {
				echo "      exist $target\n";
				echo "            ...overwrite? [Yes|No|All|Quit] ";
				$answer = !empty($params['overwrite']) ? $params['overwrite'] : trim(fgets(STDIN));
				if (!strncasecmp($answer, 'q', 1)) {
					return false;
				} else {
					if (!strncasecmp($answer, 'y', 1)) {
						echo "  overwrite $target\n";
					} else {
						if (!strncasecmp($answer, 'a', 1)) {
							echo "  overwrite $target\n";
							$all = true;
						} else {
							echo "       skip $target\n";
							return true;
						}
					}
				}
			}
			file_put_contents($target, file_get_contents($source));
			return true;
		}
		echo "   generate $target\n";
		@mkdir(dirname($target), 0777, true);
		file_put_contents($target, file_get_contents($source));
		return true;
	}

	
	protected function promptValue($name, $label, $default = null) {
		$this->params[$name] = $this->prompt($label.': ', ['default' => $default]);
		return $this->params[$name];
	}
	
	protected function generateEnvFile() {
		$from = Yii::getAlias($this->path);
		$to = Yii::getAlias($this->to.'/.env');
		
		if (!file_exists($to) || $this->force || $this->confirm('.env file is exist, do you want to regenerate it? ')) {
			$this->getParams();
			
			$this->writeEnvFile($from, $to, $this->params);
		}
	}
	
	protected function writeEnvFile($from, $to, $params = []) {
		$content = file_get_contents($from);
		foreach ($params as $name => $value) {
			$content = preg_replace('/<'.$name.'>/', $value, $content);
		}
		
		$content = preg_replace_callback('/<generatedKey>/', function () {
			$length = 32;
			$bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
			return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
		}, $content);
			
		file_put_contents($to, $content);
	}
}