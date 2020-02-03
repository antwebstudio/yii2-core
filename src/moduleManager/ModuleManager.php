<?php

namespace ant\moduleManager;

use Yii;
use yii\base\Exception;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use ant\moduleManager\ModuleAutoLoader;
use ant\moduleManager\models\Module as ModuleEnabled;

/**
 * ModuleManager handles all installed modules.
 *
 * @author luke
 */
class ModuleManager extends \yii\base\Component
{
	public $packagesPath = '@vendor';
	public $moduleAutoloadPaths = '@app/modules'; // Should not be array which is not able to be overridden (it will be merged)

    /**
     * Create a backup on module folder deletion
     *
     * @var boolean
     */
    public $createBackup = true;

    public $enabled = true;
	
	protected $_modulesConfig = [];
	
	protected $_modulesBasePath = [];

    /**
     * List of all modules
     * This also contains installed but not enabled modules.
     *
     * @param array $config moduleId-class pairs
     */
    protected $modules = []; // Registered modules, which haven't run migration yet.
	
	protected $installedModules = []; // Already run migrations.

    /**
     * List of all enabled module ids
     *
     * @var array
     */
    protected $enabledModules = [];

    /**
     * List of core module classes.
     *
     * @var array the core module class names
     */
    protected $coreModules = [];
	
    const CACHE_ID = 'module_configs';

    public function init()
    {
        parent::init();

        // Either database installed and not in installed state
        if (!$this->isDbInstalled && !$this->isAppInstalled) {
            return;
        }

        if (Yii::$app instanceof yii\console\Application && !$this->isDbInstalled) {
            $this->enabledModules = [];
        } else {
            $this->enabledModules = ModuleEnabled::getEnabledIds();
        }
		
		$this->initModuleAutoloadPaths();
		
		// Scan module paths
		if (isset(Yii::$app->cache)) {
			if (YII_DEBUG || ($configs = Yii::$app->cache->get(self::CACHE_ID)) === false) {
				$configs = $this->scanModulePaths();
				Yii::$app->cache->set(self::CACHE_ID, $configs);
			}
		} else {
			$configs = $this->scanModulePaths();
		}
		
		$this->registerBulk($configs);
    }
	
	public function loadRegisteredModules() {
        if ($this->enabled) {
            foreach ($this->enabledModules as $moduleId) {
                $this->loadModule($this->getModuleConfig($moduleId));
            }
        }
	}
	
	protected function loadModule($config) {
		
        // Append URL Rules
        if (isset($config['urlManagerRules'])) {
            Yii::$app->urlManager->addRules($config['urlManagerRules'], false);
        }

        $moduleConfig = [
            'class' => $config['class'],
            'modules' => $config['modules']
        ];

        // Add config file values to module
        if (isset(Yii::$app->modules[$config['id']])) {
			if (is_array(Yii::$app->modules[$config['id']])) {
				$moduleConfig = \yii\helpers\ArrayHelper::merge($moduleConfig, Yii::$app->modules[$config['id']]);
			} else if (Yii::$app->modules[$config['id']] instanceof \yii\base\Module) {
				throw new \Exception('Module "'.$config['id'].'" is already initialized before the default config is loaded. ');
			}
        }
		
        // Register Yii Module
        Yii::$app->setModule($config['id'], $moduleConfig);

        // Register Event Handlers
        if (isset($config['events'])) {
            foreach ($config['events'] as $event) {
                if (isset($event['class'])) {
                    Event::on($event['class'], $event['event'], $event['callback']);
                } else {
                    Event::on($event[0], $event[1], $event[2]);
                }
            }
        }
	}

    /**
     * Registers a module to the manager
     *
     * @param array $

     * @throws Exception
     */
    public function registerBulk(array $configs)
    {
        foreach ($configs as $basePath => $config) {
            $this->register($basePath, $config);
        }
    }

    /**
     * Registers a module
     *
     * @param string $basePath the modules base path
     * @param array $config the module configuration (config.php)
     * @throws InvalidConfigException
     */
    public function register($basePath, $config = null)
    {
        if ($config === null && is_file($basePath . '/config.php')) {
            $config = require($basePath . '/config.php');
        }
        // Check mandatory config options
        if (!isset($config['class']) || !isset($config['id'])) {
            throw new InvalidConfigException("Module configuration requires an id and class attribute! (file: ".$basePath . '/config.php)');
        }

        $isCoreModule = (isset($config['isCoreModule']) && $config['isCoreModule']);
        $isInstallerModule = (isset($config['isInstallerModule']) && $config['isInstallerModule']);

        $this->modules[$config['id']] = $config['class'];

		// Register alias for module
		$namespace = $this->getNamespace($config['class']);
		Yii::setAlias('@' . str_replace('\\', '/', $namespace), $basePath);
		Yii::setAlias('@app/modules/'.$config['id'], $basePath);
		
        if (isset($config['alias']) && is_array($config['alias'])) {
			foreach ($config['alias'] as $alias => $path) {
				Yii::setAlias($alias, $path);
			}
        }
        Yii::setAlias('@' . $config['id'], $basePath);
        if (isset($config['aliases']) && is_array($config['aliases'])) {
            foreach ($config['aliases'] as $name => $value) {
                Yii::setAlias($name, $value);
            }
        }

        if (!$this->isAppInstalled && $isInstallerModule) {
            $this->enabledModules[] = $config['id'];
        }

        // Submodules
        if (!isset($config['modules'])) {
            $config['modules'] = [];
        }
		
		$this->_modulesBasePath[$config['id']] = $basePath;
		$this->_modulesConfig[$config['id']] = $config;

        // Not enabled and no core/installer module
        if (!$isCoreModule && !in_array($config['id'], $this->enabledModules)) {
            return;
        }

        if ($isCoreModule) {
            $this->coreModules[] = $config['class'];
        }
		
		if (isset($config['depends'])) {
			foreach ($config['depends'] as $dependModuleName) {
				if (!in_array($dependModuleName, $this->enabledModules)) {
					$this->enabledModules[] = $dependModuleName;
				}
			}
		}
    }
	
	protected function getNamespace($className) {
		return substr($className, 0, strrpos($className, '\\'));
	}

    /**
     * Returns all modules (also disabled modules).
     *
     * Note: Only modules which extends \humhub\components\Module will be returned.
     *
     * @param array $options options (name => config)
     * The following options are available:
     *
     * - includeCoreModules: boolean, return also core modules (default: false)
     * - returnClass: boolean, return classname instead of module object (default: false)
     *
     * @return array
     */
    public function getModules($options = [])
    {
        $modules = [];

        foreach ($this->modules as $id => $class) {

            // Skip core modules
            if (!isset($options['includeCoreModules']) || $options['includeCoreModules'] === false) {
                if (in_array($class, $this->coreModules)) {
                    continue;
                }
            }

            if (isset($options['returnClass']) && $options['returnClass']) {
                $modules[$id] = $class;
            } else {
                $module = $this->getModule($id);
                if ($module instanceof Module) {
                    $modules[$id] = $module;
                }
            }
        }

        return $modules;
    }

    /**
     * Checks if a moduleId exists, regardless it's activated or not
     *
     * @param string $id
     * @return boolean
     */
    public function hasModule($id)
    {
        return (array_key_exists($id, $this->modules));
    }

    /**
     * Returns a module instance by id
     *
     * @param string $id Module Id
     * @return \yii\base\Module
     */
    public function getModule($id)
    {
        // Enabled Module
        if (Yii::$app->hasModule($id)) {
            return Yii::$app->getModule($id, true);
        }

        // Disabled Module
        if (isset($this->modules[$id])) {
            $class = $this->modules[$id];
            return Yii::createObject($class, [$id, Yii::$app]);
        }

        throw new Exception("Could not find/load requested module: " . $id.'. Please check if the config file point to the correct module class, and also the module class namespace is set correctly. ');
    }

    /**
     * Flushes module manager cache
     */
    public function flushCache()
    {
		if (isset(Yii::$app->cache)) {
			Yii::$app->cache->delete(self::CACHE_ID);
		}
    }

    /**
     * Checks the module can removed
     *
     * @param type $moduleId
     */
    public function canRemoveModule($moduleId)
    {
        $module = $this->getModule($moduleId);

        if ($module === null) {
            return false;
        }

        // Check is in dynamic/marketplace module folder
        if (strpos($module->getBasePath(), Yii::getAlias(Yii::$app->params['moduleMarketplacePath'])) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Removes a module
     *
     * @param strng $id the module id
     */
    public function removeModule($moduleId, $disableBeforeRemove = true)
    {
        $module = $this->getModule($moduleId);

        if ($module == null) {
            throw new Exception("Could not load module to remove!");
        }

        /**
         * Disable Module
         */
        if ($disableBeforeRemove && Yii::$app->hasModule($moduleId)) {
            $module->disable();
        }

        /**
         * Remove Folder
         */
        if ($this->createBackup) {
            $moduleBackupFolder = Yii::getAlias("@runtime/module_backups");
            if (!is_dir($moduleBackupFolder)) {
                if (!@mkdir($moduleBackupFolder)) {
                    throw new Exception("Could not create module backup folder!");
                }
            }

            $backupFolderName = $moduleBackupFolder . DIRECTORY_SEPARATOR . $moduleId . "_" . time();
            $moduleBasePath = $module->getBasePath();
            FileHelper::copyDirectory($moduleBasePath, $backupFolderName);
            FileHelper::removeDirectory($moduleBasePath);
        } else {
            //TODO: Delete directory
        }

        $this->flushCache();
    }

    /**
     * Enables a module
     *
     */
    public function enable($module)
    {
		$module = ($module instanceof Module) ? $module : $this->getModule($module);
			
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => $module->id]);
        if ($moduleEnabled == null) {
            $moduleEnabled = new ModuleEnabled();
            $moduleEnabled->module_id = $module->id;
            $moduleEnabled->save();
        }

        $this->enabledModules[] = $module->id;
        $this->register($module->getBasePath());
		
		$this->flushCache();
		
		return true;
    }

    public function enableModules($modules = [])
    {
        foreach ($modules as $module) {
            $module = ($module instanceof Module) ? $module : $this->getModule($module);
            if ($module != null) {
                $module->enable();
            }
        }
    }

    /**
     * Disables a module
     *
     */
    public function disable($module)
    {
		$module = ($module instanceof Module) ? $module : $this->getModule($module);
		
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => $module->id]);
        if ($moduleEnabled != null) {
            $moduleEnabled->delete();
        }

        if (($key = array_search($module->id, $this->enabledModules)) !== false) {
            unset($this->enabledModules[$key]);
        }

        Yii::$app->setModule($module->id, 'null');
		
		$this->flushCache();
		
		return true;
    }

    public function disableModules($modules = [])
    {
        foreach ($modules as $module) {
            $module = ($module instanceof Module) ? $module : $this->getModule($module);
            if($module != null) {
                $module->disable();
            }
        }
    }
	
	public function isModuleEnabled($name) {
		return array_key_exists($name, $this->getEnabledModules());
	}
	
	public function getRegisteredModules() {
		return $this->modules;
	}
	
	public function getEnabledModules() {
		return $this->enabledModules;
	}
	
	protected function initModuleAutoloadPaths() {
		$modules = require Yii::getAlias('@vendor/antweb/modules.php');
		
		foreach (array_keys($modules) as $composerName) {
			$this->moduleAutoloadPaths[] = $this->packagesPath.'/'.$composerName.'/src';
		}
	}
	
	protected function scanModulePaths() {
		$modules = [];
		foreach ((array) $this->moduleAutoloadPaths as $modulePath) {
			$modulePath = Yii::getAlias($modulePath);
			if (is_dir($modulePath)) {
				foreach (scandir($modulePath) as $moduleId) {
					if ($moduleId == '.' || $moduleId == '..')
						continue;

					$moduleDir = $modulePath . DIRECTORY_SEPARATOR . $moduleId;
					if (is_dir($moduleDir) && is_file($moduleDir . DIRECTORY_SEPARATOR . 'config.php')) {
						try {
							$modules[$moduleDir] = require($moduleDir . DIRECTORY_SEPARATOR . 'config.php');
						} catch (\Exception $ex) {
							if (YII_DEBUG) {
								throw $ex;
							} else {
								Yii::error($ex);
							}
						}
					}
				}
			}
		}
		return $modules;
	}
	
	protected function getDependcy($moduleId) {
		$config = $this->getModuleConfig($moduleId);
		$depends = isset($config['depends']) ? $config['depends'] : [];
		
		if (isset($depends)) {
			foreach ($depends as $depend) {
				$depends = \yii\helpers\ArrayHelper::merge($depends, $this->getDependcy($depend));
			}
		}
		return $depends;
	}
	
	public function getModuleBasePath($moduleId) {
		return isset($this->_modulesBasePath[$moduleId]) ? $this->_modulesBasePath[$moduleId] : null;
	}
	
	protected function getModuleConfig($moduleId) {
		if (isset($this->_modulesConfig[$moduleId])) {
			return $this->_modulesConfig[$moduleId];
		} else {
			/*$autoloadPath = [];
			foreach ((array) $this->moduleAutoloadPaths as $pathAlias) {
				$autoloadPath[] = Yii::getAlias($pathAlias);
			}*/
			throw new \Exception('Module "'.$moduleId.'" config.php is not exist or the "id" of the config file is not set correctly. ');
		}
	}
	
	/*public function getInstalledModulesConfigs() {
		return $this->_modulesConfig;
	}*/
	
	public function getMigrationNamespaces($type = 'db', $moduleId = null) {
		$ns = [];
		
		if (isset($moduleId)) {
			$enabled = $this->getDependcy($moduleId);
			$enabled[] = $moduleId;
		} else {
			$enabled = $this->getEnabledModules();
		}
		$enabled = array_unique($enabled);
		
		foreach ($enabled as $moduleId) {
			if (isset($this->modules[$moduleId])) {
				$class = $this->modules[$moduleId];
				if (($pos = strrpos($class, '\\')) !== false) {
					$ns[] = substr($class, 0, $pos) . '\\migrations\\'.$type;
					
					// api
					//$ns[] = 'api\v1'.substr($class, strlen('common'), $pos - strlen('common')) . '\\migrations\\'.$type;
				}
			}
		}
		return $ns;
	}
	
	public function getMigrationPath($type = 'db', $moduleId = null) {
		$path = [];
		
		if (isset($moduleId)) {
			$enabled = $this->getDependcy($moduleId);
			$enabled[] = $moduleId;
		} else {
			$enabled = $this->getEnabledModules();
		}
		$enabled = array_unique($enabled);
		
		foreach ($enabled as $moduleId) {
			if (isset($this->modules[$moduleId])) {
				$class = $this->modules[$moduleId];
				if (($pos = strrpos($class, '\\')) !== false) {
					//$path[] = '@vendor/inspirenmy/ecommerce/src/'.str_replace('\\', '/', substr($class, 0, $pos)) . '/migrations/'.$type;
				}
			}
		}
		return $path;
	}
	
	/*public function getActiveModules() {
		$activeModules = [];
		
		foreach (['@frontend', '@common', '@backend'] as $alias) {
			$path = \Yii::getAlias($alias).'/config/modules.php';
			if (file_exists($path)) {
				$modules = require $path;
				$activeModules = ArrayHelper::merge($activeModules, $modules);
			}
		}
		
		return $activeModules;
	}*/
	
	protected function getIsAppInstalled() {
		return true;
	}
	
	protected function getIsDbInstalled() {
		$tableSchema = Yii::$app->db->schema->getTableSchema(ModuleEnabled::tableName());
		return $tableSchema !== null;
	}

}
