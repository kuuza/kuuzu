<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

class Registry
{
    private static $data = [];

    public static function get($key)
    {
        if (!static::exists($key)) {
            trigger_error('Kuuzu\KU\Registry::get - ' . $key . ' is not registered');

            return false;
        }

        return static::$data[$key];
    }

    public static function set($key, $value, $force = false)
    {
        if (!is_object($value)) {
            trigger_error('Kuuzu\KU\Registry::set - ' . $key . ' is not an object and cannot be set in the registry');

            return false;
        }

        if (static::exists($key) && ($force !== true)) {
            trigger_error('Kuuzu\KU\Registry::set - ' . $key . ' already registered and is not forced to be replaced');

            return false;
        }

        static::$data[$key] = $value;
    }

    public static function exists($key)
    {
        return array_key_exists($key, static::$data);
    }
}
