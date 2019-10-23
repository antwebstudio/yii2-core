<?php

/**
 * Yii2 default aliases
 */
Yii::setAlias('@project', YII_PROJECT_BASE_PATH);
Yii::setAlias('@common', YII_APP_BASE_PATH . '/common');
Yii::setAlias('@frontend', YII_APP_BASE_PATH . '/frontend');
Yii::setAlias('@backend', YII_APP_BASE_PATH . '/backend');
Yii::setAlias('@console', YII_APP_BASE_PATH . '/console');
Yii::setAlias('@root', YII_APP_BASE_PATH);
//Yii::setAlias('@projects', YII_APP_BASE_PATH . '/projects');

/**
 *  Custom aliases
 */
Yii::setAlias('@api', YII_APP_BASE_PATH. '/api');
Yii::setAlias('@storage', YII_PROJECT_BASE_PATH . '/storage');

Yii::setAlias('@baseUrl', env('BASE_URL'));
Yii::setAlias('@backendUrl', env('BACKEND_URL'));
Yii::setAlias('@frontendUrl', env('FRONTEND_URL'));
Yii::setAlias('@apiUrl', env('API_URL'));
Yii::setAlias('@storageUrl', env('STORAGE_URL'));
