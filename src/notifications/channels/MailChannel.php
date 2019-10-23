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
		$from = isset($message->from) ? $message->from : $this->from;
		$to = $recipient->routeNotificationFor('mail');
		if (YII_DEBUG) {
			$to = isset($this->developerEmail) ? $this->developerEmail : $from;
		}
        $message = $notification->exportFor('mail');
        return $this->mailer->compose($message->view, $message->viewData)
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($message->subject)
            ->send();
    }
}
