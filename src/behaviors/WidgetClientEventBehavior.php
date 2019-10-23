<?php
namespace ant\behaviors;

class WidgetClientEventBehavior extends \yii\base\Behavior {
	public $clientEvents;
	public $clientEventMap;
	
	public function registerClientEvents($id) {
        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                if (isset($this->clientEventMap[$event])) {
                    $eventName = $this->clientEventMap[$event];
                } else {
                    $eventName = $event;
                }
                $js[] = "jQuery('#$id').on('$eventName', $handler);";
            }
            $this->owner->getView()->registerJs(implode("\n", $js));
        }
	}
}