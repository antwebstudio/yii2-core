<?php

namespace ant\actions;

class CheckTimezone extends \yii\base\Action {
	
	public function run() {
		$return = '';
		
		\Yii::$app->setTimezone('America/Nome');
		
		// Current date time
		$date = new \DateTime();
		$return .= 'Current PHP time: '.$date->format('Y-m-d H:i').'<br/>';
		echo 'PHP timezone: '.$date->getTimezone()->getName().'<br/>';
		echo 'Yii timezone: '.\Yii::$app->timezone.'<br/>';
		
		$sql = \Yii::$app->getDb()->createCommand('select timediff(now(),convert_tz(now(),@@session.time_zone,\'+00:00\'));')->queryAll();
		echo 'MySQL timezone: '.current($sql[0]).'<br/>';
		
		echo '<script type="text/javascript">
					var visitortime = new Date();
					var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
					document.write("Client timezone: "+visitortimezone);
			</script>';
		
		// Specific date time
		$time = '2017-07-07 14:32:54';
		echo '<hr/>';
		echo $time.'<br/>';
		$date = new \DateTime($time, new \DateTimeZone('Asia/Kuala_Lumpur'));
		echo $date->format('Y-m-d H:i').'<br/>';
		echo 'timezone: '.$date->getTimezone()->getName().'<br/>';
		
		return '';
	}
}