<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use ant\models\ModelClass;
use ant\helpers\DateTime;
use ant\models\Expiration;

class Expirable extends Behavior {
	public $modelClass;
	
	protected $_model;

	public function events() {
		return [
			ActiveRecord::EVENT_AFTER_UPDATE => [$this, 'afterUpdate'],
			ActiveRecord::EVENT_AFTER_INSERT => [$this, 'afterInsert'],
		];
	}

	public function expireAfter($seconds) {
		$date = new DateTime();
		$date->addSeconds($seconds);
		$this->setExpireAt($date->format(DateTime::FORMAT_MYSQL));

		return $this->owner;
	}
	
	public function expireAfterDays($days, $setTimeAsEndOfDay = false) {
		$date = new DateTime();
		$date->addDays($days);
		if ($setTimeAsEndOfDay) $date->setTimeAsEndOfDay();

		$this->setExpireAt($date->format(DateTime::FORMAT_MYSQL));

		return $this->owner;
	}

	public function extendExpiryTime($seconds) {
		if (isset($this->expireAt)) {
			$date = new DateTime($this->expireAt);
			$date->addSeconds($seconds);

			$this->setExpireAt($date);
			$this->owner->expirationModel->renew_count = new \yii\db\Expression('renew_count + 1');
			//$this->owner->expirationModel->updateCounters(['renew_count' => 1]);

			return $this->owner;
		} else {
			throw new \Exception('Expiry date is never set, so cannot extend from it. (Use renew or setExpireAt to set new expiry date)');
		}
	}

	// Extend mean start from current expiry time
	public function extendExpiryDate($days) {
		return $this->extendExpiryTime($days * 24 * 3600);
	}
	
	// Renew mean start from now
	public function renewSeconds($seconds) {
		$date = new DateTime($this->owner->{$this->expireAtAttribute});
		$date->addSeconds($seconds);
		$this->owner->{$this->expireAtAttribute} = $date;
		
		return $this->owner->save();
	}
	
	public function renewDays($days) {
		$date = new DateTime($this->owner->{$this->expireAtAttribute});
		$date->addDays($days)->setTimeAsEndOfDay();
		$this->owner->{$this->expireAtAttribute} = $date;
		
		return $this->owner->save();
	}

	public function setExpireAt($value) {
		$model = $this->ensureExpirationModel();
		$model->expire_at = $value;

		return $this->owner;
	}

	public function getRenewCount() {
		if (isset($this->owner->expirationModel)) {
			return $this->owner->expirationModel->renew_count;
		}
		return 0;
	}
	
	public function getExpireAt() {
		if (isset($this->owner->expirationModel) && isset($this->owner->expirationModel->expire_at)) {
			return new DateTime($this->owner->expirationModel->expire_at);
		}
	}

	public function getExpirationModel() {
		return $this->owner->hasOne(Expiration::class, ['model_id' => 'id'])
			->onCondition(['model_class_id' => ModelClass::getClassId($this->modelClass)]);
	}
	
	public function afterInsert() {
		if (isset($this->_model)) {
			$this->_model->model_id = $this->owner->id;
			if (!$this->_model->save()) throw new \Exception(print_r($this->_model->errors, 1));
			
			$this->owner->link('expirationModel', $this->_model);
		}
	}

	public function afterUpdate() {
		if (isset($this->owner->expirationModel) && !$this->owner->expirationModel->save()) throw new \Exception(print_r($this->owner->expirationModel->errors, 1));
	}

	protected function ensureExpirationModel() {
		if (!isset($this->owner->expirationModel)) {
			$model = new Expiration;
			$model->model_id = $this->owner->id;
			$model->model_class_id = ModelClass::getClassId($this->modelClass);
			
			if ($this->owner->isNewRecord) {
				$this->_model = $model;
			} else {
				$this->owner->link('expirationModel', $model);

				if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
			}

			return $model;
		}
		return $this->owner->expirationModel;
	}
}