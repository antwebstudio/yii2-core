<?php
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\View;

class ShowModalButton extends \yii\bootstrap\Widget {
	/**
	 * button label
	 * @var 
	 */
	public $label;
	
	/**
	 * modal content url
	 * @var string
	 */
	public $url;
	
	/**
	 * button options
	 * @var array
	 */
	public $options;
	
	public $modalOptions;

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

	public function init()
    {
        parent::init();

        $this->id = $this->id ? $this->id : $this->createElementId($this->url);

        $this->modalHeaderId = $this->id . '_modalHeader';
        $this->modalId = $this->id . '_modal';
        $this->modalContentId = $this->id . '_modalContent';

        $this->options = $this->options ? $this->options : [];
        $this->options['id'] = $this->id;
        $this->options['url'] = $this->url;
		
		$this->modalOptions['id'] = $this->modalId;

        \yii\bootstrap\Modal::begin($this->modalOptions);
    }

    public function run()
    {
		echo '<div id="' . $this->modalContentId . '"></div>';
		\yii\bootstrap\Modal::end();
		
		$this->options['data-toggle'] = 'modal';
		$this->options['data-target'] = '#'.$this->modalId;
		
        return \yii\helpers\Html::button($this->label, $this->options);
    }

    private function createElementId($string) {
	   $string = str_replace(' ', '_', $string);
	   $string = str_replace('/', '_', $string);

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}
}