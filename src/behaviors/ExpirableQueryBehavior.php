<?php
namespace ant\behaviors;

class ExpirableQueryBehavior extends DateTimeAttributeQueryBehavior {
	//public $modelClass;
	public function expired() {
		return $this->owner->joinWith('expirationModel expirationModel')
			->andWhereOlderThanNow('expirationModel.expire_at');
	}
	
	public function notExpired() {
		return $this->owner->joinWith('expirationModel expirationModel')
			->andWhereNewerThanNow('expirationModel.expire_at');
	}

	public function expireLast() {
		return $this->owner->joinWith('expirationModel expirationModel')
			->orderBy('expirationModel.expire_at desc');
	}

	public function expireFirst() {
		return $this->owner->joinWith('expirationModel expirationModel')
			->orderBy('expirationModel.expire_at asc');
	}
}