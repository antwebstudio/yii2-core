<?php
namespace ant\widgets;

use Yii;
use yii\helpers\Html;
use ant\widgets\assets\NestedSortableColumnAsset;

class NestedSortableColumn extends \yii\grid\DataColumn {
	public $structureId;
	public $moveElementUrl;
	public $depthAttribute = 'depth';
	public $handleTooltip = 'Drag to sort';

	public function init() {
		$this->grid->options['data-structure-id'] = $this->structureId;
		$this->grid->tableOptions['class'] = 'main items elements table';
		
		$this->grid->rowOptions = function($model, $key, $index, $grid) {
			return [
				'data-descendants' => 0, 
				'data-id' => $model->id,
				'data-level' => $model->{$this->depthAttribute}
			];
		};
		$this->options['data-titlecell'] = '';
		
		$this->registerNestedSortableScript();
		parent::init();
	}

	public function renderDataCell($model, $key, $index) {
		//$data = $this->grid->dataProvider->data[$model];
		$options = $this->options;
		/*if ($this->cssClassExpression !== null) {
			$class = $this->evaluateExpression($this->cssClassExpression, array(
				'row' => $model, 
				//'data' => $data 
			));
			if (! empty($class)) {
				if (isset($options['class']))
					$options['class'] .= ' ' . $class;
				else
					$options['class'] = $class;
			}
		}*/
		$html = '';
		
		$html .= Html::beginTag('td', $options);
		$html .= "<a class=\"move icon glyphicon glyphicon-move\" title=\"".$this->handleTooltip."\" role=\"button\"></a> &nbsp; ";
		$html .= $this->renderDataCellContent($model, $key, $index);
		$html .= Html::endTag('td');
		
		return $html;
	}

	protected function registerNestedSortableScript() {
		$this->options['data-structure-id'] = 1;
		
		NestedSortableColumnAsset::register($this->grid->view);
		
		/*$this->grid->view->registerJs('
			
		');*/
		
		$this->grid->view->registerJs('
			Ant.elementIndex = Ant.createElementIndex("Category", $("#'.$this->grid->id.'"), {
				context:        "index",
				showStatusMenu: false,
				showLocaleMenu: false,
				storageKey:     "elementindex.Category",
				criteria:       { localeEnabled: null },
				moveElementUrl: "'.\yii\helpers\Url::to($this->moveElementUrl).'",
			});
		');
		
	}
}