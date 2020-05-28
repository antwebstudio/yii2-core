<?php
namespace ant\behaviors;

use yii\db\ActiveRecord;
use yii\caching\TagDependency;

class ActiveRecordQueryCache extends \yii\base\Behavior {
	//public $cacheComponents;
	//public $cacheTag;
	
	public function events() {
		return [
            ActiveRecord::EVENT_AFTER_INSERT => 'clearQueryCache',
            ActiveRecord::EVENT_AFTER_UPDATE => 'clearQueryCache',
			ActiveRecord::EVENT_AFTER_DELETE => 'clearQueryCache',
		];
	}
	
	public function clearQueryCache() {
		$this->owner->clearQueryCache();
		/*foreach ($this->getCacheComponents() as $cacheComponent) {
			TagDependency::invalidate($cacheComponent, $this->getCacheTag());
		}*/
	}
	
	/*protected function getCacheTag() {
		if (!isset($this->cacheTag)) {
			return get_class($this->owner);
		}
	}*/
	
	/*protected function getCacheComponents() {
		if (!isset($this->cacheComponents)) {
			$components = [];
			if (isset(Yii::$app->cache)) $components[] = Yii::$app->cache;
			if (isset(Yii::$app->frontendCache)) $components[] = Yii::$app->frontendCache;
			if (isset(Yii::$app->backendCache)) $components[] = Yii::$app->backendCache;
		} else {
			return $this->cacheComponents;
		}
		
		return $components;
	}*/
}