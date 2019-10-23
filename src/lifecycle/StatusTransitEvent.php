<?php
namespace ant\lifecycle;

class StatusTransitEvent extends \yii\base\Event {
    public $statusAttribute;
    public $oldStatus;
    public $newStatus;
}