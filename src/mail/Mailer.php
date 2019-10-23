<?php

namespace ant\mail;

class Mailer extends \yii\swiftmailer\Mailer {
	public $silentException = false;
    public $messageClass = 'ant\mail\Message';
	
	const EVENT_SEND_EMAIL_FAIL = 'emailFailed';
	
	
	public function init() {
		return parent::init();
	}
	protected function sendMessage($message)
    {
		$sent = false;
		try {
			$sent = parent::sendMessage($message);
			if (!$sent) {
				$this->onSendFail($message);
			}
		} catch (\Exception $ex) {
			$this->onSendFail($message);
			if (!$this->silentException) throw $ex;
		}
		return $sent;
    }
	
	protected function onSendFail($message) {
		$message->attempt++;
		$this->trigger(self::EVENT_SEND_EMAIL_FAIL, new \ant\mail\events\MailerEvent(['message' => $message]));
	}
}