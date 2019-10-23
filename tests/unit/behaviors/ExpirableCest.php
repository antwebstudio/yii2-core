<?php 
namespace behaviors;

use UnitTester;
use ant\helpers\DateTime;

class ExpirableCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testExpireAfterDays(UnitTester $I)
    {
        $days = 5;
        $expectedExpireAt = (new DateTime)->addDays($days)->setTimeAsEndOfDay();

        $model = new ExpirableCestTestModel;
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        $model->expireAfterDays($days, true)->save();

        $I->assertEquals($expectedExpireAt->format(DateTime::FORMAT_MYSQL), $model->expireAt);
    }

    public function testSetExpireAt(UnitTester $I) {
        $date = new DateTime();
        $model = new ExpirableCestTestModel;
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        $model->setExpireAt($date->format(DateTime::FORMAT_MYSQL))->save();

        $model = ExpirableCestTestModel::findOne($model->id);

        $I->assertEquals($date->format(DateTime::FORMAT_MYSQL), $model->expireAt);
    }
	
	public function testSetExpireAtBeforeSave(UnitTester $I) {
        $date = new DateTime();
        $model = new ExpirableCestTestModel;
		
        $model->setExpireAt($date->format(DateTime::FORMAT_MYSQL))->save();

        $model = ExpirableCestTestModel::findOne($model->id);

        $I->assertEquals($date->format(DateTime::FORMAT_MYSQL), $model->expireAt);
	}

    public function testExtendExpiryDate(UnitTester $I) {
        $days = 5;
        $expectedExpireAt = (new DateTime)->addDays($days * 2)->setTimeAsEndOfDay();

        $model = new ExpirableCestTestModel;
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        $I->assertEquals(0, $model->renewCount);

        $model->expireAfterDays($days, true);
        $model->extendExpiryDate($days)->save();

        $I->assertEquals($expectedExpireAt->format(DateTime::FORMAT_MYSQL), $model->expireAt);
        //$I->assertEquals(1, $model->renewCount);

        $model = ExpirableCestTestModel::findOne($model->id);

        $I->assertEquals($expectedExpireAt->format(DateTime::FORMAT_MYSQL), $model->expireAt);
        $I->assertEquals(1, $model->renewCount);
    }
}

class ExpirableCestTestModel extends \yii\db\ActiveRecord {
    public function behaviors() {
        return [
            [
                'class' => 'ant\behaviors\Expirable',
            ],
        ];
    }

    public static function tableName() {
        return '{{%test}}';
    }
}
