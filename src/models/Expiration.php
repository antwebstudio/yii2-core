<?php

namespace ant\models;

use Yii;

/**
 * This is the model class for table "model_expiration".
 *
 * @property int $id
 * @property int $model_class_id
 * @property string $expire_at
 * @property int $renew_count
 * @property string $renewed_at
 * @property string $created_at
 */
class Expiration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%model_expiration}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_class_id'], 'required'],
            [['model_class_id'], 'integer'],
            [['expire_at', 'renewed_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_class_id' => 'Model Class ID',
            'expire_at' => 'Expire At',
            'renew_count' => 'Renew Count',
            'renewed_at' => 'Renewed At',
            'created_at' => 'Created At',
        ];
    }
}
