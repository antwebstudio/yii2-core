<?php

namespace ant\mail;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\di\Instance;
use yii\queue\Queue;
use shaqman\mailer\queuemailer\jobs\SendMessageJob;
use shaqman\mailer\queuemailer\jobs\SendMultipleMessagesJob;

class QueueableMailer extends \shaqman\mailer\queuemailer\Mailer {
    public $mailqueue = 'mailer';
    public $messageClass = 'ant\mail\QueueableMessage';

    public function init() {
        $this->syncMailer['messageClass'] = $this->messageClass;
        return parent::init();
    }

    public function send($message) {
        return $this->syncMailer->send($message);
    }

    public function sendMultiple(array $message) {
        return $this->syncMailer->sendMultiple($message);
    }

    public function queue($message) {
        $message->mailer = null;
        return $this->queue->push(new SendMessageJob([
            'message' => Instance::ensure($message, MessageInterface::class),
            'mailer' => $this->mailqueue,
        ]));
    }

    public function queueMultiple(array $messages) {
        foreach ($messages as $message) {
            $message = Instance::ensure($message, MessageInterface::class);
        }

        $this->queue->push(new SendMultipleMessagesJob([
            'messages' => $messages,
            'mailer' => $this->mailqueue,
        ]));
    }
}