<?php
namespace ant\traits;

use Yii;
use yii\caching\TagDependency;
use yii\caching\ChainedDependency;

trait HasQueryCache {
	protected $cacheComponents;
	
	public function behaviors() {
		
	}
	
	public static function findWithCache($extraDependency = []) {
		$cacheTime = defined('static::QUERY_CACHE_TIME') ? static::QUERY_CACHE_TIME :7200;
		return static::find()->cache($cacheTime, static::getCacheDependency($extraDependency));
	}
	
	public static function getCacheDependency($extraDependency = []) {
		$extraDependency[] = new TagDependency(['tags' => static::getCacheTag()]);
		return new ChainedDependency(['dependencies' => $extraDependency]);
	}
	
	public static function getCacheTag() {
		return static::class;
	}
	
	public function clearQueryCache() {
		foreach ($this->getCacheComponents() as $cacheComponent) {
			TagDependency::invalidate($cacheComponent, static::getCacheTag());
		}
	}
	
	protected function getCacheComponents() {
		if (!isset($this->cacheComponents)) {
			$components = [];
			if (isset(Yii::$app->cache)) $components[] = Yii::$app->cache;
			if (isset(Yii::$app->frontendCache)) $components[] = Yii::$app->frontendCache;
			if (isset(Yii::$app->backendCache)) $components[] = Yii::$app->backendCache;
		} else {
			return $this->cacheComponents;
		}
		
		return $components;
	}
}