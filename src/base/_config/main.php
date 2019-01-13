<?php

use yii2lab\app\domain\helpers\Config;

$config = \yii2lab\app\domain\helpers\EnvService::get('config');
return Config::loadData($config);
