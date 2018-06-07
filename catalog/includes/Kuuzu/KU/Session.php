<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

use Kuuzu\KU\KUUZU;

class Session
{
    public static function load($name = null)
    {
        $class_name = 'Kuuzu\\KU\\Session\\' . KUUZU::getConfig('store_sessions');

        if (!class_exists($class_name)) {
            trigger_error('Session Handler \'' . $class_name . '\' does not exist, using default \'Kuuzu\\KU\\Session\\File\'', E_USER_NOTICE);

            $class_name = 'Kuuzu\\KU\\Session\\File';
        } elseif (!is_subclass_of($class_name, 'Kuuzu\KU\SessionAbstract')) {
            trigger_error('Session Handler \'' . $class_name . '\' does not extend Kuuzu\\KU\\SessionAbstract, using default \'Kuuzu\\KU\\Session\\File\'', E_USER_NOTICE);

            $class_name = 'Kuuzu\\KU\\Session\\File';
        }

        $obj = new $class_name();

        if (!isset($name)) {
            $name = 'kuuzuid';
        }

        $obj->setName($name);

        return $obj;
    }
}
