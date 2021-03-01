<?php
namespace ant\db\traits;

trait ActiveRecordShortcut {
    public static function create($attributes) {
        $model = null;
        try {
            $model = self::createOrFail($attributes);
        } catch (\Exception $e) {

        }
        return $model;
    }

    public static function createOrFail($attributes) {
        $model = self::make($attributes);
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        return $model;
    }

    public static function make($attributes) {
        $model = new self();
        $model->attributes = $attributes;
        return $model;
    }

    public static function updateOrCreate($conditions, $attributes = null) {
        if (!isset($attributes)) {
            $attributes = $conditions;
        } else {
            $attributes = array_merge($conditions, $attributes);
        }
        $model = self::find()->where($conditions)->one();
        if (isset($model)) {
            $model->attributes = $attributes;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        } else {
            $model = new static;
            $model->attributes = $attributes;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        }
        return $model;
    }

    public static function findOrFail($id) {
        $model = self::findOne($id);
        if (!isset($model)) throw new \Exception('Not found');
        return $model;
    }

    public function isAttributeDirty($attribute) {
        $dirtyAttributes = $this->getDirtyAttributes();
        return in_array($attribute, $dirtyAttributes);
    }
}