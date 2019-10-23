<?php
namespace ant\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Menu extends \yii\widgets\Menu
{
    public $linkTemplate = '<a href="{url}"{attr}>{label}</a>';

    protected function renderItem($item)
    {
        $template = parent::renderItem($item);

        if (isset($item['url'])) {
            $template = strtr($template, [
                '{attr}' => Html::renderTagAttributes(ArrayHelper::getValue($item, 'linkOptions', [])),
            ]);
        }

        return $template;
    }
}