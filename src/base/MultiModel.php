<?php
namespace ant\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 * @var array $models
 * Example:
 * $model = new MultiModel([
 *      'models' => [
 *          'account' => $accountModel,
 *          'profile' => $profileModel
 *      ]
 * ])
 * $model->load($_POST);
 * $model->save();
 *
 * In view:
 * $form->field($model->getModel('account'), 'username')->textInput();
 */

class MultiModel extends Model {
    const EVENT_BEFORE_SAVE = 'before_save';
	
    public function init() {
        
    }
	
    /**
     * @var string
     */
    public $db = 'db';

    public $alias = [];

    /**
     * @var array
     */
    protected $_models = [];
    /**
     * @var array
     */
    protected $_optionalModels = [];

    protected $_modelsConfigs = [];

    protected $_optionalModelsConfigs = [];
	
	protected $_formIdentityName = [];

    public function setModel($key, Model $model)
    {
        return $this->_models[$key] = $model;
    }
	
	protected function getAllModelConfigs() {
		return ArrayHelper::merge($this->_modelsConfigs, $this->_optionalModelsConfigs);
	}

    public function getModelConfig($key) {
        return ArrayHelper::getValue(ArrayHelper::merge($this->_modelsConfigs, $this->_optionalModelsConfigs), $key, null);
    }

    protected function setModelConfig($key, $config) {
		$this->_formIdentityName[$key] = ArrayHelper::remove($config, 'name');
        $this->_modelsConfigs[$key] = $config;
    }

    protected function setOptionalModelConfig($key, $config) {
		$this->_formIdentityName[$key] = ArrayHelper::remove($config, 'name');
        $this->_optionalModelsConfigs[$key] = $config;
    }

    /**
     * @param array $models
     */
    public function setModels(array $models)
    {
        foreach ($models as $key => $model) {
            if ($model instanceof Model) {
                $this->setModel($key, $model);
            } else if (is_array($model)) {
                $this->setModelConfig($key, $model);
            }
        }
    }

     /**
     * @param $key
     * @param Model $model
     * @return Model
     */
    public function setOptionalModel($key, Model $model)
    {
        return $this->_optionalModels[$key] = $model;
    }

    /**
     * @param array $models
     */
    public function setOptionalModels(array $models)
    {
		//throw new \Exception('setoptionalModels');
        foreach ($models as $key => $model) {
            if ($model instanceof Model) {
                $this->setOptionalModel($key, $model);
            } else if (is_array($model)) {
                $this->setOptionalModelConfig($key, $model);
            }
        }
    }

    protected function merge($defaultConfigs, $optional = true) {
        foreach ($defaultConfigs as $key => $defaultConfig) {
            if (isset($this->_modelsConfigs[$key])) {
                $this->setModelConfig($key, ArrayHelper::merge($defaultConfig, $this->_modelsConfigs[$key]));
            } else if (isset($this->_optionalModelsConfigs[$key])) {
                $this->setOptionalModelConfig($key, ArrayHelper::merge($defaultConfig, $this->_optionalModelsConfigs[$key]));
            } else {
                if ($optional) {
                    $this->setOptionalModelConfig($key, $defaultConfig);
                } else {
                    $this->setModelConfig($key, $defaultConfig);
                }
            }
        }
    }

    protected function isModelIsset($name) {
        return isset($this->_models[$name]) || isset($this->_modelsConfigs[$name])
            || isset($this->_optionalModels[$name]) || isset($this->_optionalModelsConfigs[$name]);
    }

    protected function isOptional($key) {
        if (isset($this->_optionalModelsConfigs[$key]) || isset($this->_optionalModels[$key])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $key
     * @return Model|null
     */
    public function getModel($key, $numberOfInstance = 1, $refresh = false)
    {
        // Alias
        if (isset($this->alias[$key])) {
            $models =  call_user_func_array($this->alias[$key], [$this]);
            //throw new \Exception(print_r($models,1));
        }

        // Models
        if ($refresh || (!isset($this->_models[$key]) && !isset($this->_optionalModels[$key]))) {
            if (isset($this->_modelsConfigs[$key])) {
                $this->_models[$key] = $this->createModels($key, $numberOfInstance);
                return $this->_models[$key];
            } else if (isset($this->_optionalModelsConfigs[$key])) {
                $this->_optionalModels[$key] = $this->createModels($key, $numberOfInstance);
                return $this->_optionalModels[$key];
            } else {
				return null;
                throw new \Exception('Config for model "'.$key.'" is not set. ');
            }
        }
        return ArrayHelper::getValue(ArrayHelper::merge($this->_models, $this->_optionalModels), $key, false);
    }

    protected function createModels($key, $numberOfInstance = 1) {
        $config = ArrayHelper::getValue(ArrayHelper::merge($this->_modelsConfigs, $this->_optionalModelsConfigs), $key, false);

        if ($numberOfInstance > 1) {
            $models = [];
            for ($i = 0; $i < $numberOfInstance; $i++) {
                $models[] = Yii::createObject($config);
            }
            return $models;
        } else if ($numberOfInstance == 1) {
            return Yii::createObject($config);
        }
        return [];
    }

    /**
     * @return array
     */
    public function getModels()
    {
        $configs = $this->_modelsConfigs;

        foreach ($configs as $key => $config) {
            $this->getModel($key);
        }
        return ArrayHelper::merge($this->_models, $this->_optionalModels);
    }

    protected function ensureAllModelsInitialized() {
        $this->getModels();
    }

    /**
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = '')
    {
        $haveSuccess = parent::load($data);

        $this->ensureAllModelsInitialized();

        foreach ($this->_models as $k => &$m) {
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
        }

        foreach ($this->_optionalModels as $k => &$m) {
            $m->load($data);
        }

        foreach ($this->_optionalModelsConfigs as $name => $config) {
			$formIdentityName = isset($this->_formIdentityName[$name]) ? $this->_formIdentityName[$name] : null;
			
            $object = Yii::createObject($config);
            $formName = $object->formName();

            if (isset($data[$formName])) {
				if (isset($formIdentityName) && isset($data[$formName][$formIdentityName])) {
					//throw new \Exception($formIdentityName.'@@'.print_r($data[$formName][$formIdentityName],1).$count);
					$model = $this->createModels($name, 1);
					
					if ($model->load($data[$formName][$formIdentityName], '')) {
						$haveSuccess = true;
					} else {
						return false;
					}
				} else {
					$count = count($data[$formName]);
					$model = $this->createModels($name, $count);
					
					if (is_array($model)) {
						\yii\base\Model::loadMultiple($model, $data);
					} else {
						$model->load($data);
					}
				}
				$this->_optionalModels[$name] = $model;
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

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $this->trigger(Model::EVENT_BEFORE_VALIDATE);
        parent::validate();
        foreach ($this->getModels() as $key => $model) {
            /* @var $model Model */
            if (is_array($model)) {
                foreach ($model as &$m) {
                    $this->validateModel($key, $m);
                }
            } else if ($model !== [] && !$this->validateModel($key, $model)) {
                //$this->addErrors([$key => $model->getErrors()]);
            }
        }
        $this->trigger(Model::EVENT_AFTER_VALIDATE);
        return !$this->hasErrors();
    }

	// Separate out from validate method to ease FormModel to overwrite it.
	protected function validateModel($key, $model) {
		$success = $model->validate();
		if (!$success) {
			$this->addErrors([$key => $model->errors]);
			//$this->addError($key, $model->errors);
			//throw new \Exception(print_r($this->errors,1).print_r($model->errors,1));
			//throw new \Exception(print_r($this->errors,1).print_r($model->errors,1).\yii\helpers\Html::errorSummary($this));
		}
		return $success;
	}
	
	// Excluded alias model
	protected function saveModel($key, $model, $validate = true) {
		return $model->save($validate);
	}

    /**
     * @param bool $runValidation
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }
        $this->trigger(self::EVENT_BEFORE_SAVE);
        $success = true;
        $transaction = $this->getDb()->beginTransaction();
        try {
			$allModels = array_merge($this->getAllModelConfigs(), $this->getModels());
            foreach ($allModels as $key => $config) {
				$model = $this->getModel($key);
				
                if (is_array($model)) {
                    foreach ($model as $m) {
                        $success = $this->saveModel($key, $m);
                        if (!$success) {
                            $transaction->rollBack();
                            return false;
                        }
                    }
                } else {
					$success = $this->saveModel($key, $model, false);
                    if ($success === false) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
            
            foreach ($this->alias as $func) {
                $aliasModel = call_user_func_array($func, [$this]);

                if (is_array($aliasModel)) {
                    foreach ($aliasModel as $modelId => &$model) {
                        $success = $model->save();
                        if (!$success) {
                            $transaction->rollBack();
                            return false;
                        }
                    }
                } else {
                    $aliasModel->save();
                    if (!$success) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            if (!$this->beforeCommit()) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();   
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        return $success;
    }

    public function beforeCommit() {
        return true;
    }

    /**
     * @return \yii\db\Connection
     */
    public function getDb()
    {
        return Yii::$app->get($this->db);
    }

    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    /*
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }*/
}