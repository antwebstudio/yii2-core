<?php
namespace ant\widgets;

use Yii;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\Block;

use ant\helpers\ArrayHelper;

class AjaxActiveForm extends \yii\widgets\ActiveForm
{
	public $successBlock;
	
	public $hideFormOnSuccess = true;
	
	public $resetFormOnSuccess = false;
	
	public function behaviors() {
		return [
			[
				'class' => 'ant\behaviors\WidgetClientEventBehavior',
			],
		];
	}

	public function init() {
		return parent::init();
	}
	
    /**
     * {@inheritdoc}
     */
    public function run()
    {
		$this->registerClientEvents($this->id);
		
        $html = parent::run();
		
        $js = $this->getAjaxActiveFormJs();
        $this->getView()->registerJs($js);
		
		return $html.Html::tag('div', $this->successBlock, ['id' => $this->id.'_success', 'style' => 'display: none;']);
    }
	
	public function beginOnSuccess() {
		ob_start();
	}
	
	public function endOnSuccess() {
		// If not hide form then it need to be shown outside the form tag, or else it can be shown in wherever it is.
		if ($this->hideFormOnSuccess) {
			$this->successBlock = ob_get_clean();
		} else {
			echo Html::tag('div', ob_get_clean(), ['id' => $this->id.'_success', 'style' => 'display: none;']);;
		}
	}
	
    /**
     * @return string
     */
    protected function getAjaxActiveFormJs()
    {
        $id = $this->getId();
		
		$afterSuccess = '';
		if ($this->hideFormOnSuccess) {
			$afterSuccess .= '$form.hide();';
		}
		if ($this->resetFormOnSuccess) {
			$afterSuccess .= '$form.trigger("reset");';
		}
		
        return '
			(function () {
				var $form = $("#'.$id.'");
				var $success = $("#'.$this->id.'_success'.'");
				  
				$form.on("beforeSubmit", function () {
					if ($form.data("blocked")) {
						console.log("form submit canceled");
						return false;
					}
					if ($form.triggerHandler("beforeSend") === false) {
						console.log("form submit canceled");
						return false;
					}
					$success.hide();
					$form.data("blocked", true);
					
					$.ajax($form.attr("action"), {
						method: $form.attr("method"),
						data: $form.serialize(),
						success: function (response) {
							if (!response.success) {
								if (response.errors) {
									$form.yiiActiveForm("updateMessages", response.errors);
								}
								return;
							}

							'.$afterSuccess.'
							$success.show();
							$form.trigger("success", response);

						},
						complete: function () {
							$form.data("blocked", false);
						}
					}).fail(function(xhr, status, response) {
						$form.trigger("fail", response);
					});
					return false;
				});
				  
				$success.hide();
			})();
		';
    }

}
