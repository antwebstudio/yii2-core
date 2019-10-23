<?php
namespace ant\widgets;

use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

class JsToggleHideField extends Widget{
	public $idOnChange;
	public $idRespond;
	
	public function init()
	{
		parent::init();
        if ($this->idOnChange == null || is_array($this->idOnChange)) {
            throw new \Exception('idOnChange can not be null or array');
        }
        if ($this->idRespond == null || is_array($this->idRespond)) {
            throw new \Exception('IdRespond can not be null or array');
        }
	}

	public function run(){	

    $this->view->beginBlock('jsToggleHideField', true);

    $this->view->registerJs("
        $('". $this->idOnChange . "').change(function(){
            $('". $this->idRespond . "').parent().find('label').toggleClass('hide');
            $('". $this->idRespond . "').toggleClass('hide');
            $('". $this->idRespond . "').val('');
        })
    ");
        
    $this->view->endBlock();

    }
}


