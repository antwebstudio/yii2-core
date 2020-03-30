<?php
namespace ant\lifecycle;

class Status extends \yii\base\Component {
    const ACTIVE = 0;
    const CLOSED = 2;

    public $statusText = [];
	public $statusSpanCss = [];
	public $statusTransit = [];
	public $defaultStatusSpanCss = 'label label-default';

    public $model;
    public $attribute;

    public function getTextByValue($value) {
		if (!isset($this->statusText[$value])) throw new \Exception('statusText for status "'.$value.'" is not set. ');
        return $this->statusText[$value];
    }
	
	public function getHtml() {
		return '<span class="'.$this->getStatusSpanCss().'">{status}</span>';
	}

    public function getText() {
        return $this->getTextByValue($this->getValue());
    }

    public function getValue() {
        return $this->model->{$this->attribute};
    }
	
	protected function getStatusSpanCss() {
		$value = $this->getValue();
		return isset($this->statusSpanCss[$value]) ? $this->statusSpanCss[$value] : $this->defaultStatusSpanCss;
	}

    protected function setValue($status) {
        $this->model->{$this->attribute} = $status;
    }

    public function transit($status) {
		return $this->setValue($status);
    }
	
	public function checkTransitPermission($fromStatus, $toStatus) {
		if ($fromStatus === $toStatus) {
			return true;
		} else if (isset($this->statusTransit[$toStatus])) {
			if (is_callable($this->statusTransit[$toStatus])) {
				return call_user_func_array($this->statusTransit[$toStatus], [$this->model, $fromStatus]);
			}
			return $this->statusTransit[$toStatus];
		}
		return true;
	}
}