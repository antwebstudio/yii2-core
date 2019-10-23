<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class SendEmailBehavior extends Behavior
{
	public $mailer;
	public $mailerComponentName = 'mailer';
    public $template = [];
	public $messageConfig = [];
	public $throwException = false;
	
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'sendEmailHandler',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'sendEmailHandler',
            ActiveRecord::EVENT_AFTER_FIND => 'sendEmailHandler',
            ActiveRecord::EVENT_AFTER_INSERT => 'sendEmailHandler',
            ActiveRecord::EVENT_AFTER_UPDATE => 'sendEmailHandler',
        ];
    }
	
    public function sendEmailHandler($event)
    {
		if (isset($this->template[$event->name])) {
			$mailer = isset($this->mailer) ? $this->mailer : \Yii::$app->{$this->mailerComponentName};
			$template = $this->template[$event->name];
			$message = $mailer->compose($template, ['model' => $event->sender]);
			
			if (isset($this->messageConfig[$event->name])) {
				\Yii::configure($message, $this->messageConfig[$event->name]);
			}
			
			try {
				$event->valid = $message->send();
			} catch (\Exception $ex) {
				if ($this->throwException) throw $ex;
			}
			
		}
    }
	
} 