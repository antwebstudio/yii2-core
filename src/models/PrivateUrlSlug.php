<?php

namespace ant\models;

use Yii;

/**
 * This is the model class for table "em_model_private_url".
 *
 * @property integer $id
 * @property integer $model_class_id
 * @property integer $model_id
 * @property string $slug
 */
class PrivateUrlSlug extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%model_private_url}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_class_id', 'model_id', 'slug'], 'required'],
            [['model_class_id', 'model_id'], 'integer'],
            [['slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_class_id' => 'Model Class ID',
            'model_id' => 'Model ID',
            'slug' => 'Slug',
        ];
    }
	
	public function getModel() {
		return \ant\models\ModelClass::getModel($this->model_class_id, $this->model_id);
	}
}
