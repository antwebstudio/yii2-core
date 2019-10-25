<?php
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\View;

class ShowModalButton extends \yii\bootstrap\Widget {

	/**
	 * element id
	 * @var
	 */
	public $id;
	/**
	 * button label
	 * @var 
	 */
	public $label;
	/**
	 * modal title
	 * @var string
	 */
	public $title;
	/**
	 * modal content url
	 * @var string
	 */
	public $url;
	/**
	 * close modal popup by clicking background, specify static for a backdrop which doesn't close the modal on click.
	 * @var boolean | 'static'
	 */
	public $backdrop = true;
	/**
	 * Closes the modal when escape key is pressed
	 * @var boolean
	 */
	public $keyboard = true;
	/**
	 * modal size
	 * @var string
	 */
	public $size = 'modal-lg';
	/**
	 * button options
	 * @var array
	 */
	public $options;

	/**
	 * modal header id
	 * @var 
	 */
	private $modalHeaderId;
	/**
	 * modal id
	 * @var
	 */
	private $modalId;

	/**
	 * modal content id
	 * @var
	 */
	private $modalContentId;

	private $modal;

	public function init()
    {
        parent::init();

        $this->id = $this->id ? $this->id : $this->createElementId($this->url);

        $this->modalHeaderId = $this->id . '_modalHeader';
        $this->modalId = $this->id . '_modal';
        $this->modalContentId = $this->id . '_modalContent';

        $this->options = $this->options ? $this->options : [];
        $this->options['id'] = $this->id;
        if (!isset($this->options['title'])) $this->options['title'] = $this->title;
        $this->options['url'] = $this->url;

        ob_start();
        \yii\bootstrap\Modal::begin([
        	'header' => '<h2>' . $this->title . '</h2>',
    		//'toggleButton' => ['label' => 'click me'],
		    'headerOptions' => ['id' => $this->modalHeaderId],
		    'id' => $this->modalId,
		    'size' => $this->size,
		    //keeps from closing modal with esc key or by clicking out of the modal.
		    // user must click cancel or X to close
		    'clientOptions' => ['backdrop' => $this->backdrop, 'keyboard' => $this->keyboard]
		]);
		echo "<div id='" . $this->modalContentId . "' modalcontent></div>";
		\yii\bootstrap\Modal::end();
		$this->modal = ob_get_clean();
    }

    public function run()
    {
    	$this->getView()->registerJs('
    	$("' . trim(addslashes(str_replace("\n", '', $this->modal))) . '").appendTo("body");
        ', View::POS_READY, 'show-modal-' . $this->id);


    	$this->getView()->registerJs('
		$(document).on("click", "#' . $this->id . '", function(){
			var url = $(this).attr("url");
			var title = $(this).attr("title");

			$("#' . $this->modalId . '").find("#' . $this->modalContentId . '").load(url, function() { 
	        	$("#' . $this->modalId . '").modal("show");
	        	//document.getElementById("' . $this->modalHeaderId . '").innerHTML = "<h4>" + title + "</h4>";
		    });
    	});
        ', View::POS_LOAD, 'show-modal-button-' . $this->id);
        return $this->render('ShowModalButton', [
        	'label' => $this->label,
        	'options' => $this->options,

        	'modalHeaderId' => $this->modalHeaderId,
        	'modalId' => $this->modalId,
        	'modalContentId' => $this->modalContentId,

        	'backdrop' => $this->backdrop,
        	'keyboard' => $this->keyboard,
        	'size' => $this->size,

        ]);
    }

    private function createElementId($string) {
	   $string = str_replace(' ', '_', $string);
	   $string = str_replace('/', '_', $string);

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}
}