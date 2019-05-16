<?php

namespace yii2lab\test\helpers;

use yii\helpers\ArrayHelper;
use yii2rails\app\domain\enums\YiiEnvEnum;
use yii2rails\app\domain\helpers\Config;
use yii2rails\app\domain\helpers\Env;
use yii2rails\extension\yii\helpers\FileHelper;

class TestHelper {
	
	const PACKAGE_TEST_DB_FILE = '@common/runtime/sqlite/test.db';

    public static function getMode() {
        return self::getEnvLocalConfig('test.mode', YiiEnvEnum::DEV);
    }

    public static function getServerConfig($key) {
        $mode = TestHelper::getMode();
        return TestHelper::getEnvLocalConfig('test.server.'.$mode.'.' . $key);
    }

    public static function isSkipMode($mode, $message = null) {
        $modes = ArrayHelper::toArray($mode);
        $isSkip = in_array(self::getMode(), $modes);
        if($isSkip) {
            $m = 'skip mode' . ' - ' . implode(', ', $modes);
            if($message) {
                $m .= ' - ' . $message;
            }
            self::printMessage($m);
        }
        return $isSkip;
    }

    public static function isSkipBug($message = null) {
        $isSkip = (boolean) self::getEnvLocalConfig('test.skipBug');
        if($isSkip) {
            $m = 'skip bug';
            if($message) {
                $m .= ' - ' . $message;
            }
            self::printMessage($m);
        }
        return $isSkip;
    }

    public static function printMessage($message) {
        $m = '! >>> ' . $message . PHP_EOL;
        fwrite(STDERR, $m);
    }

    public static function dump($message) {
        fwrite(STDERR, print_r($message, TRUE));
    }

    public static function getEnvLocalConfig($name, $default = null) {
        $configFile = __DIR__ . '/../../../../../common/config/env-local.php';
        $config = \yii2rails\extension\common\helpers\Helper::includeConfig($configFile);
        return ArrayHelper::getValue($config, $name, $default);
    }

	public static function copySqlite($dir, $isForce = true) {
		$sourceFile = $dir . '/db/test.db';
		$targetFile = ROOT_DIR . '/common/runtime/sqlite/test.db';
        if(!$isForce && FileHelper::has($targetFile)) {
            return;
        }
        if(!FileHelper::has($sourceFile)) {
            return;
        }
        FileHelper::copy($sourceFile, $targetFile);
	}
	
	public static function loadEnvFromPath($path) {
		$config = require(ROOT_DIR . DS . TEST_APPLICATION_DIR . DS . 'common/config/env.php');
		$config['app'] = self::replacePath($config['app'], $path);
		$config['config'] = self::replacePath($config['config'], $path);
		return $config;
	}
	
	public static function loadConfigFromPath($path) {
		$definition = Env::get('config');
		$definition = self::replacePath($definition, $path);
		$testConfig = Config::loadData($definition);
		return $testConfig;
	}
	
	public static function loadConfig($name, $dir = TEST_APPLICATION_DIR) {
		$dir = FileHelper::trimRootPath($dir);
		$path = rtrim(ROOT_DIR . DS . $dir, DS);
		$baseConfig = @include($path . DS . $name);
		return $baseConfig;
	}
	
	private static function replacePath($definition, $path) {
		$path = FileHelper::normalizePath($path);
		$path = self::trimPath($path);
		$filters = [];
		foreach(['filters', 'commands'] as $type) {
			if(!empty($definition[$type])) {
				foreach($definition[$type] as $filter) {
					$filter = self::filterItem($filter, $path);
					if(!empty($filter['filters'])) {
                        $filter = self::replacePath($filter, $path);
                    }
					if($filter) {
						$filters[] = $filter;
					}
				}
				$definition[$type] = $filters;
			}
		}
		return $definition;
	}
	
	private static function trimPath($path) {
		$path = FileHelper::trimRootPath($path);
		$commonDir = DS . 'config';
		if(strpos($path, $commonDir) !== false) {
			$path = substr($path, 0, - strlen($commonDir));
		}
		return $path;
	}
	
	private static function filterItem($filter, $path) {
		if(is_string($filter)) {
			return $filter;
		}
		if(!array_key_exists('app', $filter)) {
			return $filter;
		}
		if($filter['app'] == TEST_APPLICATION_DIR . DS . 'console') {
			return null;
		}
		if($filter['app'] == TEST_APPLICATION_DIR . DS . 'common') {
			$filter['app'] = $path;
		}
		return $filter;
	}
	
}
