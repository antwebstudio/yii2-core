<?php
namespace ant\web;

class FormModel extends \ant\base\FormModel {
	

    /*
    'username' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter username...']],
    'password' => ['type'=>Form::INPUT_PASSWORD, 'options'=>['placeholder'=>'Enter password...']],
    'rememberMe' => ['type'=>Form::INPUT_CHECKBOX],
    */
    public function getFormAttributes($name = null) {
		return [
		];
    }
	
	public function getGridFormRows($name = null) {

	}

    public function beforeSave() {

    }

    public function load($data, $formName = null) {
        $this->_isPostBack = parent::load($data, $formName);
        return $this->_isPostBack;
    }

    public function confirm() {
        return $this->validate() && $this->confirm;
    }

    public function getIsPostBack() {
        return $this->_isPostBack;
    }
	
	public function setRedirectUrl($value) {
		$this->_redirectUrl = $value;
	}
	
	public function getRedirectUrl() {
		return $this->_redirectUrl;
	}
}