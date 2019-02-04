<?php

namespace yii2lab\test\helpers;

use yii\helpers\ArrayHelper;
use yii2rails\domain\base\BaseDto;
use yii2rails\domain\BaseEntity;
use yii2rails\extension\store\StoreFile;
use yii2rails\extension\yii\helpers\FileHelper;

class DataHelper {
	
	public static function loadForTest($package, $method, $defaultData = null, $format = 'json') {
		//$method = basename($method);
		$method = str_replace('tests\\', '', $method);
		$path = str_replace('::', SL, $method);
		$fileName = '_expect' . SL . $path . DOT . $format;
		return self::load($package, $fileName, $defaultData);
	}
	
	public static function load($packageName, $filename, $defaultData = null) {
		$store = new StoreFile(self::getDataFilename($packageName, $filename));
		$configExpect = $store->load();
		if(empty($configExpect)) {
			if(is_array($defaultData) || $defaultData instanceof BaseEntity || $defaultData instanceof BaseDto) {
				$defaultData = ArrayHelper::toArray($defaultData);
			}
			self::save($packageName, $filename, $defaultData);
			return $defaultData;
		}
		return $configExpect;
	}
	
	private static function save($packageName, $filename, $data) {
		$store = new StoreFile(self::getDataFilename($packageName, $filename));
		$store->save($data);
	}
	
	private static function getDataFilename($packageName, $filename) {
		return VENDOR_DIR . DS . $packageName . DS . 'tests' . DS . $filename;
	}
	
}
