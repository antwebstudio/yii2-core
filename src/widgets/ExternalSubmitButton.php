<?php
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\View;

class ExternalSubmitButton extends \yii\bootstrap\Widget {

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
    }

    public function run()
    {
    	$this->getView()->registerJs('
    	$("#' . $this->id . '").click(function(e) {
		    var self= $(this);
		    var form = $("#' . $this->form . '");
		    var tempElement = $("<input type=\'hidden\' external-submit-button-temp-element/>");
			
		    if($("[external-submit-button-temp-element]").length) $("[external-submit-button-temp-element]").remove();

		    tempElement
		        .attr("name", this.name)
		        .val(self.val());


		    form.append(tempElement);
		    form.submit();

		    e.preventDefault();
		});
        ', View::POS_LOAD, 'external-submit-button-' . $this->id);

        return $this->render('ExternalSubmitButton', [
        	'label' => $this->label,
        	'options' => $this->options
        ]);
    }
}