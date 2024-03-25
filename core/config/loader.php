<?php

namespace core\config;
// 全局类定义
class Loader
{
    private static $results = [];

    public static function add($result, $name) {
        self::$results[$name] = $result;
    }

    public static function get($str) {
        return isset(self::$results[$str]) ? self::$results[$str] : null;
    }
}