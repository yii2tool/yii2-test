<?php

use yii2rails\app\domain\helpers\Config;

$config = \yii2rails\app\domain\helpers\EnvService::get('config');
return Config::loadData($config);
