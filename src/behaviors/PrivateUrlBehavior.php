<?php
namespace ant\behaviors;

use ant\models\PrivateUrlSlug;

class PrivateUrlBehavior extends \yii\base\Behavior {
	public $modelClassId;
	public $route;
	public $autoSlug;
	public $uniqueSlug = false;
	
	protected $_slugs;
	protected $_default;
	protected $_defaultSlug;

	public function attach($owner) {
		parent::attach($owner);
		
		if (!$this->owner->isNewRecord) {
			$this->_slugs = PrivateUrlSlug::find()->andWhere([
				'model_class_id' => $this->modelClassId,
				'model_id' => $this->owner->id,
			])->indexBy('slug');
		}
	}
	
	public function getPrivateRoute($params = []) {
		$defaultSlug = $this->getDefaultPrivateUrlSlug();
		
		if (!isset($defaultSlug)) {
			$defaultSlug = $this->createPrivateUrl($this->autoGenerateSlug());
		}
		
		if ($defaultSlug->is_unique) {
			// No need id if the slug is unique
			return \yii\helpers\ArrayHelper::merge([$this->route, 'privateSlug' => $defaultSlug->slug], $params);
		} else {
			return \yii\helpers\ArrayHelper::merge([$this->route, 'id' => $this->owner->id, 'privateSlug' => $defaultSlug->slug], $params);
		}
	}

	public function getUniquePrivateUrlSlug() {
		return $this->getPrivateUrlSlugs()->andWhere(['is_unique' => 1, 'is_default' => 1]);
	}

	public function getPrivateUrlSlugs() {
		return $this->owner->hasMany(PrivateUrlSlug::className(), ['model_id' => 'id'])
			->andOnCondition(['model_class_id' => \ant\models\ModelClass::getClassId(get_class($this->owner))]);
	}
	
	public function getPrivateUrl($absolute = false) {
		return \yii\helpers\Url::to($this->owner->getPrivateRoute(), $absolute);
	}
	
	public function createPrivateUrl($slug, $isDefault = true, $isUnique = null) {
		if ($this->owner->isNewRecord) throw new \Exception('Record need to be saved before can create private url. ');
		
		$this->_slugs = null;
		$this->_defaultSlug = null;
		$this->_default = null;
		
		$model = new PrivateUrlSlug;
		$model->model_class_id = $this->modelClassId;
		$model->model_id = $this->owner->id;
		$model->slug = $slug;
		$model->is_unique = isset($isUnique) ? ($isUnique ? 1 : 0) : ($this->uniqueSlug ? 1 : 0);
		$model->is_default = $isDefault ? 1 : 0;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
    }
	
	public function validatePrivateUrlSlug($slug) {
		return isset($this->allSlugs[$slug]);
	}
	
	public function getDefaultPrivateUrlSlug() {
		if (!isset($this->_defaultSlug)) {
			foreach ($this->getAllSlugs() as $slug) {
				if ($slug->is_default) {
					$this->_defaultSlug = $slug;
					
					return $this->_defaultSlug;
				}
			}
		}
		return $this->_defaultSlug;
	}
	
	protected function autoGenerateSlug() {
		if (is_callable($this->autoSlug)) {
			return call_user_func_array($this->autoSlug, [$this->owner]);
		}
		throw new \Exception('autoSlug need to be set or you may call "createPrivateUrl" manually. ');
	}
	
	protected function getAllSlugs() {
		if ($this->owner->isNewRecord) throw new \Exception('Record need to be saved first. ');
		
		if (!isset($this->_slugs)) {	
			$this->_slugs = PrivateUrlSlug::find()->andWhere([
				'model_class_id' => $this->modelClassId,
				'model_id' => $this->owner->id,
			])->indexBy('slug')->all();
		}
		return $this->_slugs;
	}
}