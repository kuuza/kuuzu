<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

use Kuuzu\KU\KUUZU;
use Kuuzu\KU\Registry;

class Apps
{
    public static function getAll()
    {
        $result = [];

        $apps_directory = KUUZU::BASE_DIR . 'Apps';

        if ($vdir = new \DirectoryIterator($apps_directory)) {
            foreach ($vdir as $vendor) {
                if (!$vendor->isDot() && $vendor->isDir()) {
                    if ($adir = new \DirectoryIterator($vendor->getPath() . '/' . $vendor->getFilename())) {
                        foreach ($adir as $app) {
                            if (!$app->isDot() && $app->isDir() && static::exists($vendor->getFilename() . '\\' . $app->getFilename())) {
                                if (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false) {
                                    $result[] = $json;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public static function getModules($type, $filter_vendor_app = null, $filter = null)
    {
        $result = [];

        if (!Registry::exists('ModuleType' . $type)) {
            $class = 'Kuuzu\KU\Modules\\' . $type;

            if (!class_exists($class)) {
                trigger_error('Kuuzu\KU\Apps::getModules(): ' . $type . ' module class not found in Kuuzu\KU\Modules\\');

                return $result;
            }

            Registry::set('ModuleType' . $type, new $class());
        }

        $KUUZU_Type = Registry::get('ModuleType' . $type);

        $filter_vendor = $filter_app = null;

        if (isset($filter_vendor_app)) {
            if (strpos($filter_vendor_app, '\\') !== false) {
                list($filter_vendor, $filter_app) = explode('\\', $filter_vendor_app, 2);
            } else {
                $filter_vendor = $filter_vendor_app;
            }
        }

        $vendor_directory = KUUZU::BASE_DIR . 'Apps';

        if (is_dir($vendor_directory)) {
            if ($vdir = new \DirectoryIterator($vendor_directory)) {
                foreach ($vdir as $vendor) {
                    if (!$vendor->isDot() && $vendor->isDir() && (!isset($filter_vendor) || ($vendor->getFilename() == $filter_vendor))) {
                        if ($adir = new \DirectoryIterator($vendor->getPath() . '/' . $vendor->getFilename())) {
                            foreach ($adir as $app) {
                                if (!$app->isDot() && $app->isDir() && (!isset($filter_app) || ($app->getFilename() == $filter_app)) && static::exists($vendor->getFilename() . '\\' . $app->getFilename()) && (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false)) {
                                    if (isset($json['modules'][$type])) {
                                        $modules = $json['modules'][$type];

                                        if (isset($filter)) {
                                            $modules = $KUUZU_Type->filter($modules, $filter);
                                        }

                                        foreach ($modules as $key => $data) {
                                            $result = array_merge($result, $KUUZU_Type->getInfo($vendor->getFilename() . '\\' . $app->getFilename(), $key, $data));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public static function exists($app)
    {
        if (strpos($app, '\\') !== false) {
            list($vendor, $app) = explode('\\', $app, 2);

            if (class_exists('Kuuzu\Apps\\' . $vendor . '\\' . $app . '\\' . $app)) {
                if (is_subclass_of('Kuuzu\Apps\\' . $vendor . '\\' . $app . '\\' . $app, 'Kuuzu\KU\AppAbstract')) {
                    return true;
                } else {
                    trigger_error('Kuuzu\KU\Apps::exists(): ' . $vendor . '\\' . $app . ' - App is not a subclass of Kuuzu\KU\AppAbstract and cannot be loaded.');
                }
            }
        } else {
            trigger_error('Kuuzu\KU\Apps::exists(): ' . $app . ' - Invalid format, must be: Vendor\App.');
        }

        return false;
    }

    public static function getModuleClass($module, $type)
    {
        if (!Registry::exists('ModuleType' . $type)) {
            $class = 'Kuuzu\KU\Modules\\' . $type;

            if (!class_exists($class)) {
                trigger_error('Kuuzu\KU\Apps::getModuleClass(): ' . $type . ' module class not found in Kuuzu\KU\Modules\\');

                return false;
            }

            Registry::set('ModuleType' . $type, new $class());
        }

        $KUUZU_Type = Registry::get('ModuleType' . $type);

        return $KUUZU_Type->getClass($module);
    }

    public static function getInfo($app)
    {
        if (strpos($app, '\\') !== false) {
            list($vendor, $app) = explode('\\', $app, 2);

            $metafile = KUUZU::BASE_DIR . 'Apps/' . basename($vendor) . '/' . basename($app) . '/kuuzu.json';

            if (is_file($metafile) && (($json = json_decode(file_get_contents($metafile), true)) !== null)) {
                return $json;
            }

            trigger_error('Kuuzu\KU\Apps::getInfo(): ' . $vendor . '\\' . $app . ' - Could not read App information in ' . $metafile . '.');
        } else {
            trigger_error('Kuuzu\KU\Apps::getInfo(): ' . $app . ' - Invalid format, must be: Vendor\App.');
        }

        return false;
    }

    public static function getRouteDestination($route = null, $filter_vendor_app = null)
    {
        if (empty($route)) {
            $route = array_keys($_GET);
        }

        $result = $routes = [];

        if (empty($route)) {
            return $result;
        }

        $filter_vendor = $filter_app = null;

        if (isset($filter_vendor_app)) {
            if (strpos($filter_vendor_app, '\\') !== false) {
                list($filter_vendor, $filter_app) = explode('\\', $filter_vendor_app, 2);
            } else {
                $filter_vendor = $filter_vendor_app;
            }
        }

        $vendor_directory = KUUZU::BASE_DIR . 'Apps';

        if (is_dir($vendor_directory)) {
            if ($vdir = new \DirectoryIterator($vendor_directory)) {
                foreach ($vdir as $vendor) {
                    if (!$vendor->isDot() && $vendor->isDir() && (!isset($filter_vendor) || ($vendor->getFilename() == $filter_vendor))) {
                        if ($adir = new \DirectoryIterator($vendor->getPath() . '/' . $vendor->getFilename())) {
                            foreach ($adir as $app) {
                                if (!$app->isDot() && $app->isDir() && (!isset($filter_app) || ($app->getFilename() == $filter_app)) && static::exists($vendor->getFilename() . '\\' . $app->getFilename()) && (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false)) {
                                    if (isset($json['routes'][KUUZU::getSite()])) {
                                        $routes[$json['vendor'] . '\\' . $json['app']] = $json['routes'][KUUZU::getSite()];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return call_user_func([
            'Kuuzu\Sites\\' . KUUZU::getSite() . '\\' . KUUZU::getSite(),
            'resolveRoute'
        ], $route, $routes);
    }
}
