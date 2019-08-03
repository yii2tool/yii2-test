<?php

namespace yii2tool\test\helpers;

class LangTestHelper
{

    private static $lang = null;

    public static function set($lang) {
        self::$lang = $lang;
    }

    public static function get() {
        return self::$lang;
    }

}
