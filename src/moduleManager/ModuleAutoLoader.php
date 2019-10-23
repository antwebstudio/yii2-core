<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace ant\moduleManager;

use Yii;
use yii\base\BootstrapInterface;

/**
 * ModuleAutoLoader automatically searches for autostart.php files in module folder an executes them.
 *
 * @author luke
 */
class ModuleAutoLoader implements BootstrapInterface
{

    public function bootstrap($app)
    {
		Yii::$app->moduleManager->loadRegisteredModules();
    }

}
