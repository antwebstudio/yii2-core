<?php
namespace ant\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class IpBehavior extends AttributeBehavior {

    public $createdIpAttribute = 'created_ip';

    public $updatedIpAttribute = 'updated_ip';

    public $value;
	
	public $preserveNonEmptyValues = false;

    public function init()
    {
        parent::init();

        if (empty($this->attributes))
        {
            $this->attributes =
            [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdIpAttribute, $this->updatedIpAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedIpAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value !== null) {
            return is_callable($this->value) ? call_user_func($this->value, $event) : $this->value;
		} else if (Yii::$app instanceof Yii\console\Application) {
			return '::1';
        } else {
            return Yii::$app->request->userIp;
        }
    }

    public function setIp($attribute)
    {
        $this->owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
