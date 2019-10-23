<?php  
namespace ant\widgets;

use yii\helpers\Html;
use yii\web\JsExpression;

class ActiveField extends \yii\widgets\ActiveField
{
	public function render($content = null) {
		if (!isset($this->parts['{value}'])) {
			// $this->value(); // caused error on backend ecommerce edit product page
		}
		return parent::render($content);
	}

	public function value($options = []) {
		$this->parts['{value}'] = Html::activeHiddenInput($this->model, $this->attribute, $options).Html::getAttributeValue($this->model, $this->attribute);
	}
	
	/*
		This overwrite is needed for ant\validators\SerializableDataValidator
		which to make the client side validation of the validator work
	*/
	protected function getClientOptions()
    {
        $attribute = Html::getAttributeName($this->attribute);
        if (!in_array($attribute, $this->model->activeAttributes(), true)) {
            return [];
        }

        $clientValidation = $this->isClientValidationEnabled();
        $ajaxValidation = $this->isAjaxValidationEnabled();

        if ($clientValidation) {
            $validators = [];
            foreach ($this->model->getActiveValidators($attribute) as $validator) {
                /* @var $validator \yii\validators\Validator */
                $js = $validator->clientValidateAttribute($this->model, $attribute, $this->form->getView(), $this->attribute);
                if ($validator->enableClientValidation && $js != '') {
                    if ($validator->whenClient !== null) {
                        $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                    }
                    $validators[] = $js;
                }
            }
        }

        if (!$ajaxValidation && (!$clientValidation || empty($validators))) {
            return [];
        }

        $options = [];

        $inputID = $this->getInputId();
        $options['id'] = Html::getInputId($this->model, $this->attribute);
        $options['name'] = $this->attribute;

        $options['container'] = isset($this->selectors['container']) ? $this->selectors['container'] : ".field-$inputID";
        $options['input'] = isset($this->selectors['input']) ? $this->selectors['input'] : "#$inputID";
        if (isset($this->selectors['error'])) {
            $options['error'] = $this->selectors['error'];
        } elseif (isset($this->errorOptions['class'])) {
            $options['error'] = '.' . implode('.', preg_split('/\s+/', $this->errorOptions['class'], -1, PREG_SPLIT_NO_EMPTY));
        } else {
            $options['error'] = isset($this->errorOptions['tag']) ? $this->errorOptions['tag'] : 'span';
        }

        $options['encodeError'] = !isset($this->errorOptions['encode']) || $this->errorOptions['encode'];
        if ($ajaxValidation) {
            $options['enableAjaxValidation'] = true;
        }
        foreach (['validateOnChange', 'validateOnBlur', 'validateOnType', 'validationDelay'] as $name) {
            $options[$name] = $this->$name === null ? $this->form->$name : $this->$name;
        }

        if (!empty($validators)) {
            $options['validate'] = new JsExpression('function (attribute, value, messages, deferred, $form) {' . implode('', $validators) . '}');
        }

        if ($this->addAriaAttributes === false) {
            $options['updateAriaInvalid'] = false;
        }

        // only get the options that are different from the default ones (set in yii.activeForm.js)
        return array_diff_assoc($options, [
            'validateOnChange' => true,
            'validateOnBlur' => true,
            'validateOnType' => false,
            'validationDelay' => 500,
            'encodeError' => true,
            'error' => '.help-block',
            'updateAriaInvalid' => true,
        ]);
    }
	
	/*public function textInput($options = [])
    {
        return parent::textInput($this->generateAutoPlaceHolder($options));
    }

	public function passwordInput($options = [])
	{
		return parent::passwordInput($this->generateAutoPlaceHolder($options));
	}

	public function textarea($options = [])
	{
		return parent::textarea($this->generateAutoPlaceHolder($options));
	}

    private function generateAutoPlaceHolder($options)
    {
    	if (!isset($options['placeholder'])) $options['placeholder'] = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));

        return $options;
    }*/
}