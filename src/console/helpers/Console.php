<?php
namespace console\helpers;

use yii\helpers\ArrayHelper;

use Seld\CliPrompt\CliPrompt;

class Console extends \yii\helpers\Console
{
    public static function silentPrompt($text, $options = [])
    {
        $options = ArrayHelper::merge(
            [
                'required'  => false,
                'default'   => null,
                'pattern'   => null,
                'validator' => null,
                'error'     => 'Invalid input.',
            ],
            $options
        );
        $error   = null;

        top:
        $input = $options['default']
            ? static::silentInput("$text [" . $options['default'] . '] ')
            : static::silentInput("$text ");

        if ($input === '') {
            if (isset($options['default'])) {
                $input = $options['default'];
            } elseif ($options['required']) {
                static::output($options['error']);
                goto top;
            }
        } elseif ($options['pattern'] && !preg_match($options['pattern'], $input)) {
            static::output($options['error']);
            goto top;
        } elseif ($options['validator'] &&
            !call_user_func_array($options['validator'], [$input, &$error])
        ) {
            static::output(isset($error) ? $error : $options['error']);
            goto top;
        }

        return $input;
    }

    public static function silentInput($prompt = null)
    {
        if (isset($prompt)) {
            static::stdout($prompt);
        }

        return static::silentStdin();
    }

    public static function silentStdin()
    {
        return CliPrompt::hiddenPrompt();
    }
}
?>
