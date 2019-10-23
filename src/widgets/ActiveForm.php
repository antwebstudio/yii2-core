<?php
namespace ant\widgets;

use Yii;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Json;

use ant\helpers\ArrayHelper;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
	public $fieldClass = 'ant\widgets\ActiveField';

	//ajax
	public $allowAjaxSubmit = true;

	public $ajaxOptions = [];

	protected function getDefaultAjaxOption()
	{
		return
		[
			'url' 		=> new JsExpression("$(this).attr('action')"),
			'type' 		=> new JsExpression("$(this).attr('method')"),
			'data' 		=> new JsExpression("$(this).serialize()"),
			'success' 	=> new JsExpression("function(response){}"),
			'error' 	=> new JsExpression("function(){alert('submit error')}"),
		];
	}

	public function init()
	{
		if (YII_DEBUG) throw new \Exception('Deprecated, please use ant\widgets\AjaxActiveForm instead. ');
		
		parent::init();

		if(Yii::$app->request->isAjax && $this->allowAjaxSubmit)
		{
			$this->view->registerJs("

			(function(){

				$('#" . $this->id . "').on('beforeSubmit', function(e){

	                $.ajax(" . Json::encode(ArrayHelper::merge($this->getDefaultAjaxOption(), $this->ajaxOptions)) . ");

					e.preventDefault();
				}).on('submit', function(e){e.preventDefault();});

			})();

			", View::POS_END);
		}
	}
}
?>
