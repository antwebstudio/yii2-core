<?php
use ant\base\FormModel;

class FormModelCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
	public function testGetterForModel(UnitTester $I) {
        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);
		
		$I->assertEquals(null, $formModel->testModel);
	}
	
	public function testGetModel(UnitTester $I) {
        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);
		
		$I->assertTrue($formModel->getModel('testModel') instanceof TestFormModelCestModel);
	}

    // Value is single model
    // Use __set and __get
    public function testSetterAndGetterForModel(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $model = new TestFormModelCestModel;
        $model->attributeValue = $expectedValue;

        $formModel->testModel = $model;
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
    }
    
    // Value is single array
    // Use __set and __get
    public function testSetterAndGetterUsingArray(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $formModel->testModel = [
            'attributeValue' => $expectedValue,
        ];
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
    }
    
    // Value is single model
    // Use setTestModel and getTestModel
    public function testSetterAndGetterFuncForModel(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $model = new TestFormModelCestModel;
        $model->attributeValue = $expectedValue;

        $formModel->setTestModel($model);
        $I->assertEquals($expectedValue, $formModel->getTestModel()->attributeValue);
    }
    
    // Value is single array
    // Use setTestModel and getTestModel
    public function testSetterAndGetterFuncUsingArray(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $formModel->setTestModel([
            'attributeValue' => $expectedValue,
        ]);
        $I->assertEquals($expectedValue, $formModel->getTestModel()->attributeValue);
    }

    // Value is single model
    // Use getTestModel
    public function testGetterFuncForModelWithConfig(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $model = $formModel->getTestModel([
            'attributeValue' => $expectedValue,
        ]);
        $I->assertEquals($expectedValue, $model->attributeValue);
    }

    // Value is single model
    // Use getTestModel
    public function testGetterFuncForInitializedModelWithConfig(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);
        $model = $formModel->getTestModel(); // testModel is already initialized
        $model = $formModel->getTestModel([
            'attributeValue' => $expectedValue,
        ]);
        $I->assertEquals($expectedValue, $model->attributeValue);
    }

    // Value is multi dimension array
    // Use __set and __get
    public function testSetterAndGetterFuncForArray(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $data = [
            [
                'attributeValue' => $expectedValue,
            ],
        ];

        $formModel->testModel = $data;

        $I->assertEquals(count($data), count($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel[0]->attributeValue);
    }

    // Don't set before get
    // Use __set and __get
    public function testSetterAndGetterDontSet(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $formModel->getTestModel()->attributes = [
            'attributeValue' => $expectedValue,
        ];

        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
    }

    public function testIsset(UnitTester $I) {
        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $I->assertFalse(isset($formModel->testModel));
        $I->assertTrue($formModel->getTestModel() != null);
        $I->assertTrue($formModel->getTestModel() instanceof TestFormModelCestModel);
        $I->assertTrue(isset($formModel->testModel));

        $I->assertFalse(isset($formModel->xxxx));
        $exceptionThrown = false;
        try { $formModel->getXxxx(); } catch (\Exception $ex) {
            $exceptionThrown = true;
        }
        $I->assertTrue($exceptionThrown);
        $I->assertFalse(isset($formModel->xxxx));
    }

    public function testLoadWhenEmpty(UnitTester $I) {
        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:optional' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $I->assertFalse($formModel->load([]));
        $I->assertFalse($formModel->load(null));
    }

    public function testLoad(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
                'optionalTestModel:optional' => [
                    'class' => TestFormModelCestOptionalModel::className(),
                ],
            ],
        ]);

        $data = [
            'attributeValue' => $expectedValue,
        ];

        $formData = [
            $formModel->formName() => [
                'testModel' => [$data],
                'optionalTestModel' => $data,
            ],
        ];

        $formModel->load($formData);

        $I->assertEquals(count($data), count($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel[0]->attributeValue);

        $I->assertTrue(isset($formModel->optionalTestModel));
        $I->assertEquals($expectedValue, $formModel->optionalTestModel->attributeValue);
    }
	
	public function testLoad2(UnitTester $I) {
		$model = new TestFormModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestFormModelCestModel::className(),
					'name' => 'billTo',
                ],
            ],
        ]);

        $addressesData = [
            'billTo' => [
                'attributeValue' => 'bill firstname',
                'attributeValue2' => 'bill lastname',
            ],
        ];
        $data = [
            (new TestFormModelCestModel)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
		$I->assertTrue($model->validate());
		
		$I->assertEquals($addressesData['billTo']['attributeValue'], $model->billTo->attributeValue);
		$I->assertEquals($addressesData['billTo']['attributeValue2'], $model->billTo->attributeValue2);
	}
	
	public function testSetAttributesAfterValidate(UnitTester $I) {
		$model = new TestFormModelCestFormModel3;

        $addressesData = [
            'billTo' => [
                'name' => 'bill firstname',
            ],
        ];
		
		$model->validate(); // Validate before set attributes
		
		$model->attributes = $addressesData;
		
		$I->assertEquals($addressesData['billTo']['name'], $model->billTo->name);
	}
	
	public function testLoadArray(UnitTester $I) {
		$form = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestRequiredFieldModel::className(),
                ],
            ],
		]);

		$data = [
			(new TestFormModelCestRequiredFieldModel)->formName() => [
				[
					'attributeValue' => 'abc',
				],
			],
		];
		
		$I->assertTrue($form->load($data));
		
		$I->assertTrue(is_array($form->testModel));
		$I->assertEquals(1, count($form->testModel));
	}
	
	public function testLoadArrayRepeatedly(UnitTester $I) {
		$model = new FormModelCestFormModelWithArray([
			'arrayModels' => [],
		]);
		
		$model->load([
			(new TestFormModelCestModel)->formName() => [],
		]);

		$model->load([
			(new TestFormModelCestModel)->formName() => [
				[
					'attributeValue' => 'value1',
				],
			],
		]);
		//$models = $I->invokeMethod($model, '_getModel', ['arrayModels']);
		//$key = 0;
		//throw new \Exception(isset($models[$key]) ? 'y':'n');

		$model->load([
			(new TestFormModelCestModel)->formName() => [
				[
					'attributeValue2' => 'value2',
				],
			],
		]);
		
		$expected = [
			'attributeValue' => 'value1',
			'attributeValue2' => 'value2',
		];
		
		//throw new \Exception(print_r($model->arrayModels[0]->attributes,1));
		$I->assertEquals($expected, $model->arrayModels[0]->attributes);
	}
	
	
	public function testLoadAfterGetModel(UnitTester $I, $scenario) {
		//$scenario->skip();
		
		$expectedValue = 'test expectedValue';
		
		$data = [
			'attributeValue' => $expectedValue,
		];
		
        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel1' => [
                    'class' => TestFormModelCestModel::className(),
                ],
                'testModel2' => [
                    'class' => TestFormModelCestModel2::className(),
                ],
            ],
        ]);
		
		$formModel->getModel('testModel2'); // Bug: This cause the load() method after that failed.
		
		$formModel->load([
            (new TestFormModelCestModel)->formName() => $data,
		]);
		
		$I->assertEquals($expectedValue, $formModel->testModel1->attributeValue);
	}

    public function testLoadArrayWithFlatFormName(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $data = [
            'attributeValue' => $expectedValue,
        ];

        $formData = [
            $formModel->formName() => [
            ],
            (new TestFormModelCestModel)->formName() => [$data], // Use class name as form name
        ];

        $formModel->load($formData);

        $I->assertEquals(count($data), count($formModel->testModel));
        $I->assertTrue(is_array($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel[0]->attributeValue);
    }
	
	public function testSetArrayWithModels(UnitTester $I)
    {
        $expectedValue1 = 'test attribute value 1';
        $expectedValue2 = 'test attribute value 2';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $data1 = new TestFormModelCestModel([
            'attributeValue' => $expectedValue1,
        ]);
        $data2 = new TestFormModelCestModel([
            'attributeValue' => $expectedValue2,
        ]);
		$data = ['a' => $data1, 'b' => $data2];

        $formModel->testModel = $data;
		
		/*$tet = [];
		foreach ($formModel->testModel as $name => $model) {
			$tet[$name] = $model->attributeValue;
		}
		throw new \Exception(print_r($tet,1));*/

        $I->assertTrue(is_array($formModel->testModel));
        $I->assertEquals(count($data), count($formModel->testModel));
		
        $I->assertEquals($expectedValue1, $formModel->testModel['a']->attributeValue);
        $I->assertEquals($expectedValue2, $formModel->testModel['b']->attributeValue);
    }

    public function testLoadWithFlatFormName(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
                'optionalTestModel:optional' => [
                    'class' => TestFormModelCestOptionalModel::className(),
                ],
            ],
        ]);

        $data = [
            'attributeValue' => $expectedValue,
        ];

        $formData = [
            $formModel->formName() => [
            ],
            (new TestFormModelCestModel)->formName() => $data, // Use class name as form name
            (new TestFormModelCestOptionalModel)->formName() => $data,
        ];

        $formModel->load($formData);

        $I->assertFalse(is_array($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);

        $I->assertTrue(isset($formModel->optionalTestModel));
        $I->assertEquals($expectedValue, $formModel->optionalTestModel->attributeValue);
    }

    public function testSetByConstructor(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $data = [
            'attributeValue' => $expectedValue,
        ];

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
            'testModel' => new TestFormModelCestModel($data),
        ]);

        $I->assertTrue(isset($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
    }
	
	// If the model have behavior attached in form model
	// The new value of the "attributeValue" property of TestFormModelCestModel will be replaced by the original value
	// That mean, the "Original value" is NOT a "default value"
	// Default value, mean, the value will be replaced by the new value.
	// Original value, mean, the value will NOT be replaced by the new value.
	// What is set in "models" is Original value, but not default value.
	// If you want to set default value, please use ant\behavior\ConfigurableModelBehavior to control the value, eg. add a "defaultValues" property to ConfigurableModelBehavior
	public function testSetByConstructorModelWithBehaviors(UnitTester $I) {
		$expectedValue = 'test attribute value';

        $data = [
            'attributeValue' => $expectedValue.'2', // New value
        ];

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel' => [
                    'class' => TestFormModelCestModel::className(),
					'attributeValue' => $expectedValue, // Original value, not default value
                ],
            ],
            'testModel' => new TestFormModelCestModel($data),
        ]);

        $I->assertTrue(isset($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
	}

    public function testSetConfiguredModelByConstructor(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $data = [
            'attributeValue' => $expectedValue,
        ];

        $formModel = new TestFormModelCestConfiguredFormModel([
            'testModel' => new TestFormModelCestModel($data),
        ]);

        $I->assertTrue(isset($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel->attributeValue);
    }

    // Value is multi dimension array
    // Use __set and __get
    // Set to different value after the first set
    public function testSetterAndGetterFuncForArrayOverwriteValue(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $data = [
            [
                'attributeValue' => $expectedValue,
            ],
        ];

        $formModel->testModel = []; // First set
        $formModel->testModel = $data; // Set to different value

        $I->assertEquals(count($data), count($formModel->testModel));
        $I->assertEquals($expectedValue, $formModel->testModel[0]->attributeValue);
    }

    // Value is multi dimension array
    // Use __set and __get
    // Set to different value after the first set
    public function testSetterAndGetterFuncForArrayMergeValue(UnitTester $I)
    {

        $formModel = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $data1 = [
            [
                'attributeValue2' => 'attribute2 value 1',
            ],
        ];

        $data2 = [
            [
                'attributeValue' => 'value 1',
            ],
            [
                'attributeValue' => 'value 2',
            ],
        ];

        $formModel->testModel = $data1; // First set
        $formModel->testModel = $data2; // Set to different value

        $I->assertEquals(2, count($formModel->testModel));
        $I->assertEquals('value 1', $formModel->testModel[0]->attributeValue);
        //$I->assertEquals('attribute2 value 1', $formModel->testModel[0]->attributeValue2);
        $I->assertEquals('value 2', $formModel->testModel[1]->attributeValue);
    }
	
	public function testSave(UnitTester $I) {
		$model = new TestFormModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $addressesData = [
			'attributeValue' => 'bill firstname',
			'attributeValue2' => 'bill lastname',
        ];
        $data = [
            (new TestFormModelCestModel)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
		$I->assertFalse($model->billTo->isValidated);
		$I->assertFalse($model->billTo->isSaved);
		
		$model->save(); // True or false is not matter here.
		
		$I->assertTrue($model->billTo->isValidated);
		$I->assertTrue($model->billTo->isSaved);
	}
	
	public function testSaveUpdate(UnitTester $I) {
		$model = new TestFormModelCestFormModel([
            'models' => [
                'billTo' => [
                    'class' => TestFormModelCestActiveRecord::className(),
                ],
            ],
        ]);

        $addressesData = [
			'name' => 'bill firstname',
        ];
        $data = [
            (new TestFormModelCestActiveRecord)->formName() => $addressesData,
        ];

        $I->assertTrue($model->load($data));
		$I->assertTrue($model->save());
		
		$addressesData['name'] .= '2';
		
        $data = [
            (new TestFormModelCestActiveRecord)->formName() => $addressesData,
        ];
        $I->assertTrue($model->load($data));
		$I->assertTrue($model->save());
		
		$billTo = TestFormModelCestActiveRecord::findOne($model->billTo->id);
		
		$I->assertEquals($addressesData['name'], $billTo->name);
	}
	
	public function testSaveUpdateMultipleModelWithSameClass(UnitTester $I) {
        $addressesData = [
			'name' => 'bill firstname',
        ];
		
		$model = new TestFormModelCestActiveRecord;
		$model->attributes = $addressesData;
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$form = new TestFormModelCestFormModel3([
			'billTo' => $model,
        ]);
		
		$addressesData['name'] .= '2';
		
        $data = [
            (new TestFormModelCestActiveRecord)->formName() => ['billTo' => $addressesData],
        ];
        $I->assertTrue($form->load($data));
		$I->assertTrue($form->save());
		
		$billTo = TestFormModelCestActiveRecord::findOne($form->billTo->id);
		
		$I->assertEquals($addressesData['name'], $billTo->name);
	}
	
	// Test to confirm FormModel will do validate() when save() is called
	public function testSaveWithFormModelRules(UnitTester $I) {
		$model = new TestFormModelCestHaveRequiredFieldFormModel;
		
		// Don't call validate() before save(), to make sure save will call validate it self
		$I->assertFalse($model->save()); 
		$I->assertEquals(['requiredField' => ['Required Field cannot be blank.']], $model->errors);
	}
	
	public function testSaveWithReadonlyModel(UnitTester $I) {
		$model = new TestFormModelCestFormModel([
            'models' => [
                'billTo:readonly' => [
                    'class' => TestFormModelCestModel::className(),
                ],
            ],
        ]);

        $addressesData = [
			'attributeValue' => 'bill firstname',
			'attributeValue2' => 'bill lastname',
        ];
        $data = [
            (new TestFormModelCestModel)->formName() => $addressesData,
        ];

        $model->load($data); // Not sure should return true or false
		$I->assertFalse($model->billTo->isValidated);
		
		$model->save(); // True or false is not matter here.
		
		$I->assertFalse($model->billTo->isValidated);
		$I->assertFalse($model->billTo->isSaved);
	}
    
    public function testOverrideMethodForModelUsingSetterAndGetter(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestConfiguredAndMethodOverwrittenFormModel;
        $formModel->getTestModel();
        $formModel->setTestModel(null);

        $I->assertTrue($I->getProperty($formModel, 'getTestModelIsCalled'));
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }

    public function testOverrideMethodForModelUsingConstructor(UnitTester $I)
    {
        $expectedValue = 'test attribute value';
        
        $formModel = new TestFormModelCestContructorConfiguredFormModel([
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));

        $formModel = new TestFormModelCestConfiguredAndMethodOverwrittenFormModel([
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }

    public function testOverrideMethodForModelUsingYiiCreateObject(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = \Yii::createObject([
            'class' => TestFormModelCestContructorConfiguredFormModel::className(),
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
        
        $formModel = \Yii::createObject([
            'class' => TestFormModelCestConfiguredAndMethodOverwrittenFormModel::className(),
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }

    // Model have call parent::setTestModel
    public function testOverrideMethodWhichCallParentForModelUsingSetterAndGetter(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestConfiguredAndMethodOverwrittenWhichCallParentFormModel;
        $formModel->getTestModel();
        $formModel->setTestModel(null);

        $I->assertTrue($I->getProperty($formModel, 'getTestModelIsCalled'));
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }

    // Model have call parent::setTestModel
    public function testOverrideMethodWhichCallParentForModelUsingConstructor(UnitTester $I)
    {
        $expectedValue = 'test attribute value';

        $formModel = new TestFormModelCestConfiguredAndMethodOverwrittenWhichCallParentFormModel([
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }

    // Model have call parent::setTestModel
    public function testOverrideMethodWhichCallParentForModelUsingYiiCreateObject(UnitTester $I)
    {
        $expectedValue = 'test attribute value';
        
        $formModel = \Yii::createObject([
            'class' => TestFormModelCestConfiguredAndMethodOverwrittenWhichCallParentFormModel::className(),
            'testModel' => null,
        ]);
        $I->assertTrue($I->getProperty($formModel, 'setTestModelIsCalled'));
    }
	
	public function testValidateFail(UnitTester $I) {
		$form = new TestFormModelCestFormModel([
            'models' => [
                'testModel:array' => [
                    'class' => TestFormModelCestRequiredFieldModel::className(),
                ],
            ],
		]);

		$data = [
			(new TestFormModelCestRequiredFieldModel)->formName() => [
				[
					'attributeValue' => '',
				],
			],
		];
		
		$I->assertFalse($form->validate()); // Should be false as TestFormModelCestRequiredFieldModel required field is not filled.
	}
	
	public function testGetModelsSequence(UnitTester $I) {
		$form = new TestFormModelCestFormModel2;
		
		$I->assertEquals(['model1', 'model2'], array_keys($form->getModels()));
	}
	
	public function testGetModelsSequenceAfterGetModel(UnitTester $I, $scenario) {
		$scenario->skip('To be discussed. ');
		
		$form = new TestFormModelCestFormModel2;
		
		$form->getModel('model2'); // Sequence reverse after getModel
		
		$I->assertEquals(['model1', 'model2'], array_keys($form->getModels()));
	}
	
	public function testSaveModelsSequenceAfterGetModel(UnitTester $I, $scenario) {
		$form = new TestFormModelCestFormModel2;
		
		$form->getModel('model2'); // Sequence reverse after getModel
		
		$form->save();
		
		$I->assertEquals('model2', $form->lastSavedModel);
	}
}

class TestFormModelCestFormModel extends FormModel {
	public $lastSavedModel;
	
	public function models() {
		return \yii\helpers\ArrayHelper::merge(parent::models(), [
			'model1' => [
				'class' => TestFormModelCestModel::class,
				'on '.TestFormModelCestModel::EVENT_SAVE => function($event) {
					$this->lastSavedModel = 'model1';
				}
			]
		]);
	}
}

class TestFormModelCestFormModel2 extends TestFormModelCestFormModel {
	public function models() {
		return \yii\helpers\ArrayHelper::merge(parent::models(), [
			'model2' => [
				'class' => TestFormModelCestModel::class,
				'on '.TestFormModelCestModel::EVENT_SAVE => function($event) {
					$this->lastSavedModel = 'model2';
				}
			]
		]);
	}
}

class TestFormModelCestFormModel3 extends FormModel {
	public function models() {
		return \yii\helpers\ArrayHelper::merge(parent::models(), [
			'billTo' => [
				'class' => TestFormModelCestActiveRecord::className(),
				'name' => 'billTo',
			],
		]);
	}
}

class TestFormModelCestConfiguredFormModel extends FormModel {
    public function models() {
        return [
            'testModel' => [
                'class' => TestFormModelCestModel::className(),
            ],
        ];
    }
}

class TestFormModelCestContructorConfiguredFormModel extends FormModel  {
    protected $getTestModelIsCalled = false;
    protected $setTestModelIsCalled = false;

    public function getTestModel() {
        $this->getTestModelIsCalled = true;
    }

    public function setTestModel($value) {
        $this->setTestModelIsCalled = true;
    }
}

class TestFormModelCestConfiguredAndMethodOverwrittenFormModel extends FormModel {
    protected $getTestModelIsCalled = false;
    protected $setTestModelIsCalled = false;

    public function configs() {
        return [
            'testModel' => [
                'class' => TestFormModelCestModel::className(),
            ],
        ];
    }

    public function getTestModel() {
        $this->getTestModelIsCalled = true;
    }

    public function setTestModel($value) {
        $this->setTestModelIsCalled = true;
    }
}

class FormModelCestFormModelWithArray extends FormModel {
	public function configs() {
		return [
			'arrayModels:array' => [
				'class' => TestFormModelCestModel::className(),
			],
		];
	}
}

class TestFormModelCestHaveRequiredFieldFormModel extends FormModel {
	public $requiredField;
	
	public function rules() {
		return [
			[['requiredField'], 'required'],
		];
	}
}

class TestFormModelCestConfiguredAndMethodOverwrittenWhichCallParentFormModel extends FormModel {
    protected $getTestModelIsCalled = false;
    protected $setTestModelIsCalled = false;

    public function configs() {
        return [
            'testModel' => [
                'class' => TestFormModelCestModel::className(),
            ],
        ];
    }

    public function getTestModel() {
        parent::getTestModel(); // Call parent
        $this->getTestModelIsCalled = true;
    }

    public function setTestModel($value) {
        parent::setTestModel($value); // Call parent
        $this->setTestModelIsCalled = true;
    }
}

class TestFormModelCestOptionalModel extends \yii\base\Model {
    public $attributeValue;
    public $attributeValue2;

    public function rules() {
        return [
            [['attributeValue', 'attributeValue2'], 'safe'],
        ];
    }
}

class TestFormModelCestActiveRecord extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function rules() {
		return [
			[['name'], 'safe']
		];
	}
}

class TestFormModelCestModel extends \yii\base\Model {
	const EVENT_SAVE = 'save';
	
    public $attributeValue;
    public $attributeValue2;
	
	protected $_isValidated = false;
	protected $_isSaved = false;

    public function rules() {
        return [
            [['attributeValue', 'attributeValue2'], 'safe'],
        ];
    }
	
	public function getIsValidated() {
		return $this->_isValidated;
	}
	
	public function getIsSaved() {
		return $this->_isSaved;
	}
	
	public function validate($attributeNames = null, $clearErrors = true) {
		$this->_isValidated = true;
		return parent::validate($attributeNames, $clearErrors);
	}
	
	public function save() {
		$this->trigger(self::EVENT_SAVE);
		$this->_isSaved = true;
		return true;
	}
}

class TestFormModelCestModel2 extends TestFormModelCestModel {
	
}

class TestFormModelCestRequiredFieldModel extends TestFormModelCestModel {
	public function rules() {
        return [
            [['attributeValue'], 'required'],
        ];
    }
}
