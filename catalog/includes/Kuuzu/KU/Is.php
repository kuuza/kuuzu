<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

class Is
{
    public static function __callStatic($name, $arguments)
    {
        if (class_exists(__NAMESPACE__ . '\\Is\\' . $name)) {
            return (bool)call_user_func_array([
                __NAMESPACE__ . '\\Is\\' . $name,
                'execute'
            ], $arguments);
        }

        return false;
    }
}
