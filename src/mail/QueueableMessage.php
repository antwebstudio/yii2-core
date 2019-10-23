<?php
namespace ant\mail;

class QueueableMessage extends Message {
    public function queue() {
        return $this->mailer->queue($this);
    }
}