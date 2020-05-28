<?php
namespace ant\traits;

use yii\caching\TagDependency;

trait ActiveRecordTrait {
	use HasQueryCache;
	
	public function behaviors() {
		
	}
	
	public function behaviorsActiveRecordTrait() {
		return [
			\ant\behaviors\ActiveRecordQueryCache::class,
		];
	}
	
	public function withAttributes($attributes = []) {
		$this->setAttributes($attributes);
		return $this;
	}
}