<?php
namespace ant\mail;

class Message extends \yii\swiftmailer\Message {
    public $attempt = 0;
}