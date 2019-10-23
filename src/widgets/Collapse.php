<?php
namespace ant\widgets;

use yii\helpers\Html;

class Collapse extends \yii\base\Widget {
	public $show = false;
	public $toggleButton;
	public $autoToggleButton = false;
	public $closeButtonSelector = '[data-toggle=collapse]'; // Add 'global:' as prefix for global wise selector, eg: global:[data-toggle]
	public $options;
	
	public function init() {
		
		ob_start();
	}
	
	public function run() {
		$content = ob_get_contents();
		ob_end_clean();
		
		$id = $this->id;
		$toggleButtonId = $id.'_toggle_button';
		
		$this->toggleButton['options']['data-toggle'] = 'collapse';
		$this->toggleButton['tagName'] = 'a';
		$this->toggleButton['options']['href'] = '#'.$id;
		$this->toggleButton['options']['id'] = $toggleButtonId;
	
		$button = isset($this->toggleButton) ? \yii\bootstrap4\Button::widget($this->toggleButton) : '';
		
		$this->view->registerJs('
			(function() {
				var container = document.querySelector("#'.$id.'");
				var closeButton = document.querySelectorAll("#'.$id.' '.$this->closeButtonSelector.'");
				var toggleButton = document.querySelector("#'.$toggleButtonId.'");
				toggleButton.style.initDisplay = toggleButton.style.display;
				'.($this->show ? 'toggleButton.style.display = "none";' : '').'
				
				toggleButton.onclick = function() {
					'.($this->autoToggleButton ? 'event.target.style.display = "none";' : '').'
				}
				
				for (var i in closeButton) {
					closeButton[i].onclick = function() {
						jQuery(container).collapse("hide");
						'.($this->autoToggleButton ? 'toggleButton.style.display = toggleButton.style.initDisplay;' : '').'
					}
				}
			})();
		');
		
		$this->options['id'] = $id;
		$this->options['class'] = 'collapse';
		if ($this->show) {
			$this->options['class'] .= ' show';
		}
		return $button . Html::tag('div', $content, $this->options);
	}
}