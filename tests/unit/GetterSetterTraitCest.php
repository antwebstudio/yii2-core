<?php

use yii\helpers\ArrayHelper;
use ant\traits\GetterSetterTrait; 

class GetterSetterTraitCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testProperty(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();

        $I->assertEquals('this is test 1', $testClass->test1);

        $testClass->test1 = 'this is test one';
        $I->assertEquals('this is test one', $testClass->test1);
    }

    public function testMethod(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();

        $I->assertEquals(5, $testClass->sum(2, 3));
    }

    public function testYii2OfficialCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();

        $I->assertEquals('this is test 2', $testClass->test2);

        $testClass->test2 = 'this is test two';
        $I->assertEquals('this is test two', $testClass->test2);
    }

    public function testCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();

        $I->assertEquals('this is test 3', $testClass->test3);

        $testClass->test3 = 'this is test three';
        $I->assertEquals('this is test three', $testClass->test3);
    }

    public function testBehaviorProperty(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals('this is test 4', $testClass->test4);

        $testClass->test4 = 'this is test four';
        $I->assertEquals('this is test four', $testClass->test4);
    }

    public function testBehaviorMethod(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals(6, $testClass->multiple(2, 3));
    }

    public function testBehaviorYii2OfficialCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals('this is test 5', $testClass->test5);

        $testClass->test5 = 'this is test five';
        $I->assertEquals('this is test five', $testClass->test5);
    }

    public function testActiveRecordProperty(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();

        $I->assertEquals('this is test 6', $testClass->test6);

        $testClass->test6 = 'this is test six';
        $I->assertEquals('this is test six', $testClass->test6);
    }

    public function testActiveRecordYii2OfficialCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();

        $I->assertEquals('this is test 7', $testClass->test7);

        $testClass->test7 = 'this is test seven';
        $I->assertEquals('this is test seven', $testClass->test7);
    }

    public function testActiveRecordCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();

        $I->assertEquals('this is test 8', $testClass->test8);

        $testClass->test8 = 'this is test eight';
        $I->assertEquals('this is test eight', $testClass->test8);
    }

    public function testActiveRecordBehaviorProperty(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals('this is test 4', $testClass->test4);

        $testClass->test4 = 'this is test four';
        $I->assertEquals('this is test four', $testClass->test4);
    }

    public function testActiveRecordBehaviorMethod(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals(6, $testClass->multiple(2, 3));
    }

    public function testActiveRecordBehaviorYii2OfficialCustomAttribute(UnitTester $I)
    {
        $testClass = new TestSetterSetterTraitActiveRecordClass();
        $testClass->attachBehavior('testBehavior', [
            'class' => TestSetterSetterTraitBehaviorClass::className()
        ]);

        $I->assertEquals('this is test 5', $testClass->test5);

        $testClass->test5 = 'this is test five';
        $I->assertEquals('this is test five', $testClass->test5);
    }
}

class TestSetterSetterTraitClass extends \yii\base\Component implements \ant\interfaces\GetterSetterTraitInterface
{
    use GetterSetterTrait;

    // attribute
    public      $test1  = 'this is test 1';
    protected   $_test2 = 'this is test 2';


    // method
    public function sum($a, $b)
    {
        return $a + $b;
    }



    // custome attribute
    protected $_customAttribute = [
        'test3' => 'this is test 3',
    ];




    // yii2 style custom attribtue
    public function getTest2() { return $this->_test2; }
    public function setTest2($value) { $this->_test2 = $value; }




    // getter setter overrider 
    public function getterOverride($name, $ex)
    {
        if ($this->hasCustomAttribute($name)) return $this->getCustomAttribute($name);

        throw $ex;
    }

    public function setterOverride($name, $value, $ex)
    {
        if ($this->hasCustomAttribute($name)) {

            $this->setCustomAttribute($name, $value);

        } else {

            throw $ex;

        }
    }


    // customer attribute handler
    public function hasCustomAttribute($name)
    {
        return isset($this->_customAttribute);
    }

    public function getCustomAttribute($name)
    {
        return $this->_customAttribute[$name];
    }

    public function setCustomAttribute($name, $value)
    {
        return $this->_customAttribute[$name] = $value;
    }
}

class TestSetterSetterTraitBehaviorClass extends \yii\base\Behavior
{
    public $test4 = 'this is test 4';

    protected $_test5 = 'this is test 5'; 


    public function multiple($a, $b)
    {
        return $a * $b;
    }

    public function getTest5()
    {
        return $this->_test5;
    }

    public function setTest5($test5)
    {
        $this->_test5 = $test5;
    }
}

class TestSetterSetterTraitActiveRecordClass extends \yii\base\Component implements \ant\interfaces\GetterSetterTraitInterface
{
    use GetterSetterTrait;

    public $test6 = 'this is test 6';

    protected $_test7 = 'this is test 7';

    protected $_customAttribute = [
        'test8' => 'this is test 8',
    ];

    public function getterOverride($name, $ex)
    {
        if ($this->hasCustomAttribute($name)) return $this->getCustomAttribute($name);

        throw $ex;
    }

    public function setterOverride($name, $value, $ex)
    {
        if ($this->hasCustomAttribute($name)) {

            $this->setCustomAttribute($name, $value);

        } else {

            throw $ex;

        }
    }

    public function getTest7()
    {
        return $this->_test7;
    }

    public function setTest7($test7)
    {
        $this->_test7 = $test7;
    }

    public function hasCustomAttribute($name)
    {
        return isset($this->_customAttribute[$name]);
    }

    public function getCustomAttribute($name)
    {
        return $this->_customAttribute[$name];
    }

    public function setCustomAttribute($name, $value)
    {
        return $this->_customAttribute[$name] = $value;
    }
}