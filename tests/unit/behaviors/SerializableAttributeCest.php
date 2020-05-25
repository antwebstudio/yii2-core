<?php 

class SerializableAttributeCest
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
		$model = new SerializableAttributeCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$model->refresh();
		
		$I->assertEquals($data, $model->options);
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
		
		$model = new SerializableAttributeCestTestModel;
		$model->options = $data;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$model->options = $newData;
		
		if (!$model->save()) throw new \Exception(print_r($model->errors));
		
		$I->assertEquals($newData, $model->options);
    }
}

class SerializableAttributeCestTestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function behaviors() {
		return [
			[
				'class' => \ant\behaviors\SerializableAttribute::class,
				'attributes' => ['options'],
			],
		];
	}
}