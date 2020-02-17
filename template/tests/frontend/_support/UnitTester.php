<?php
namespace tests\frontend;

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
}
