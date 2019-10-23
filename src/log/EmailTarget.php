<?php
namespace ant\log;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;

class EmailTarget extends \yii\log\EmailTarget {
	public function export()
    {
        // moved initialization of subject here because of the following issue
        // https://github.com/yiisoft/yii2/issues/1446
        if (empty($this->message['subject'])) {
            $this->message['subject'] = 'Application Log';
        }
        $messages = array_map([$this, 'formatMessage'], $this->messages);
		
		$body = '';
		$body .= '<table style="border: 1px #000000 solid; padding: 10px 5px;">';
		foreach ($messages as $message) {
			$body .= '<tr><td>'.$message.'</td></tr>';
		}
		$body .= '</table>';
        //$body = wordwrap(implode("\n", $messages), 70);
        $this->composeMessage($body)->send($this->mailer);
    }
	
	public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                //$text = (string) $text;
				$text = $this->renderCallStack($text);
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $prefix = $this->getMessagePrefix($message);
        return date('Y-m-d H:i:s', $timestamp) . " {$prefix}[$level][$category] $text"
            . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces)) ;
    }
	
	public function renderCallStack($exception)
    {
        $handler = new \yii\web\ErrorHandler;
		return $handler->renderCallStack($exception);
    }
	
	protected function getContextMessage()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        $result = [];
        foreach ($context as $key => $value) {
            $result[] = "\${$key} = <pre>" . VarDumper::dumpAsString($value, 10, true).'</pre>';
        }
        return implode("\n\n", $result);
    }
	
	protected function composeMessage($body)
    {
        $message = $this->mailer->compose();
        Yii::configure($message, $this->message);
        $message->setHtmlBody($body);

        return $message;
    }
}