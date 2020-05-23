<?php 

use ant\models\ModelClass;

class ModelClassCest
{
    public function _before(UnitTester $I)
    {
		\Yii::configure(\Yii::$app, [
            'components' => [
				'cache' => [
					'class' => 'yii\caching\FileCache',
				],
            ],
        ]);
    }

    // tests
    public function testGetClassId(UnitTester $I)
    {
		$className = 'TestModelClass';
		
		$class = ModelClass::findOne(['class_name' => $className]);
		$I->assertFalse(isset($class));
		
		$classId = ModelClass::getClassId($className);
		$classId2 = ModelClass::getClassId($className);
		
		$I->assertEquals($classId, $classId2);
    }
	
	public function testGetClassIdWithObject(UnitTester $I) {
		$object = new TestModelClass;
		$classId = ModelClass::getClassId($object);
	}
}

class TestModelClass {
	
}