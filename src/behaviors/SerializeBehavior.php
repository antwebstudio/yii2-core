<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class SerializeBehavior extends Behavior
{
	const METHOD_PHP = 'php';
	const METHOD_JSON = 'json';
	
    public $attributes = [];
    public $default = [];
	public $serializeMethod = self::METHOD_PHP;
	
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'encode',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
            ActiveRecord::EVENT_AFTER_FIND => 'decode',
            ActiveRecord::EVENT_AFTER_INSERT => 'decode',
            ActiveRecord::EVENT_AFTER_UPDATE => 'decode',
        ];
    }

	
    public function encode()
    {
        $model = $this->owner;
        foreach ($this->attributes as $attribute) {
            if (isset($model->$attribute)) {
                $model->$attribute = $this->_encode($model->$attribute);
            }
        }
    }
	
    public function decode()
    {
        $model = $this->owner;
        foreach ($this->attributes as $attribute) {
            if (($model->$attribute = $this->_decode($model->$attribute)) === false) {
                $model->$attribute = $this->default;
            }
        }
    }
	
	protected function _encode($value) {
		if (is_string($this->serializeMethod)) {
			$method = $this->serializeMethod.'Encode';
			return $this->{$method}($value);
		}
		throw new \Exception('Invalid serializeMethod set. ');
	}
	
	protected function _decode($value) {
		if (is_string($this->serializeMethod)) {
			$method = $this->serializeMethod.'Decode';
			return $this->{$method}($value);
		}
		throw new \Exception('Invalid serializeMethod set. ');
	}
	
	protected function phpEncode($value) {
		return serialize($value);
	}
	
	protected function phpDecode($value) {
		return unserialize($value);
	}
	
	protected function jsonEncode($value) {
		return Json::encode($value);
	}
	
	protected function jsonDecode($value) {
		return Json::decode($value);
	}
	
} 