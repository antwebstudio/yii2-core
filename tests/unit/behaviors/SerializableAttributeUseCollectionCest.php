<?php 

class SerializableAttributeUseCollectionCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function test(UnitTester $I)
    {
		$data = [
			'test' => 'test value',
		];
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$model->refresh();
		
		$I->assertEquals($data, $model->options->toArray());
    }
	
    // tests
    public function testUpdate(UnitTester $I)
    {
		$data = [
			'test' => 'test value',
		];
		
		$newData = [
			'newTest' => 'another test value',
		];
		
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$model->options = $newData;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($newData, $model->options->toArray());
    }
	
	public function testFill(UnitTester $I) {
		$data = [
			'test' => 'test value',
		];
		
		$add = [
			'newTest' => 'another test value',
		];
		
		$expected = $data;
		foreach ($add as $key => $value) {
			$expected[$key] = $value;
		}
		
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$options = $model->options->fill('newTest', 'another test value');
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($expected, $options->toArray());
		$I->assertEquals($expected, $model->options->toArray());
	}
	
	public function testAttributeMergeAndOverride(UnitTester $I) {
		$data = [
			'old' => 'old',
			'intersect' => 'old',
			'nested' => ['old' => 'old', 'intersect' => 'old'],
		];
		
		$add = [
			'new' => 'new',
			'intersect' => 'new',
			'nested' => ['intersect' => 'new', 'new' => 'new'],
		];
		
		$expected = [
			'old' => 'old',
			'new' => 'new',
			'intersect' => 'new',
			'nested' => ['old' => 'old', 'intersect' => 'new', 'new' => 'new'],
		];
		
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$options = $model->options->mergeAndOverride($add);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($expected, $options->toArray());
		$I->assertEquals($expected, $model->options->toArray());
	}
	
	public function testAttributeFill(UnitTester $I) {
		$data = [
			'old' => 'old',
			'intersect' => 'old',
			//'nested' => ['old' => 'old', 'intersect' => 'old'],
		];
		
		$add = [
			'new' => 'new',
			'intersect' => 'new',
			//'nested' => ['intersect' => 'new', 'new' => 'new'],
		];
		
		$expected = [
			'old' => 'old',
			'new' => 'new',
			'intersect' => 'old',
			//'nested' => ['old' => 'old', 'intersect' => 'old'],
		];
		
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$options = $model->options->fill('new', 'new');
		$options = $model->options->fill('intersect', 'new');
		//$options = $model->options->fill('nested', ['intersect' => 'new', 'new' => 'new']);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($expected, $options->toArray());
		$I->assertEquals($expected, $model->options->toArray());
	}
	
	public function testLoadAndSetSerializableAttribute(UnitTester $I) {
		$data = [
			'old' => 'old',
			'intersect' => 'old',
			'nested' => ['old' => 'old', 'intersect' => 'old'],
		];
		
		$add = [
			'new' => 'new',
			'intersect' => 'new',
			'nested' => ['intersect' => 'new', 'new' => 'new'],
		];
		
		$expected = [
			'old' => 'old',
			'new' => 'new',
			'intersect' => 'new',
			'nested' => ['old' => 'old', 'intersect' => 'new', 'new' => 'new'],
		];
		
		// First fillAttributes
		$model = new SerializableAttributeUseCollectionCestTestModel;
		$model->load([$model->formName() => ['options' => $data]]);
		$model->setSerializableAttributes(['options' => $data]);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($data, $model->options->toArray());
		
		$model->refresh();
		
		// Second fillAttributes
		$model->load([$model->formName() => ['options' => $data]]);
		$model->setSerializableAttributes(['options' => $add]);
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($expected, $model->options->toArray());
	}
	
	
}

class SerializableAttributeUseCollectionCestTestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\SerializableAttribute::class,
				'attributes' => ['options'],
				'useCollection' => true,
			],
		];
	}
}