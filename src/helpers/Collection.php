<?php
namespace ant\helpers;

class Collection extends \yii2mod\collection\Collection {
	
	public function __set($name, $value) {
		throw new \Exception('set: '.$name);
	}
	
	public function set($name, $value = null) {
        if (is_iterable($name)) {
            return $this->override($this->merge($name));
        }
		$items = $this->toArray();
		return $this->override(static::dataSet($items, $name, $value));
	}
	
	public function mergeAndOverride($value) {
		return $this->override($this->merge($value));
	}
	
	public function fill($name, $value = null) {
        if (is_iterable($name)) {
			throw new \Exception('Not yet implemented ');
			/*$values = $name;
			foreach ($values as $name => $value) {
				$this->{$name}->fill($value);
			}*/
        }
		$items = $this->toArray();
		return $this->override(static::dataFill($items, $name, $value));
	}
	
	protected function override($newValue) {
        $this->items = $this->getArrayableItems($newValue);
		return $this;
	}
	
	protected static function dataFill(&$target, $key, $value)
    {
        return static::dataSet($target, $key, $value, false);
    }
	
	protected static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }
	
	protected static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
	
	protected static function dataSet(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (! static::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    static::dataSet($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (static::accessible($target)) {
            if ($segments) {
                if (! static::exists($target, $segment)) {
                    $target[$segment] = [];
                }
                static::dataSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! static::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                static::dataSet($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                static::dataSet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }

}