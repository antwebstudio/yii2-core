<?php
namespace ant\widgets;

use yii\bootstrap\Tabs; 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\base\Widget;

class Steps extends Widget
{
	public $items;

	public function init()
	{
		parent::init();
		$this->registerJs();
	}

	public function run()
	{
		$html = '<div id="' . $this->id . '">';

		$html .= Tabs::widget([
			'id' => $this->id . '-tabs',
			'items' => $this->items,
			'headerOptions' => ['step-tab' => true],
		]);


		$html .= '<div class ="btnAction">';
		
		$html .= Html::button('Previous', ['id' => $this->id . '-prev', 'class' => 'btn btn-secandary']);
		$html .= Html::button('Next', ['id' => $this->id . '-next', 'class' => 'btn btn-primary pull-right']);
		
		$html .= '<div class="clearfix"></div>';

		$html .= '</div>';


		$html .= '</div>';
		
		return $html;
	}


	public function registerJs()
	{
		$this->view->registerJs('
			(function($) {	
				var $widget = $("#' . $this->id . '");
				var $tabs = $("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab]");
				var $prevBtn = $widget.find("#' . $this->id . '-prev");
				var $nextBtn = $widget.find("#' . $this->id . '-next");

				var current = 0;

				var min = 0;
				var max = 0;

				var inited = false;

				stepInit();
				addPassClass(current);
				removeBtn(current);
				tabError();

				$widget.find("#' . $this->id . '-prev").on("click", function(e) {
					e.preventDefault();
					
					stepInit();

					var prevIndex = current - 1;
					var index = prevIndex >= min ? prevIndex : min;
					var $tab = $("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab=" + index + "]");
					$tab.find("[data-toggle=tab]").trigger("click");

					addPassClass(index);
					removeBtn(index);
				});

				$widget.find("#' . $this->id . '-next").on("click", function(e) {
					e.preventDefault();

					stepInit();
					
					var nextIndex = current + 1;
					var index = nextIndex <= max ? nextIndex : max;
					var $tab = $("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab=" + index + "]");
					$tab.find("[data-toggle=tab]").trigger("click");

					addPassClass(index);
					removeBtn(index);
				});

				function stepInit()
				{
					$tabs.each(function(i, e) {
						$e = $(e);
						$e.attr("step-tab", i);

						if (!inited) 
						{
							$e.on("click", function() {
								current = $(this).attr("step-tab");
								addPassClass(current);
								removeBtn(current);
							});
						}

						if ($e.hasClass("active")) current = i;

						max = i;
					});

					inited = true;
				}

				function addPassClass(current)
				{
					$("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab]").removeClass("pass");
					for (var i = 0; i < current; i ++)
					{
						var $tab = $("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab=" + i + "]");
						$tab.addClass("pass");
					}
				}

				function removeBtn(current)
				{
					$prevBtn.show();
					$nextBtn.show();

					if(current == 0)
					{
						$prevBtn.hide();
					}
					else if (current == 3)
					{
						$nextBtn.hide();
					}
				}

				function tabError()
				{
					var $tabContents = $("#' . $this->id . ' .tab-content .tab-pane");
					//console.log ($tabContents);

					$tabContents.each(function(i, e) {
						var $e = $(e);
						var $errors = $e.find(".has-error");

						//console.log(i + " : " +  $errors.length);

						//console.log($errors);
						if($errors.length >0)
						{
							var $tab = $("#' . $this->id . ' #' . $this->id . '-tabs li[step-tab=" + i + "]")
							$tab.find("[data-toggle=tab]").trigger("click");
	
							return false;				
						} 
					});

				}
				

				/*
				function removeBtn(current)
				{
					$prevBtn.removeClass("disabled");
					$nextBtn.removeClass("disabled");


					if(current == 0)
					{
						$prevBtn.addClass("disabled");
					}
					else if (current == 3)
					{
						$nextBtn.addClass("disabled");
					}
				}*/
			}(jQuery));
		');
	}
}