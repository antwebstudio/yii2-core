<?php
use ant\validators\SerializableDataValidator;

class SerializableDataValidatorCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testValidateAttributeWhenHasError(UnitTester $I)
    {
        $model = new SerializableDataValidatorCestModel;
        $model->data = [
            //'firstname' => null,
        ];

        $validator = new SerializableDataValidator;
        $validator->rules = [
            [['firstname'], 'required'],
        ];
        $validator->validateAttribute($model, 'data');

        $I->assertTrue($model->hasErrors());
    }

    public function testValidateAttributeWhenNoError(UnitTester $I)
    {
        $model = new SerializableDataValidatorCestModel;
        $model->data = [
            'firstname' => 'firstname',
        ];

        $validator = new SerializableDataValidator;
        $validator->rules = [
            [['firstname'], 'required'],
        ];
        $validator->validateAttribute($model, 'data');

        $I->assertFalse($model->hasErrors());
    }
	
	public function testValidateNestedData(UnitTester $I) {
        $model = new SerializableDataValidatorCestModel;
        $model->data = [
            //'fullname' => 'fullname',
        ];

        $validator = new SerializableDataValidator;
        $validator->rules = [
			[['fullname'], 'required'],
			[['nested'], SerializableDataValidator::className(), 'rules' => [
				[['firstname', 'lastname'], 'required'],
			]],
        ];
        $validator->validateAttribute($model, 'data');
		//throw new \Exception(print_r($model->errors,1).print_r($model->errors,1));
        $I->assertTrue($model->hasErrors());
		$I->assertEquals([
			'data.fullname' => ['Fullname cannot be blank.'],
			// TODO: return error should be 'data.nested.firstname' => ['Firstname cannot be blank.'],
			'data.data.firstname' => ['Firstname cannot be blank.'],
			'data.data.lastname' => ['Lastname cannot be blank.'],
		], $model->errors);
	}
	
	public function testValidateNestedData2(UnitTester $I) {
        $model = new SerializableDataValidatorCestModelWithRules;
        $model->data = [
            //'fullname' => 'fullname',
        ];

        $I->assertFalse($model->validate());
		//throw new \Exception(print_r($model->errors,1).print_r($model->errors,1));
        $I->assertTrue($model->hasErrors());
		$I->assertEquals([
			'data.fullname' => ['Fullname cannot be blank.'],
			// TODO: return error should be 'data.nested.firstname' => ['Firstname cannot be blank.'],
			'data.data.firstname' => ['Firstname cannot be blank.'],
			'data.data.lastname' => ['Lastname cannot be blank.'],
		], $model->errors);
	}
}

class SerializableDataValidatorCestModel extends \yii\base\Model {
    public $data;
}

class SerializableDataValidatorCestModelWithRules extends  \yii\base\Model {
	public $data;
	
	public function rules() {
		return [
			[['data'], SerializableDataValidator::className(), 'rules' => [
				[['fullname'], 'required'],
				[['nested'], SerializableDataValidator::className(), 'rules' => [
					[['firstname', 'lastname'], '\ant\validators\MultipleChoiceRequiredValidator'],
				]],
			]],
		];
	}
}