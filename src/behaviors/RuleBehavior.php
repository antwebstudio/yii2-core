<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;

class RuleBehavior extends Behavior
{
    public $uniqueId;

    public function hasDbRuleFields() {
        return count($this->owner->ticketTypeRule) > 0;
    }

    public function getDbRuleFields($model) {
        $fields = [];
        foreach ($this->owner->ticketTypeRule as $dbRule) {
            $rule = $dbRule->getInstance();
            $rule->context = $this->owner;
            $fields = ArrayHelper::merge($fields, $rule->getVisibleFields($model));
        }
        return $fields;
    }

    public function getDbRules($model) {
        $rules = [];
        foreach ($this->owner->ticketTypeRule as $dbRule) {
            $rule = $dbRule->getInstance();
            $rule->context = $this->owner;
            $rules = ArrayHelper::merge($rules, $rule->getCustomRules($model));
        }
        return $rules;
    }
}