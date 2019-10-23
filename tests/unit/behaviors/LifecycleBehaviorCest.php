<?php namespace behaviors;

use UnitTester;
use ant\lifecycle\Status;

class LifecycleBehaviorCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testSimplest(UnitTester $I)
    {
        $model = new TestLifecycleSimplestModel;

        $I->assertNotEquals(Status::CLOSED, $model->status);

        $model->setStatus(Status::CLOSED);

        $I->assertEquals(Status::CLOSED, $model->status);
        $I->assertEquals(Status::CLOSED, $model->getStatus()->getValue());

        $model->setStatus(Status::ACTIVE);

        $I->assertEquals(Status::ACTIVE, $model->status);
        $I->assertEquals(Status::ACTIVE, $model->getStatus()->getValue());
    }

    // tests
    public function test(UnitTester $I)
    {
        $model = new TestLifecycleModel;

        $I->assertNotEquals(Status::CLOSED, $model->status);

        $model->setStatus(Status::CLOSED);

        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        $model = TestLifecycleModel::findOne($model->id);

        $I->assertEquals(Status::CLOSED, $model->status);
        $I->assertEquals(Status::CLOSED, $model->getStatus()->getValue());

        $I->assertTrue($model->validate());

        $model->setStatus(Status::ACTIVE);
        
        $I->assertFalse($model->validate());
    }

    // tests
    public function testStatusOptions(UnitTester $I)
    {
        $expected = [
            Status::ACTIVE => 'Active',
            Status::CLOSED => 'Closed',
        ];
        $model = new TestLifecycleModel;
        $I->assertEquals($expected, $model->getStatusOptions());
    }
}

class TestLifecycleModel extends \yii\db\ActiveRecord {
    public static function tableName() {
        return '{{%test}}';
    }

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\LifecycleBehavior',
                'validStatus' => [
                    Status::ACTIVE, Status::CLOSED
                ],
                'validStatusChanges' => [
                    Status::CLOSED => [],
                ],
                'statusModelConfig' => [
                    'statusText' => [
                        Status::ACTIVE => 'Active',
                        Status::CLOSED => 'Closed',
                    ],
                ],
            ]
        ];
    }
}


class TestLifecycleSimplestModel extends \yii\db\ActiveRecord {
    public $status;

    public static function tableName() {
        return '{{%test}}';
    }

    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\LifecycleBehavior',
            ]
        ];
    }
}
