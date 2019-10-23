<?php
namespace ant\helpers;

use \yii\helpers\StringHelper;

class RecurringHelper {
	public static $daysOfWeek = ['mo', 'tu', 'we', 'th', 'fr', 'sa', 'su'];
	public static $type = ['secondly', 'minutely', 'hourly', 'daily', 'weekly', 'monthly', 'yearly'];
	
	public static function getNextDateTime($dateTime, $period, $periodType) {
		$typeWord = self::$type[$periodType];
		
		if (StringHelper::endsWith($typeWord, 'ly')) {
			$typeWord = substr($typeWord, 0, strlen($typeWord) - 2);
			if ($typeWord == 'dai') $typeWord = 'day';
		}
		$dateTime->modify('+'.$period.' '.$typeWord);
		
		return $dateTime;
	}
}