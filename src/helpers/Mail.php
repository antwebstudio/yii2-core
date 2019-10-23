<?php
namespace ant\helpers;

class Mail
{
	public static function getDefaultFrom() {
		if (function_exists('env')) {
			return [env('ROBOT_EMAIL') => \Yii::$app->name];
		} else {
			return 'noreply@example.com';
		}
	}
}
