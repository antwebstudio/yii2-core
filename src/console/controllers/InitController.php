<?php

namespace ant\console\controllers;

use Yii;
use yii\helpers\Console;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class InitController extends \yii\console\Controller {
	public $path = '@vendor/antweb/yii2-core/src/config/.env.dist';
	public $templatePath = '@vendor/antweb/yii2-core/template';
	public $to = '@ant';
	public $basePath = '@ant';
	
	protected $params;
	
	public function actionIndex() {
		$from = Yii::getAlias($this->path);
		$to = Yii::getAlias($this->to.'/.env');
		
		
		if (!file_exists($to) || $this->confirm('.env file is exist, do you want to regenerate it? ')) {
			$this->getParams();
			
			$this->generateEnvFile($from, $to, $this->params);
		}		
		$this->copyTemplate();
	}
	
	protected function getParams() {
		$basePath = Yii::getAlias($this->basePath);
		$projectId = basename($basePath);
		$this->params = [
			'debug' => 'false',
			'env' => 'dev',
		];
		
		$projectId = $this->promptValue('project_id', 'Project ID', $projectId);
		$this->promptValue('application_name', 'Application Names');
		$this->promptValue('db_host', 'Host', 'localhost');
		$this->promptValue('db_port', 'Port', '3306');
		$this->promptValue('dbname', 'DB Name', $projectId);
		$this->promptValue('db_username', 'Username', 'root');
		$this->promptValue('db_password', 'Password');
		$this->promptValue('db_table_prefix', 'Table Prefix', '');
		$this->promptValue('base_url', 'Base Url');
		$this->promptValue('theme', 'Theme', $projectId);
		
		$this->params['dbname_test'] = $this->params['dbname'];
	}
	
	protected function copyTemplate() {
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
	
	protected function generateEnvFile($from, $to, $params = []) {
		$content = file_get_contents($from);
		foreach ($params as $name => $value) {
			$content = preg_replace('/<'.$name.'>/', $value, $content);
		}
		file_put_contents($to, $content);
	}
}