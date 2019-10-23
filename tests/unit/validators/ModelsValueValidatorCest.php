<?php
use ant\validators\ModelsValueValidator;

class ModelsValueValidatorCest
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
        $model = new ModelsValueValidatorCestModel;
        $model->profile->data = [
            'firstname' => null,
        ];

        $validator = new ModelsValueValidator;
        $validator->modelTobeCheck = 'profile';
        $validator->attributeNames = ['data' => 'data'];
        $validator->rules = [
            'data' => [
                [['firstname'], 'required'],
            ],
        ];
        $validator->validateAttribute($model, 'profile');

        $I->assertTrue($model->hasErrors());
    }
}

class ModelsValueValidatorCestModel extends \yii\base\Model {
    protected $_profile;

    public function getProfile() {
        if (!isset($this->_profile)) {
            $this->_profile = new ModelsValueValidatorCestProfile;
        }
        return $this->_profile;
    }
}

class ModelsValueValidatorCestProfile extends \yii\base\Model {
    public $data;
}