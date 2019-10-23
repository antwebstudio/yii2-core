<?php
namespace ant\traits;

trait FixtureTrait {
	public static function create($attributes = []) {
		$model = new self;
		$modelClass = $model->modelClass;
		
		$model = new $modelClass;
		$model->attributes = $attributes;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
	}
}