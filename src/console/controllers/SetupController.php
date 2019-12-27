<?php

namespace ant\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use ant\user\models\User;
use ant\rbac\Role;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SetupController extends Controller
{
    public $modules = [];
	
	public $targetPath;
	
	public $symlinks = [
		'@project/web/storage' => '@project/storage/web',
		//'@project/frontend/web/admin' => '@project/backend/web',
		//'C:/wamp64/www/ant/eventmy/frontend/web/storage' => 'C:/wamp64/www/ant/eventmy/storage/web',
	];

    public $writablePaths = [
        '@ant/runtime',
        //'@frontend/runtime',
        //'@frontend/web/assets',
        //'@backend/runtime',
        //'@backend/web/assets',
        '@storage/cache',
        '@storage/web/source'
    ];

    public $executablePaths = [
        '@backend/yii',
        '@frontend/yii',
        '@console/yii',
    ];

    public $generateKeysPaths = [
        '@project/.env'
    ];
	
	public function options($actionId) {
        return \yii\helpers\ArrayHelper::merge(parent::options($actionId), ['generateKeysPaths', 'targetPath']);
    }

    public function actionIndex($env = null)
    {
        $this->runAction('set-writable', ['interactive' => $this->interactive]);
        $this->runAction('set-executable', ['interactive' => $this->interactive]);
        $this->runAction('set-keys', ['interactive' => $this->interactive]);
		$this->runAction('create-symlinks', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('core-migrate/up', ['interactive' => false]);

		// Enable user module
        \Yii::$app->runAction('module/enable', [0 => 'user', 'interactive' => false]);
		
		// Enable other modules
        foreach ($this->modules as $module) {
            \Yii::$app->runAction('module/enable', [0 => $module, 'interactive' => false]);
        }

        \Yii::$app->runAction('migrate/up', ['interactive' => false]);
        \Yii::$app->runAction('rbac-migrate/up', ['interactive' => false]);
		
		\Yii::$app->runAction('user/generate-default-user', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('cache/flush-all');
    }

    public function actionSetWritable()
    {
        $this->setWritable($this->writablePaths);
    }

    public function actionSetExecutable()
    {
        $this->setExecutable($this->executablePaths);
    }

    public function actionSetKeys()
    {
        $this->setKeys($this->generateKeysPaths);
    }
	
	public function actionCreateSymlinks() {
		foreach ($this->symlinks as $link => $destination) {
			$link = Yii::getAlias($link);
			$destination = Yii::getAlias($destination);
			//throw new \Exception($destination.' => '.$link);
			
			if (!is_dir($link)) {
				\yii\helpers\FileHelper::createDirectory(dirname($link), 0775, true);
				symlink($destination, $link);
			}
		}
	}

    public function setWritable($paths)
    {
        foreach ($paths as $writable) {
            $writable = Yii::getAlias($writable);
            Console::output("Setting writable: {$writable}");
            @chmod($writable, 0777);
        }
    }

    public function setExecutable($paths)
    {
        foreach ($paths as $executable) {
            $executable = Yii::getAlias($executable);
            Console::output("Setting executable: {$executable}");
            @chmod($executable, 0755);
        }
    }

    public function setKeys($paths)
    {
        foreach ($paths as $file) {
            $file = Yii::getAlias($file);
            Console::output("Generating keys in {$file}");
            $content = file_get_contents($file);
            $content = preg_replace_callback('/<generated_key>/', function () {
                $length = 32;
                $bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
                return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
            }, $content);
            file_put_contents($file, $content);
        }
    }
}
