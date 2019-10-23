<?php
namespace ant\helpers;

use Yii;

class TemplateHelper {
	public static function renderAttribute($model, $attribute) {
		$format = isset($attribute['format']) ? $attribute['format'] : 'text';
		$content = isset($attribute['content']) ? $attribute['content'] : null;
		
		if ($content === null) {
            return Yii::$app->formatter->format(self::getAttributeValue($model, $attribute), $format);
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
	}
	
	protected static function getAttributeValue($model, $attribute) {
		if (is_array($attribute)) {
			$value = isset($attribute['value']) ? $attribute['value'] : null;
			$attribute = isset($attribute['attribute']) ? $attribute['attribute'] : null;
		}
		
        if (isset($value)) {
            if (is_string($value)) {
                return ArrayHelper::getValue($model, $value);
            } else {
                return call_user_func($value, $model, $attribute);
            }
        } elseif ($attribute !== null) {
            return ArrayHelper::getValue($model, $attribute);
        }
        return null;
	}
	
	public static function renderTemplate($template, $callback, $callbackParams = []) {
		// Search for pattern {name}
		return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($callback, $callbackParams) {
            $name = $matches[1]; // Section token name
			
			if (is_callable($callbackParams)) {
				$callbackParams = call_user_func_array($callbackParams, [$name]);
			}
			
			if (is_callable($callback)) {
				array_unshift($callbackParams, $name);
				return call_user_func_array($callback, $callbackParams);
			} else if (is_array($callback) && isset($callback[$name])) {
				return call_user_func_array($callback[$name], $callbackParams);
			} else {
				return '';
			}

            /*if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }

            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }*/
        }, $template);
	}
}
	