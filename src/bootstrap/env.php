<?php
/**
 * Require helpers
 */

/**
 * Load application environment from .env file
 */
$dotenv = new \Dotenv\Dotenv(YII_PROJECT_BASE_PATH);
$dotenv->load();

/**
 * Init application constants
 */
defined('YII_DEBUG') or define('YII_DEBUG', env('YII_DEBUG', false));
defined('YII_ENV') or define('YII_ENV', env('YII_ENV', 'prod'));
defined('YII_LOCALHOST') or define('YII_LOCALHOST', env('YII_LOCALHOST', false));