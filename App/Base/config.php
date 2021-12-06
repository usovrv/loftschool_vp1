<?php

namespace App\Base;


abstract class Config
{
    private static $cfg;

    public static function loadConfig()
    {
        self::$cfg = require(ROOT . '/config.php');
    }

    public static function getMinLoginLength()
    {
        return self::$cfg['user']['minLoginLength'];
    }

    public static function getMaxLoginLength()
    {
        return self::$cfg['user']['maxLoginLength'];
    }

    public static function getMinPasswordLength()
    {
        return self::$cfg['user']['minPasswordLength'];
    }

    public static function getMaxPasswordLength()
    {
        return self::$cfg['user']['maxPasswordLength'];
    }

    public static function getCookieCryptPassword()
    {
        return self::$cfg['cookie']['cryptPassword'];
    }

    public static function getCookieLiveTime()
    {
        return self::$cfg['cookie']['liveTime'];
    }
}
