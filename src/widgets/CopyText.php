<?php
namespace ant\widgets;

use yii\helpers\Html;

class CopyText extends \yii\base\Widget {
	public $options = [];
	public $copiedText = 'Copied';
	public $onCopied;
	
	public $content = '';
	
	protected $contentIsBegin = false;
	
	public function init() {
		ob_start();
	}
	
	public function beginContent() {
		$this->contentIsBegin = true;
		ob_start();
	}
	
	public function endContent() {
		$this->content = ob_get_contents();
		ob_end_clean();
		echo $this->content;
		$this->contentIsBegin = false;
	}
	
	protected function checkClosing() {
		if ($this->contentIsBegin) throw new \Exception('Expected endContent()');
	}
	
	protected function processContent($content) {
		$content = str_replace(['"', "\n", "\r", '/'], ['\"', '\n', '\r', '\/'], $content);
		return $content;
	}
	
	public function run() {
		$content = ob_get_contents();
		ob_end_clean();
		
		$this->checkClosing();
		
		$id = $this->id;
		$this->options['id'] = $id;
		
		$this->view->registerJs('
			(function() {
				var content = "'.$this->processContent($this->content).'";
				var buttons = document.querySelectorAll("#'.$id.' [data-toggle=copy]");
				for (var i in buttons) {
					buttons[i].onclick = onCopyClick;
				}
				
				var onCopied = '.$this->onCopied.';
				
				function onCopyClick() {
					var button = event.target;
					button.text = "'.$this->copiedText.'";
					copyToClipboard(content);
					onCopied(button);
				}

				function copyToClipboard(content){
					var currentLink = document.createElement("textarea");
					//currentLink.class = "copytext";
					document.body.appendChild(currentLink);
					//console.log(content);
					currentLink.value = content;
					currentLink.select();
					document.execCommand("copy");
					document.body.removeChild(currentLink);
				}
			})();
		');
		
		//$toggleButton = '';
		//$toggleButton = '<a data-toggle="copy" class="btn-primary btn-xs copy-btn" href="javascript:;">Copy</a>';
		return Html::tag('div', $content, $this->options);
	}
}