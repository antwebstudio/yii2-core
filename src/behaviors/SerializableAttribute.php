<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use ant\helpers\Collection;
use yii\helpers\Json;

class SerializableAttribute extends Behavior {
	
	public $serializeMethod = self::METHOD_JSON;
	public $useCollection = false;
	
	
	const METHOD_PHP = 'php';
	const METHOD_JSON = 'json';
	
    public $attributes = [];
    public $default = [];
	
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
	
	public function setSerializableAttributes(array $attributes) {
		foreach ($attributes as $name => $value) {
			$this->setSerializableAttribute($name, $value);	
		}
	}

	protected function setSerializableAttribute($name, $value) {
		if (!in_array($name, $this->attributes)) {
			throw new \Exception('Only serializable attribute is fillable. ');
		}
		
		if ($this->useCollection) {
			if (isset($this->owner->{$name})) {
				if (is_array($this->owner->{$name})) {
					throw new \Exception('Please use setSerializableAttribute() method to set value for serializable attribute when useCollection is set to true instead of using load(). Hint: You may simply remove the serializable attribute "'.$name.'" from safe attributes to avoid this attribute is set when load() is called. ');
				}
				$this->owner->{$name}->set($value);
			} else {
				$this->owner->{$name} = new Collection($value);
			}
		} else {
			$this->owner->{$name} = $value;
		}
	}
	
    public function encode()
    {
        $model = $this->owner;
        foreach ($this->attributes as $attribute) {
            if (isset($model->{$attribute})) {
                $model->{$attribute} = $this->_encode($model->{$attribute});
            }
        }
    }
	
    public function decode()
    {
        $model = $this->owner;
        foreach ($this->attributes as $attribute) {
            if (($model->{$attribute} = $this->_decode($model->{$attribute})) === false) {
                $model->{$attribute} = $this->default;
            }
			
			if ($this->useCollection) {
				$data = $model->{$attribute} ?? [];
				$model->{$attribute} = new Collection($data, [], !isset($model->{$attribute}));
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