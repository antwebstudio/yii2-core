<?php
namespace ant\base;

use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use ant\base\Model;
use ant\base\MultiModel;

/*
[
    'class' => 'ant\order\models\OrderForm',
    'form' => [
        
    ],
    'models' => [
        'xxname' => [
            'class' => 'ant\order\models\Order',
            'as customized' => [
                'class' => 'ant\behaviors\CustomizableModelBehavior',
                'rules' => [

                ],
                'form' => [
                    
                ],
                'models' => [
                    
                ],
            ]
        ],
    ],
]
*/

class FormModel extends MultiModel {
    const EVENT_SET = 'set';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_COMMIT_SAVE = 'before_commit_save';
	const EVENT_BEFORE_VALIDATE = \yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE;

    public $configs = [];
    public $confirm = false;
	public $extraModels = [];

	protected $_redirectUrl;
    protected $_modelInArray = [];
	protected $_modelFlags = [];
    protected $_isPostBack = false;
	//public $test = false;
	
	public function __construct($config = []) {
		// Set scenario if config have scenario
		if (isset($config['scenario'])) $this->scenario = $config['scenario'];
		
		$models = isset($config['models']) ? $config['models'] : $this->models();
		
		if (isset($config['extraModels'])) {
			$extraModels = is_callable($config['extraModels']) ? call_user_func_array($config['extraModels'], [$this]) : $config['extraModels'];

			foreach ($extraModels as $name => $modelConfig) {
				if ($modelConfig === false) {
					unset($models[$name]);
				} else {
					$models[$name] = $modelConfig;
				}
			}
		}
		
		$mergedConfig = array_merge(['models' => $models], $config);
        return parent::__construct($mergedConfig);
	}

    public function __isset($name) {
        //if ($this->isModelIsset($name)) {
        if (isset($this->_models[$name])) {
            return true;
        }
        return parent::__isset($name);
    }

    public function __call($name, $params) {
        //if (!$this->hasMethod($name)) {
            $prefix = substr($name, 0, 3);
            if ($prefix == 'get' || $prefix == 'set') {
                $modelName = strtolower(substr($name, 3, 1)).substr($name, 4);

                if ($this->isModelIsset($modelName)) {
                    if ($prefix == 'get') {
                        return $this->_getModel($modelName, isset($params[0]) ? $params[0] : [], true);
                    } else if ($prefix == 'set') {
                        return $this->_setValue($modelName, $params[0]);
                    }
                } else {
					//throw new \Exception('t');
				}
            }
			
			//throw new \Exception('t2');
        //}
        return parent::__call($name, $params);
    }

    /*public function __set($name, $value) {
        if ($this->isModelIsset($name)) {
            return $this->_setValue($name, $value);
        }
        return parent::__set($name, $value);
    }

    public function __get($name) {
        if ($this->isModelIsset($name)) {
            return $this->_getModel($name, [], false);
        }
        return parent::__get($name);
    }*/

    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch (\Exception $ex) {
            if ($this->isModelIsset($name)) {
                return $this->_setValue($name, $value);
            }
            throw $ex;
        }
    }

    public function __get($name) {
        try {
            return parent::__get($name);
        } catch (\Exception $ex) {
            if ($this->isModelIsset($name)) {
                return $this->_getModel($name, [], false);
            }
            throw $ex;
        }
    }

    protected function configs() {
        return [];
    }
	
	protected function models() {
        return $this->configs();
	}

    public function hasModelConfigured($name) {
        return isset($this->models[$name]);
    }

    public function rules() {
        $modelNames = array_merge(array_keys($this->_modelsConfigs), array_keys($this->_models));
        return [
            [$modelNames, 'safe'],
            ['confirm', 'safe'],
        ];
    }

	public function save($runValidation = true) {
        $transaction = $this->getDb()->beginTransaction();
        try {
            /*if ($this->beforeSave() === false) {
                $transaction->rollBack();
                return false;
            }*/
            $result = parent::save($runValidation);

            if ($result) {
                $this->trigger(self::EVENT_BEFORE_COMMIT_SAVE);
                $transaction->commit();
                $this->trigger(self::EVENT_AFTER_SAVE);
            } else {
                $transaction->rollBack();
            }
            return $result;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
	}

    protected function _getModel($name, $configs = null, $createIfNotExist = false) {
        $model = ArrayHelper::getValue($this->_models, $name, null);
		
        if (!isset($model) && $createIfNotExist) {
            $model = $this->createModels($name, 1, $configs);
            //if ($this->isOptional($name)) {
            //    $this->_optionalModels[$name] = $model;
            //} else {
                $this->_models[$name] = $model;
            //}
        } else if (isset($configs)) {
			//throw new \Exception('Model is already initialized. Failed to get model with initialized config');  // When model is already initialized, and configs is set, throw exception
			Yii::configure($model, $configs);
		}
        return $model;
        
        //return $this->getModel($name);
    }

    protected function _getModelValue($name, $value) {
		//if ($this->isOptional($name)) {
		//	return $this->_optionalModels[$name];
		//} else {
			return $this->_models[$name];
		//}
     }

    protected function _setModelValue($name, $value) {
		//if ($this->isOptional($name)) {
        //    $this->_optionalModels[$name] = $value;
        //} else {
            $this->_models[$name] = $value;
        //}
    }
	
	protected function _loadModels($name, $data, $formName = null) {
		$haveSuccess = false;
		if (!is_array($this->_models[$name])) {
			$this->__deprecatedMethod($name, $this->_models[$name], $data);
		} else {
			$config = $this->getModelConfig($name);
			$object = Yii::createObject($config);
			$formName = $object->formName();
			
			if (isset($data[$formName])) {
				$count = count($data[$formName]);
				
				if (isset($this->_models[$name])) {
					foreach ($data[$formName] as $key => $value) {
						if (isset($this->_models[$name][$key])) {
							if ($this->_models[$name][$key]->load($value, '')) {
								$haveSuccess = true;
							} else {
								//return false;
							}
						} else {
							$this->_models[$name][$key] = $this->_attributesToModel($name, $value);
						}
					}
					$models = $this->_models[$name];
				} else {
					$models = $this->createModels($name, $count);
					if (\yii\base\Model::loadMultiple($models, $data)) {
						$haveSuccess = true;
					} else {
						//return false;
					}
				}
				$this->_models[$name] = $models;
			}
		}
		return $haveSuccess;
	}
	
	protected function _loadModel($name, $data, $formName = null) {
		$m = $this->_models[$name];
		$k = $name;
		$haveSuccess = false;
		
		if (is_object($m)) {
			$formName = $m->formName();
			$formIdentityName = isset($this->_formIdentityName[$k]) ? $this->_formIdentityName[$k] : null;
			
			if (isset($formIdentityName) && isset($data[$formName][$formIdentityName])) {
				$success = $m->load($data[$formName][$formIdentityName], '');
				if ($success) {
					$haveSuccess = true;
				} else {
					//return false;
				}
			} else {
				$success = $m->load($data);
				if ($success) {
					$haveSuccess = true;
				} else {
					//return false;
				}
			}
		} else if (!is_array($m)) {
			$this->__deprecatedMethod($k, $m, $data);
		} else {
			$config = $this->getModelConfig($k);
			$object = Yii::createObject($config);
			$formName = $object->formName();
			if (isset($data[$formName])) {
				$count = count($data[$formName]);
				$model = $this->createModels($k, $count);
				$success = \yii\base\Model::loadMultiple($model, $data);
				if ($success) {
					$haveSuccess = true;
				} else {
					//return false;
				}
				$this->_models[$k] = $model;
			}
		}
		return $haveSuccess;
	}
	
	public function load($data, $formName = null)
    {
        $haveSuccess = \yii\base\Model::load($data);

        $this->ensureAllModelsInitialized();

        foreach ($this->_models as $name => &$m) {
			if ($this->isModelHasFlag($name, 'array')) {
				if ($this->_loadModels($name, $data, $formName)) {
					$haveSuccess = true;
				}
			} else {
				if ($this->_loadModel($name, $data, $formName)) {
					$haveSuccess = true;
				}
			}
        }

        foreach ($this->alias as $func) {
            $aliasModel = call_user_func_array($func, [$this]);
            
            if (is_array($aliasModel)) {
                \yii\base\Model::loadMultiple($aliasModel, $data);
            } else {
                $aliasModel->load($data);
            }
        }

        return $haveSuccess;
    }
	
	protected function __deprecatedMethod($k, $m, $data) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');
		$config = $this->getModelConfig($k);
		$object = Yii::createObject($config);
		$formName = $object->formName();
		$formIdentityName = isset($this->_formIdentityName[$k]) ? $this->_formIdentityName[$k] : null;
		
		if (isset($formIdentityName) && isset($data[$formName][$formIdentityName])) {
			$success = $m->load($data[$formName][$formIdentityName], '');
			if ($success) {
				$haveSuccess = true;
			} else {
				//return false;
			}
		} else {
			$success = $m->load($data);
			if ($success) {
				$haveSuccess = true;
			} else {
				//return false;
			}
		}
	}
	
	protected function _normalizeModel($name, \yii\base\BaseObject $model) {
		$config = $this->getModelConfig($name);
		unset($config['class']);
		return \Yii::configure($model, $config);
	}
	
	protected function _attributesToModel($name, array $attributes) {
		return $this->_createModel($name, [], $attributes);
	}
	
	protected function _setValues($name, array $value) {
		$model = [];
		
		foreach ($value as $key => $v) {			
			if (is_object($v)) {
				$model[$key] = $this->_normalizeModel($name, $v);
			} else {
				if (isset($this->_models[$name][$key])) {
					$model[$key] = $this->_models[$name][$key];
					$model[$key]->attributes = $v;
				} else {
					$model[$key] = $this->_attributesToModel($name, $v);
				}
			}
		}
		$this->_setModelValue($name, $model);
		
		$this->triggerForModels($model);
	}

    protected function _setValue($name, $value) {
		if (!isset($value)) return null;
		
		if ($this->isModelHasFlag($name, 'array')) {
			return $this->_setValues($name, (array) $value);
		}
		
        if (is_object($value)) {
			// It is a model
			$model = $this->setModel($name, $this->_normalizeModel($name, $value));
        } else if (is_array($value)) {
			// It is attributes of a modal
            $model = $this->_getModel($name);
			if (!isset($model)) {
				$model = $this->_models[$name] = $this->_attributesToModel($name, $value);
			} else if (is_array($model)) {
                foreach ($value as $key => $v) {
                    if (is_object($v)) {
                        $model[$key] = $this->_normalizeModel($name, $v);
                    } else if (isset($model[$key])) {
                        $model[$key]->attributes = $v;
                    } else {
                        $model[$key] = $this->_attributesToModel($name, $v);
                    }
                }
                $this->_setModelValue($name, $model);
            } else if (is_object($model)) {
                $model->load($value, '');
            }/* else {
                $model = $this->_models[$name] = $this->_createModel($name, [], $value);
            }*/
        } else {
			throw new \Exception('Invalid value assigned to model(s). ');
		}
		
		$this->triggerForModel($model);
    }
	
	protected function triggerForModels(array $models) {
		foreach ($models as $model) {
			$this->triggerForModel($model);
		}
	}
	
	protected function triggerForModel(\yii\base\BaseObject $model) {
		$model->trigger(FormModel::EVENT_SET, new FormModelEvent(['formModel' => $this]));
	}

    protected function createModels($name, $numberOfInstance = 1, $config = []) {
		if ($this->isModelMultiple($name)) {
			return $this->_createModels($name, $numberOfInstance, $config);
		} else {
			//if (YII_DEBUG) throw new \Exception('DEPRECATED');
			return $this->_createModel($name, $config);
		}
    }
	
	protected function _createModels($name, $numberOfInstance = 1, $config = []) {
        $config = ArrayHelper::merge($this->getModelConfig($name), $config);
		
		if ($this->isModelMultiple($name)) {
            return Model::createMultiple($config, [], array_pad([], $numberOfInstance, null));
        } else {
			throw new \Exception('Model "'.$name.'" is not array type. ');
        }
	}
    
    protected function _createModel($name, $config = [], $attributes = []) {
		//if (count($config)) throw new \Exception('DEPRECATED');
		//if ($this->test) throw new \Exception('y');
			
        $config = ArrayHelper::merge($this->getModelConfig($name), $config);
		$config['attributes'] = $attributes;
		return Yii::createObject($config);
    }
	
	protected function parseKey($rawKey) {
        list($key, $type) = array_pad(explode(':', $rawKey), 2, null);
		
        if (isset($type)) {
			$this->_modelFlags[$key] = \ant\helpers\ArrayHelper::trim(explode(',', $type));
			
			if ($this->isModelHasFlag($key, 'readonly')) {
				$this->_modelFlags[$key][] = 'optional';
			}
		}
		return $key;
	}
	
	protected function isModelHasFlag($key, $flagName) {
		$return = isset($this->_modelFlags[$key]) ? in_array($flagName, $this->_modelFlags[$key]) : false;
		//if ($flagName == 'readonly') throw new \Exception(($return?'y':'n').$key.print_r($this->_modelFlags,1));
		return $return;
	}

    protected function setModelConfig($rawKey, $config) {
		$key = $this->parseKey($rawKey);
			
		/*if ($this->isModelHasFlag($key, 'optional')) {
			return $this->setOptionalModelConfig($key, $config);
		}*/

        parent::setModelConfig($key, $config);
    }

    protected function setOptionalModelConfig($rawKey, $config) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');
		$key = $this->parseKey($rawKey);

        parent::setOptionalModelConfig($key, $config);
    }

    protected function isModelMultiple($name) {
        return $this->isModelHasFlag($name, 'array');
    }
	
	protected function validateModel($key, $model) {
		if (!$this->isModelHasFlag($key, 'readonly')) {
			return parent::validateModel($key, $model);
		}
		return true;
	}
	
	// Excluded alias model
	protected function saveModel($key, $model, $validate = true) {
		if (!$this->isModelHasFlag($key, 'readonly')) {
			return $model->save($validate);
		}
		return true;
	}
}