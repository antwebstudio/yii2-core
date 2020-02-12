<?php

namespace ant\test;

/**
 * User fixture
 */
class ActiveFixture extends \yii\test\ActiveFixture
{
	public static function create($attributes = []) {
		$fixture = new static;
		$className = $fixture->modelClass;
		$model = new $className;
		$model->attributes = array_merge($fixture->defaultAttributes, $attributes);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
	}
}
