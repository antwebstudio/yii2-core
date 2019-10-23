<?php
/**
 * @link https://github.com/borodulin/yii2-highlightjs
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-highlightjs/blob/master/LICENSE
 */

namespace ant\widgets;

use yii\helpers\Html;
use yii\helpers\Json;
use ant\assets\HighlightjsAsset;

/**
 * @author Andrey Borodulin
 */
class HighlightjsWidget extends \yii\base\Widget
{

    /**
     * Options Reference
     * @link http://highlightjs.readthedocs.org/en/latest/api.html#configure-options
     * @var array
     */
    public $options = [];

    /**
     * Programming language to highlight
     * Hljs will autodetect it, if empty
     * @link http://highlightjs.readthedocs.org/en/latest/css-classes-reference.html#language-names-and-aliases
     * @var string
     */
    public $language;
    
    /**
     * Container tag
     * @var string
     */
    public $tag = 'pre';

    /**
     * Container html options
     * @var array
     */
    public $htmlOptions = [];

    /**
     * Syntax highlight style
     * @link https://highlightjs.org/static/demo/
     * @var unknown
     */
    public $style = 'default';
    
    /**
     * @var string
     */
    public $content;
    
    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId() .'_hljs';
        }
        echo Html::beginTag($this->tag, $this->htmlOptions);
        if (is_string($this->language)) {
            $codeOptions = ['class' => $this->language];
        } else {
            $codeOptions = [];
        }
        echo Html::beginTag('code', $codeOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->content) {
            echo Html::encode($this->content);
        }
        echo Html::endTag('code');
        echo html::endTag($this->tag);
        
        $view = $this->view;

        HighlightjsAsset::$style = $this->style;
        
        HighlightjsAsset::register($view);
        
        $id = $this->htmlOptions['id'];

        if (!empty($this->options)) {
            $options = Json::encode($this->options);
            $view->registerJs("hljs.configure($options);");
        }
        $view->registerJs("hljs.highlightBlock(document.getElementById('$id'));");
    }
}