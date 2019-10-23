<?php
namespace console\components;

use console\helpers\Console;

class Controller extends \yii\console\Controller
{
    public function silentPrompt($text, $options)
    {
        return Console::silentPrompt($text, $options);
    }
}
?>
