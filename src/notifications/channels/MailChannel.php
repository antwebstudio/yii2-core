<?php
namespace ant\notifications\channels;

use tuyakhov\notifications\NotifiableInterface;
use tuyakhov\notifications\NotificationInterface;

class MailChannel extends \tuyakhov\notifications\channels\MailChannel
{
	public $developerEmail;
	
    public function send(NotifiableInterface $recipient, NotificationInterface $notification)
    {
        /**
         * @var $message MailMessage
         */
		$to = $recipient->routeNotificationFor('mail');
		if (YII_DEBUG || !YII_ENV_PROD) {
			$to = isset($this->developerEmail) ? $this->developerEmail : $from;
		}
        $message = $notification->exportFor('mail');
		
		if (isset($message->from)) {
			$from = $message->from;
		} else if (is_callable($this->from)) {
			$from = call_user_func_array($this->from, [$recipient, $notification]);
		} else {
			$from = $this->from;
		}
		
        return $this->mailer->compose($message->view, $message->viewData)
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($message->subject)
            ->send();
    }
}
