<?php
namespace ant\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
	public static function toJson($array) {
		return json_encode($array);
	}
	
	public static function getFirst($array, $count) {
		$chunked = array_chunk($array, $count, true);
		return isset($chunked[0]) ? $chunked[0] : [];
	}
	
	public static function toggleValue($array, $value) {
		$remove = self::removeValue($array, (string) $value);
		
		if (count($remove) == 0) {
			$array[] = $value;
		}
		return $array;
	}
	
	public static function combine($keyArray, $valueArray = null) {
		if (!isset($valueArray)) $valueArray = $keyArray;
		
		return array_combine($keyArray, $valueArray);
	}
	
	public static function count($array, $callback = null) {
		if (isset($callback)) {
			$count = 0;
			foreach ($array as $key => $value) {
				if (call_user_func_array($callback, [$value, $key])) {
					$count++;
				}
			}
			return $count;
		} else {
			return count($array);
		}
	}
	
	public static function trim($value) {
		if (is_array($value)) {
			$return = [];
			foreach ($value as $key => $v) {
				$return[$key] = self::trim($v);
			}
			return $return;
		} else {
			return trim($value);
		}
	}
	
	public static function each($arrays, $callback) {
		$return = [];
		foreach ($arrays as $key => $value) {
			$return[$key] = call_user_func_array($callback, [$value, $key]);
		}
		return $return;
	}
	
	public static function implode($delimiter, $array, $callback = null) {
		if (isset($callback)) {
			return implode($delimiter, static::each($array, $callback));
		} else {
			return implode($delimiter, $array);
		}
	}
	
	public static function allKeysExists($keys, $array, $caseSensitive = true) {
		$keys = (array) $keys;
		foreach ($keys as $key) {
			if (!self::keyExists($key, $array, $caseSensitive)) {
				return false;
			}
		}
		return true;
	}
	
    public static function combinations($arrays, $i = 0)
    {
        if (!isset($arrays[$i]))
        {
            return [];
        }

        if ($i == count($arrays) - 1)
        {
            return $arrays[$i];
        }

        $tmp = self::combinations($arrays, $i + 1);

        $result = [];

        foreach ($arrays[$i] as $v)
        {
            foreach ($tmp as $t)
            {
                $result[] = is_array($t) ? array_merge([$v], $t) : [$v, $t];
            }
        }

        return $result;
    }
	
	public static function getValues($array, $key, $default = null) {
		$return = [];
		foreach ($array as $a) {
			$return[] = self::getValue($a, $key, $default);
		}
		return $return;
	}

    public static function setValue(&$array, $path, $value)
    {
        if ($path === null) {
            $array = $value;
            return;
        }
        $keys = is_array($path) ? $path : explode('.', $path);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            if (!is_array($array[$key])) {
                $array[$key] = [$array[$key]];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }

    public static function removeEmpty(array $array) {
        $result = [];
        foreach ($array as $key => $value) {
            if (isset($value) && trim($value) != '') {
                $result[$key] = $value;
            }
        }
        return $result;
    }
	
	public static function indexOf($value, array $array) {
		return array_search($value, $array);
	}
}