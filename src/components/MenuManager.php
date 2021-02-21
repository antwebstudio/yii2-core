<?php
namespace ant\components;

use Yii;
use yii\helpers\Url;
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

	public function getMenu($name, $params = [], $default = []) {
		$menu = $this->_getMenu($name, $params, $default);
		return $this->processMenu($menu);
	}
	
	//return null in config for blank menu
	public function _getMenu($name, $params = [], $default = []) {
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

	public function getMenuByUrlQuery() {
		return $this->getMenu(Yii::$app->request->get('tab'));
	}
	
	public function isItemActive($item, $currentRoute)
    {
        /*if (!$this->activateItems) {
            return false;
        }*/
        if (isset($item['active'])) {
            return ArrayHelper::getValue($item, 'active', false);
        }

		if ($this->yii2IsItemActive($item, $currentRoute)) {
			return true;
		}

        if (isset($item['url'])) {
			$route = is_array($item['url']) ? $item['url'][0] : $item['url'];
			$route = trim($route, '/');

			$queryMatched = false;
			if (is_array($item['url'])) {
				// Get params from url
				$params = $item['url'];
				array_shift($params);
				unset($params['#']);

				$queryMatched = $params == Yii::$app->request->get();
			}
			
			return ($route == $currentRoute || $route.'/index' == $currentRoute) && $queryMatched;
            
        }

        return false;
    }

	/**
	 * Copied from yii\widgets\Menu
	 */
	protected function yii2IsItemActive($item, $currentRoute)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if (strpos($route, '/') !== 0 && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $currentRoute) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

	protected function processMenu($menu) {
		foreach ($menu as &$item) {
			if ($this->isItemActive($item, Yii::$app->controller->route)) {
				$item['active'] = true;
			}
		}
		return $menu;
	}
	
	/*protected function getPublicIdentity() {
		return Yii::$app->user->identity->getPublicIdentity();
	}
	
	protected function checkIsGuest() {
		return Yii::$app->user->isGuest;
	}*/
}