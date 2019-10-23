<?php
namespace behaviors;
use \UnitTester;

class DuplicatableBehaviorCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function test(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$newModel = $model->duplicate();
		
		$I->assertTrue($newModel->id > 0);
		$I->assertNotEquals($model->id, $newModel->id);
		$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertTrue($newModel->id > 0);
		$I->assertNotEquals($model->id, $newModel->id);
		$I->assertEquals($expected, $newModel->name);
    }
	
    public function testDuplicateRelations(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$child = new DuplicatableBehaviorCestTestChildModel;
		$child->test_id = $model->id;
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$newModel = $model->duplicate();
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertTrue($newModel->child[0]->id > 0);
		$I->assertNotEquals($model->child[0]->id, $newModel->child[0]->id);
		//$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertTrue($newModel->child[0]->id > 0);
		$I->assertNotEquals($model->child[0]->id, $newModel->child[0]->id);
		//$I->assertEquals($expected, $newModel->name);
    }
	
	public function testDuplicateNestedRelations(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$child = new DuplicatableBehaviorCestTestChildModel;
		$child->test_id = $model->id;
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$childChild = new DuplicatableBehaviorCestTestChildChildModel;
		$childChild->test_child_id = $child->id;
		if (!$childChild->save()) throw new \Exception(print_r($childChild->errors, 1));
		
		$newModel = $model->duplicate();
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		$I->assertTrue($newModel->child[0]->child[0]->id > 0);
		$I->assertNotEquals($model->child[0]->child[0]->id, $newModel->child[0]->child[0]->id);
		//$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		$I->assertTrue($newModel->child[0]->child[0]->id > 0);
		$I->assertNotEquals($model->child[0]->child[0]->id, $newModel->child[0]->child[0]->id);
		//$I->assertEquals($expected, $newModel->name);
    }
	
	public function testDuplicateSpecifiedRelationsWithFirstLevelOnly(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$child = new DuplicatableBehaviorCestTestChildModel;
		$child->test_id = $model->id;
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$childChild = new DuplicatableBehaviorCestTestChildChildModel;
		$childChild->test_child_id = $child->id;
		if (!$childChild->save()) throw new \Exception(print_r($childChild->errors, 1));
		
		$model->getBehavior('duplicable')->relations = ['child' => []];
		
		$newModel = $model->duplicate();
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(0, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(0, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
    }
	
	public function testDuplicateSpecifiedNestedRelationsWillAllLevel(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$child = new DuplicatableBehaviorCestTestChildModel;
		$child->test_id = $model->id;
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$childChild = new DuplicatableBehaviorCestTestChildChildModel;
		$childChild->test_child_id = $child->id;
		if (!$childChild->save()) throw new \Exception(print_r($childChild->errors, 1));
		
		$model->getBehavior('duplicable')->relations = ['child'];
		
		$newModel = $model->duplicate();
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
    }
	
	public function testDuplicateSpecifiedNestedRelationsWillAllLevel2(UnitTester $I)
    {
		$expected = 'abc';
		
		$model = new DuplicatableBehaviorCestTestModel;
		$model->name = $expected;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$child = new DuplicatableBehaviorCestTestChildModel;
		$child->test_id = $model->id;
		if (!$child->save()) throw new \Exception(print_r($child->errors, 1));
		
		$childChild = new DuplicatableBehaviorCestTestChildChildModel;
		$childChild->test_child_id = $child->id;
		if (!$childChild->save()) throw new \Exception(print_r($childChild->errors, 1));
		
		$model->getBehavior('duplicable')->relations = ['child' => ['child']];
		
		$newModel = $model->duplicate();
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
		
		$newModel = DuplicatableBehaviorCestTestModel::findOne($newModel->id);
		
		$I->assertEquals(1, count($newModel->child));
		$I->assertEquals(1, count($newModel->child[0]->child));
		//$I->assertEquals($expected, $newModel->name);
    }
}

class DuplicatableBehaviorCestTestModel extends \yii\db\ActiveRecord {
	public function behaviors() {
		return [
			'duplicable' => [
				'class' => 'ant\behaviors\DuplicatableBehavior',
				//'relations' => ['child'],
			],
		];
	}
	
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function getChild() {
		return $this->hasMany(DuplicatableBehaviorCestTestChildModel::class, ['test_id' => 'id']);
	}
}

class DuplicatableBehaviorCestTestChildModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test_child}}';
	}
	
	public function getChild() {
		return $this->hasMany(DuplicatableBehaviorCestTestChildChildModel::class, ['test_child_id' => 'id']);
	}
	
}

class DuplicatableBehaviorCestTestChildChildModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test_child_child}}';
	}
}