<?php
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\View;

class NotificationBar extends \yii\base\Widget {
	public $message;
	public $startAt;
	public $endAt;
	public $marquee = false;
	
	public function init() {
		$this->startAt = isset($this->startAt) ? new \ant\helpers\DateTime($this->startAt) : null;
		$this->endAt = isset($this->endAt) ? new \ant\helpers\DateTime($this->endAt) : null;
		ob_start();
	}
	
	public function run() {
		$content = ob_get_contents();
		ob_end_clean();
		
		return $this->render('notification-bar', [
			'content' => isset($content) && $content ? $content : $this->message,
		]);
	}
	
	public function shouldShow() {
		$now = new \ant\helpers\DateTime;
		
		if (isset($this->startAt) && isset($this->endAt)) {
			return $now >= $this->startAt && $now <= $this->endAt;
		} else if (isset($this->startAt)) {
			return $now >= $this->startAt;
		} else if (isset($this->endAt)) {
			return $now <= $this->endAt;
		}
		return true;
	}
}