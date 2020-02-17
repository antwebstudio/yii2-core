<?php
namespace ant\behaviors;

use Yii;

class BlameableBehavior extends \yii\behaviors\BlameableBehavior
{
    protected function getValue($event)
    {
        if ($this->value === null && Yii::$app->has('user')) {
            $userId = isset(Yii::$app->session) ? Yii::$app->get('user')->id : null;
            if ($userId === null) {
                return $this->getDefaultValue($event);
            }

            return $userId;
        } elseif ($this->value === null) {
            return $this->getDefaultValue($event);
        }

        return parent::getValue($event);
    }
}
