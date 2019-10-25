<?php
namespace ant\console\components;

use ant\console\helpers\Console;

class Controller extends \yii\console\Controller
{
    public function silentPrompt($text, $options)
    {
        return Console::silentPrompt($text, $options);
    }
}