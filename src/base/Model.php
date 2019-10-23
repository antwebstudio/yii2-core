<?php

namespace ant\base;

use Yii;
use yii\helpers\ArrayHelper;

class Model extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($config, $multipleModels = [], $values = [])
    {
        $model    = Yii::createObject($config);
        $formName = $model->formName();
        $post     = isset($values) ? $values : Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $model = $multipleModels[$item['id']];
                } else {
                    $model = Yii::createObject($config);
                }
                $model->attributes = $item;
                $models[] = $model;
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
}