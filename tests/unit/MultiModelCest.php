<?php

use ant\base\MultiModel;

class MultiModelCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
    }
	
	public function testGetModelForOptionalModel(UnitTester $I) {
		$model = new TestMultiModelCestFormModel([
            'optionalModels' => [
                'shipTo' => [
                    'class' => TestMultiModelCestModel::className(),
					'name' => 'shipTo', // Need this when multiple model using the same class (same form name)
                ],
            ],
            'alias' => [
                'billToAndShipTo' => function($model) {
                    return [
                        'shipTo' => $model->getModel('shipTo'),
                    ];
                }
            ], 
        ]);

        $addressesData = [
            'billTo' => [
                'firstname' => 'shipping firstname',
                'lastname' => 'shipping lastname',
                'email' => 'shipping@example.com',
                'contact_number' => '01345678901',
                'addressString' => 'shipping address',
            ],
            'shipTo' => [
                'firstname' => 'shipping firstname',
                'lastname' => 'shipping lastname',
                'email' => 'shipping@example.com',
                'contact_number' => '01345678901',
                'addressString' => 'shipping address',
            ],
        ];
		//throw new \Exception((new TestMultiModelCestModel)->formName());
        $data = [
            (new TestMultiModelCestModel)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
	}
	
	public function testGetModel(UnitTester $I) {
		$model = new TestMultiModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestMultiModelCestModel::className(),
					'name' => 'billTo', // Need this when multiple model using the same class (same form name)
                ],
            ],
            'alias' => [
                'billToAndShipTo' => function($model) {
                    return [
                        'billTo' => $model->getModel('billTo'),
                    ];
                }
            ], 
        ]);

        $addressesData = [
            'billTo' => [
                'firstname' => 'shipping firstname',
                'lastname' => 'shipping lastname',
                'email' => 'shipping@example.com',
                'contact_number' => '01345678901',
                'addressString' => 'shipping address',
            ],
            'shipTo' => [
                'firstname' => 'shipping firstname',
                'lastname' => 'shipping lastname',
                'email' => 'shipping@example.com',
                'contact_number' => '01345678901',
                'addressString' => 'shipping address',
            ],
        ];
		//throw new \Exception((new TestMultiModelCestModel)->formName());
        $data = [
            (new TestMultiModelCestModel)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
	}
	
	public function testValidateSuccess(UnitTester $I) {
		$model = new TestMultiModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestMultiModelCestModel::className(),
					'name' => 'billTo', // Need this when multiple model using the same class (same form name)
                ],
            ],
        ]);

        $addressesData = [
            'billTo' => [
                'firstname' => 'shipping firstname',
                'lastname' => 'shipping lastname',
                'email' => 'shipping@example.com',
                'contact_number' => '01345678901',
                'addressString' => 'shipping address',
            ],
        ];
        $data = [
            (new TestMultiModelCestModel)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
		$I->assertTrue($model->validate());
	}
	
	public function testValidateFailed(UnitTester $I) {
		
		$model = new TestMultiModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestMultiModelCestModelWithRequiredField::className(),
                ],
            ],
        ]);

        $addressesData = [
        ];
        $data = [
            (new TestMultiModelCestModelWithRequiredField)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
		$I->assertFalse($model->validate());
		$I->assertEquals(['billTo' => [['Required Field cannot be blank.']]], $model->errors);
	}
	
	public function testLoadAfterGetModel(UnitTester $I, $scenario) {
		//$scenario->skip();
		
		$expectedValue = 'test expectedValue';
		
		$data = [
			'attributeValue' => $expectedValue,
		];
		
        $formModel = new TestMultiModelCestMultiModel([
            'models' => [
                'testModel1' => [
                    'class' => TestMultiModelCestModel::className(),
                ],
                'testModel2' => [
                    'class' => TestMultiModelCestModel2::className(),
                ],
            ],
        ]);
		
		$formModel->getModel('testModel2'); // Bug: This cause the load() method after that failed.
		
		$formModel->load([
            (new TestMultiModelCestModel)->formName() => $data,
		]);
		
		$I->assertEquals($expectedValue, $formModel->getModel('testModel1')->attributeValue);
	}
	
	public function testGetModelsSequence(UnitTester $I, $scenario) {
		$scenario->skip('To be discussed. ');
		
		$model = new MultiModel([
			'models' => [
				'model1' => ['class' => TestMultiModelCestModel::class],
				'model2' => ['class' => TestMultiModelCestModel2::class],
			],
		]);
		
		throw new \Exception(print_r(array_keys($model->getModels()),1));
	}
	
	public function testSave(UnitTester $I) {
		
		$data = [
			'MultiModelCestTestModel' => [
				'name' => 'test',
			],
			'MultiModelCestTestChildModel' => [
				'name' => 'test',
			],
		];
		$testModel1 = new MultiModelCestTestModel();
		$testModel2 = new MultiModelCestTestChildModel();

		$model = new MultiModel([
			'models' =>
			[
				'testModel1' => $testModel1,
				'testModel2' => $testModel2,
			],
		]);
		
		$I->assertTrue($model->load($data));
		$I->assertTrue($model->save());
		$I->assertTrue($model->getModel('testModel2')->id > 0);
	}
}

class MultiModelCestTestModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function getChild() {
		return $this->hasOne(MultiModelCestTestChildModel::class, ['test_id' => 'id']);
	}
}

class MultiModelCestTestChildModel extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test_child}}';
	}
}

class TestMultiModelCestFormModel extends \ant\base\MultiModel {

}

class TestMultiModelCestModel extends \yii\base\Model {
    public $attributeValue;
    public $attributeValue2;
	
	public function rules() {
		return [
			[['attributeValue', 'attributeValue2'], 'safe'],
		];
	}
}

class TestMultiModelCestModelWithRequiredField extends \yii\base\Model {
	public $requiredField;
	
	public function rules() {
		return [
			[['requiredField'], 'required'],
		];
	}
}

class TestMultiModelCestModel2 extends TestMultiModelCestModel {
	
}

class TestMultiModelCestMultiModel extends \ant\base\MultiModel {

}