<?php
namespace ant\widgets;

use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

class SearchName extends Widget{
	public $label = false;
	public $model;
	
	public function init()
	{
		parent::init();
	}

	public function run(){	

            $form = ActiveForm::begin(['id' => 'contact-form', 'options' => ['enctype' => 'multipart/form-data',
                'data-pjax' => '' ,
                ]
                ,        
                         'method' => 'get',
            ]);

            echo $form->field($this->model, 'employee')->textInput([
                // 'onChange' => "$(this).parents('form').submit();",
                'autofocus' => 'autofocus',
                'id' => 'searchBox'
            ])->label($this->label);

             ActiveForm::end(); 

$this->view->registerJs("
	 var delay = (function(){
                var timer = 0;
                return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
                  };
                })();

            $('#searchBox').on('keyup',function(){
                    //$(#contact-form).submit();
                var input= $(this);
                delay(function(){
                input.parents('form').submit();
                         }, 1000 );

});

			        var input = $('#searchBox');
                    var len = input.val().length;
                    input[0].focus();
                    input[0].setSelectionRange(len, len);
	");

             }

        }


