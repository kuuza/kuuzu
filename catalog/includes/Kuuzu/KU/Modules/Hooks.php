<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Modules;

use Kuuzu\KU\Apps;

class Hooks extends \Kuuzu\KU\ModulesAbstract
{
    public function getInfo($app, $key, $data)
    {
        $result = [];

        foreach ($data as $code => $class) {
            $class = $this->ns . $app . '\\' . $class;

            if (is_subclass_of($class, 'Kuuzu\KU\Modules\\' . $this->code . 'Interface')) {
                $result[$app . '\\' . $key . '\\' . $code] = $class;
            }
        }

        return $result;
    }

    public function getClass($module)
    {
        if (strpos($module, '/') === false) { // TODO core hook compatibility; to remove
            return $module;
        }

        list($vendor, $app, $group, $code) = explode('\\', $module, 4);

        $info = Apps::getInfo($vendor . '\\' . $app);

        if (isset($info['modules'][$this->code][$group][$code])) {
            return $this->ns . $vendor . '\\' . $app . '\\' . $info['modules'][$this->code][$group][$code];
        }
    }

    public function filter($modules, $filter)
    {
        $result = [];

        foreach ($modules as $key => $data) {
            if (($key == $filter['site'] . '/' . $filter['group']) && isset($data[$filter['hook']])) {
                $result[$key] = $data;
            }
        }

        return $result;
    }
}
