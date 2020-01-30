<?php
namespace ant\components;

use yii\helpers\ArrayHelper;

class MenuManager extends \yii\base\Component {
	const MENU_MEMBER = 'member';
	
	public $mainMenu = [];
	public $menu = [];
	
	public function getMainMenu() {
		if (is_callable($this->mainMenu)) {
			return call_user_func_array($this->mainMenu, []);
		} else if (is_array($this->mainMenu)) {
			return $this->mainMenu;
		}
		
		return [
		];
    }
	
	//return null in config for blank menu
	public function getMenu($name, $params = [], $default = []) {
		if (!isset($this->menu[$name])) {
			return $default;
		}
		
		if (is_callable($this->menu[$name])) {
			return call_user_func_array($this->menu[$name], [$params]);
		} else if (is_array($this->menu[$name])) {
			return $this->menu[$name];
		}
		
		return [
		];
	}
	
	public function isItemActive($item, $currentRoute)
    {
        /*if (!$this->activateItems) {
            return false;
        }*/
        if (isset($item['active'])) {
            return ArrayHelper::getValue($item, 'active', false);
        }
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $currentRoute) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                /*foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }*/
            }

            return true;
        }

        return false;
    }
	
	/*protected function getPublicIdentity() {
		return Yii::$app->user->identity->getPublicIdentity();
	}
	
	protected function checkIsGuest() {
		return Yii::$app->user->isGuest;
	}*/
}