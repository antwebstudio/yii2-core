<?php
namespace ant\behaviors;

class EventHandlerBehavior extends \yii\base\Behavior {
	public $events;
	
	public function events()
    {
        return $this->events;
    }
}