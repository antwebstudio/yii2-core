<?php 
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class DefaultValueBehavior extends Behavior
{
	public $attributes = [];

	public function events()
	{
		return [
			ActiveRecord::EVENT_INIT => 'eventInit',
		];
	}

	public function eventInit()
	{
		foreach ($this->attributes as $key => $value) 
		{
			if (is_callable($value)) $value = call_user_func_array($value, [$this]);
			$this->owner->$key = $value;
		}
	}
}