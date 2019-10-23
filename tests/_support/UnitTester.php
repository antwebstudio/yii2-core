<?php

use \yii\console\widgets\Table;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

   /**
    * Define custom actions here
    */
	
	public function invokeMethod($object, $methodName, $arguments = []) {
		$class = new \ReflectionClass($object);
		$method = $class->getMethod($methodName);
		$method->setAccessible(true);
		return $method->invokeArgs($object, $arguments);
		
	}
	
	public function getProperty($object, $propertyName) {
		$class = new \ReflectionClass($object);
		$property = $class->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($object);
	}
	
	public function setProperty($object, $propertyName, $value) {
		$class = new \ReflectionClass($object);
		$property = $class->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->setValue($object, $value);
	}
	
	public function renderDbTable($name, $select = null) {
		
		$query = (new \yii\db\Query)->from($name);
		if (isset($select)) {
			$query->select($select);
			$columns = is_array($select) ? $select : explode(',', str_replace(' ', '', $select));
		} else {			
			$rows = Yii::$app->db->createCommand('SHOW FULL COLUMNS FROM '.$name)->queryAll();
			$columns = [];
			foreach ($rows as $r) {
				$columns[] = $r['Field'];
			}
		}
		$rows = $query->all();
		
		return "\n".$name."\n".Table::widget([
			'headers' => $columns,
			'rows' => $rows,
			'screenWidth' => 120,
			'chars' => [
				Table::CHAR_TOP => '-',
				Table::CHAR_TOP_MID => '+',
				Table::CHAR_TOP_LEFT => '+',
				Table::CHAR_TOP_RIGHT => '+',
				Table::CHAR_BOTTOM => '-',
				Table::CHAR_BOTTOM_MID => '+',
				Table::CHAR_BOTTOM_LEFT => '+',
				Table::CHAR_BOTTOM_RIGHT => '+',
				Table::CHAR_LEFT => '|',
				Table::CHAR_LEFT_MID => '+',
				Table::CHAR_MID => '-',
				Table::CHAR_MID_MID => '+',
				Table::CHAR_RIGHT => '|',
				Table::CHAR_RIGHT_MID => '+',
				Table::CHAR_MIDDLE => '|',
			],
		])."\n";
	}
}
