<?php

namespace ant\models;

use Yii;

/**
 * This is the model class for table "em_model_class".
 *
 * @property integer $id
 * @property string $class_name
 */
class ModelClass extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%model_class}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_name' => 'Class Name',
        ];
    }
	
	public static function getModel($modelClassId, $modelId) {
		$className = self::getClassName($modelClassId);
		return $className::findOne($modelId);
	}

    public static function getClassName($modelClassId) {
        return self::findOne($modelClassId)->class_name;
    }

    public static function getClassId($modelClassName) {
        $model = self::find()->andWhere(['class_name' => $modelClassName])->one();
        if (!isset($model)) {
            $model = new self;
            $model->class_name = $modelClassName;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        }
        return $model->id;
    }
}
