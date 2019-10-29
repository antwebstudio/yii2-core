<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/* @var $this \yii\web\View */
/* @var $content string */

$this->params['sideNav']['items'] = \Yii::$app->menu->getMenu(\ant\components\MenuManager::MENU_MEMBER);
?>

<?php $this->beginContent('@app/views/layouts/left-sidenav.php') ?>
	<?= $content ?>
<?php $this->endContent() ?>