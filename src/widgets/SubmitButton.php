<?php
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;

class SubmitButton extends \yii\bootstrap\Widget {

	public $label;
	public $form;
	public $options;
	public $id;

	public $target;
	public $url;

	public function init()
    {
        parent::init();

        $this->id = $this->id ? $this->id : 'external-submit-button-' . $this->label;

        $this->options = $this->options ? $this->options : [];
        $this->options['id'] = $this->id;

        $this->options = ArrayHelper::merge($this->options,[
        	'class' => (isset($this->options['class']) ? $this->options['class'] . ' ' : '') .  $this->getScriptPrefix() . 'submit-button',
        	'data-form-id' => $this->form,
        	'data-target' => $this->target,
        	'data-url' => $this->url,
        ]);
    }

    public function run()
    {
    	$this->getView()->registerJs('
    	$(document).on("click", ".' . $this->getScriptPrefix() . 'submit-button", function(e) {
		    e.preventDefault();

			var self = $(this);
		    var form = $("#" + self.data("form-id"));
		    if(!form.data("action")) form.data("action", form.attr("action"));

			' . $this->getScriptPrefix(true) . 'submit_button_clear_changes(form);

			if(self.data("target")) form.attr("target", self.data("target"));

			if(self.data("url")) form.attr("action", self.data("url"));

		    var tempElement = $("<input type=\'hidden\' class=\'' . $this->getScriptPrefix() . 'submit-button-temp-element\'/>");
			
		    form.append(tempElement.attr("name", this.name).val(self.val()));
		    form.submit();

		    setTimeout(function(){
		    	' . $this->getScriptPrefix(true) . 'submit_button_clear_changes(form);
		    }, 300);
		});

		function ' . $this->getScriptPrefix(true) . 'submit_button_clear_changes(form){
			form.removeAttr("target");
			form.attr("action", form.data("action"));
		    $(".' . $this->getScriptPrefix() . 'submit-button-temp-element").remove();
		}

        ', View::POS_END, $this->getScriptPrefix());

        return $this->render('SubmitButton', [
        	'label' => $this->label,
        	'options' => $this->options
        ]);
    }

	private function getScriptPrefix($underScore = false)
	{
		$glue = $underScore ? '_' : '-';

		$prefixParts = explode('\\', self::className());

		return implode($glue, $prefixParts) . $glue;
	}
}