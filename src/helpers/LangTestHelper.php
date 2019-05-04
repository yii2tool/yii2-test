<?php

namespace yii2lab\test\helpers;

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
