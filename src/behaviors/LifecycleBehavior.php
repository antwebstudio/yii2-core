<?php
namespace ant\behaviors;

use yii\db\ActiveRecord;

class LifecycleBehavior extends \cebe\lifecycle\LifecycleBehavior {
    const EVENT_AFTER_STATUS_TRANSIT = 'after_status_transit';

    public $validStatus;

    public $statusModel = 'ant\lifecycle\Status';

    public $statusModelConfig = [];
	
	public $statusTransit = [];

    protected $oldStatusValue;

    public function events() {
        return \yii\helpers\ArrayHelper::merge(parent::events(), [
            ActiveRecord::EVENT_AFTER_UPDATE => [$this, 'afterUpdate'],
        ]);
    }
	
	public function getStatusHtml($attribute = null) {
		return $this->getStatus($attribute)->getHtml();
	}

    public function getStatusText($attribute = null) {
        return $this->getStatus($attribute)->getText();
    }

    public function getStatus($attribute = null) {
        $attribute = isset($attribute) ? $attribute : $this->statusAttribute;

        $config = $this->statusModelConfig;
        $config['class'] = $this->statusModel;
        $config['model'] = $this->owner;
        $config['attribute'] = $attribute;
		$config['statusTransit'] = isset($this->statusTransit[$attribute]) ? $this->statusTransit[$attribute] : null;
        return \Yii::createObject($config);
    }

    public function setStatus($value, $attribute = null) {
        $attribute = isset($attribute) ? $attribute : $this->statusAttribute;

        $this->getStatus($attribute)->transit($value);
		
		return $this;
    }

    public function getStatusOptions($attribute = null) {
        $attribute = isset($attribute) ? $attribute : $this->statusAttribute;

        if (isset($this->validStatus)) {
            $options = [];
            foreach ($this->validStatus as $status) {
                $options[$status] = $this->getStatus($attribute)->getTextByValue($status);
            }
            return $options;
        }
    }

    public function handleBeforeSave()
	{
        $oldStatus = $this->oldStatusValue = $this->owner->getOldAttribute($this->statusAttribute);
		$newStatus = $this->owner->getAttribute($this->statusAttribute);
		
		if ($this->getStatus()->checkTransitPermission($oldStatus, $newStatus) !== true) {
			throw new \Exception('Transit of status is prohibited. ');
		}
		return parent::handleBeforeSave();
	}


    public function afterUpdate($event) {
        $this->owner->trigger(self::EVENT_AFTER_STATUS_TRANSIT, new \ant\lifecycle\StatusTransitEvent([
            'statusAttribute' => $this->statusAttribute,
            'oldStatus' => $this->oldStatusValue, // $this->owner->getOldAttribute($this->statusAttribute),
            'newStatus' => $this->owner->getAttribute($this->statusAttribute),
        ]));
    }
}