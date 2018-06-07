<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\Sites\Shop;

use Kuuzu\KU\Apps;
use Kuuzu\KU\Cookies;
use Kuuzu\KU\Db;
use Kuuzu\KU\Hooks;
use Kuuzu\KU\HTML;
use Kuuzu\KU\Language;
use Kuuzu\KU\KUUZU;
use Kuuzu\KU\Registry;
use Kuuzu\KU\Session;

class Shop extends \Kuuzu\KU\SitesAbstract
{
    protected function init()
    {
        global $PHP_SELF, $currencies, $messageStack, $kuuTemplate, $breadcrumb;

        $KUUZU_Cookies = new Cookies();
        Registry::set('Cookies', $KUUZU_Cookies);

        try {
            $KUUZU_Db = Db::initialize();
            Registry::set('Db', $KUUZU_Db);
        } catch (\Exception $e) {
            include(KUUZU::getConfig('dir_root') . 'includes/error_documents/maintenance.php');
            exit;
        }

        Registry::set('Hooks', new Hooks());

// set the application parameters
        $Qcfg = $KUUZU_Db->get('configuration', [
            'configuration_key as k',
            'configuration_value as v'
        ]);//, null, null, null, 'configuration'); // TODO add cache when supported by admin

        while ($Qcfg->fetch()) {
            define($Qcfg->value('k'), $Qcfg->value('v'));
        }

// set php_self in the global scope
        $req = parse_url($_SERVER['SCRIPT_NAME']);
        $PHP_SELF = substr($req['path'], strlen(KUUZU::getConfig('http_path', 'Shop')));

        $KUUZU_Session = Session::load();
        Registry::set('Session', $KUUZU_Session);

// start the session
        $KUUZU_Session->start();

        $this->ignored_actions[] = session_name();

        $KUUZU_Language = new Language();
//        $KUUZU_Language->setUseCache(true);
        Registry::set('Language', $KUUZU_Language);

// create the shopping cart
        if (!isset($_SESSION['cart']) || !is_object($_SESSION['cart']) || (get_class($_SESSION['cart']) != 'shoppingCart')) {
            $_SESSION['cart'] = new \shoppingCart();
        }

// include currencies class and create an instance
        $currencies = new \currencies();

// set the language
        if (!isset($_SESSION['language']) || isset($_GET['language'])) {
            if (isset($_GET['language']) && !empty($_GET['language']) && $KUUZU_Language->exists($_GET['language'])) {
                $KUUZU_Language->set($_GET['language']);
            }

            $_SESSION['language'] = $KUUZU_Language->get('code');
        }

// include the language translations
        $KUUZU_Language->loadDefinitions('main');

// Prevent LC_ALL from setting LC_NUMERIC to a locale with 1,0 float/decimal values instead of 1.0 (see bug #634)
        $system_locale_numeric = setlocale(LC_NUMERIC, 0);
        setlocale(LC_ALL, explode(';', KUUZU::getDef('system_locale')));
        setlocale(LC_NUMERIC, $system_locale_numeric);

// currency
        if (!isset($_SESSION['currency']) || isset($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (KUUZU::getDef('language_currency') != $_SESSION['currency']))) {
            if (isset($_GET['currency']) && $currencies->is_set($_GET['currency'])) {
                $_SESSION['currency'] = $_GET['currency'];
            } else {
                $_SESSION['currency'] = ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && $currencies->is_set(KUUZU::getDef('language_currency'))) ? KUUZU::getDef('language_currency') : DEFAULT_CURRENCY;
            }
        }

// navigation history
        if (!isset($_SESSION['navigation']) || !is_object($_SESSION['navigation']) || (get_class($_SESSION['navigation']) != 'navigationHistory')) {
            $_SESSION['navigation'] = new \navigationHistory();
        }

        $_SESSION['navigation']->add_current_page();

        $messageStack = new \messageStack();

        tep_update_whos_online();

        tep_activate_banners();
        tep_expire_banners();

        tep_expire_specials();

        $kuuTemplate = new \kuuTemplate();

        $breadcrumb = new \breadcrumb();

        $breadcrumb->add(KUUZU::getDef('header_title_top'), KUUZU::getConfig('http_server', 'Shop'));
        $breadcrumb->add(KUUZU::getDef('header_title_catalog'), KUUZU::link('index.php'));
    }

    public function setPage()
    {
        if (!empty($_GET)) {
            if (($route = Apps::getRouteDestination()) !== null) {
                $this->route = $route;

                list($vendor_app, $page) = explode('/', $route['destination'], 2);

// get controller class name from namespace
                $page_namespace = explode('\\', $page);
                $page_code = $page_namespace[count($page_namespace)-1];

                if (class_exists('Kuuzu\Apps\\' . $vendor_app . '\\' . $page . '\\' . $page_code)) {
                    $class = 'Kuuzu\Apps\\' . $vendor_app . '\\' . $page . '\\' . $page_code;
                }
            } else {
                $req = basename(array_keys($_GET)[0]);

                if (class_exists('Kuuzu\Sites\\' . $this->code . '\Pages\\' . $req . '\\' . $req)) {
                    $page_code = $req;

                    $class = 'Kuuzu\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;
                }
            }
        }

        if (isset($class)) {
            if (is_subclass_of($class, 'Kuuzu\KU\PagesInterface')) {
                $this->page = new $class($this);

                $this->page->runActions();
            } else {
                trigger_error('Kuuzu\Sites\Shop\Shop::setPage() - ' . $page_code . ': Page does not implement Kuuzu\KU\PagesInterface and cannot be loaded.');
            }
        }
    }

    public static function resolveRoute(array $route, array $routes)
    {
        $result = [];

        foreach ($routes as $vendor_app => $paths) {
            foreach ($paths as $path => $page) {
                $path_array = explode('&', $path);

                if (count($path_array) <= count($route)) {
                    if ($path_array == array_slice($route, 0, count($path_array))) {
                        $result[] = [
                            'path' => $path,
                            'destination' => $vendor_app . '/' . $page,
                            'score' => count($path_array)
                        ];
                    }
                }
            }
        }

        if (!empty($result)) {
            usort($result, function ($a, $b) {
                if ($a['score'] == $b['score']) {
                    return 0;
                }

                return ($a['score'] < $b['score']) ? 1 : -1; // sort highest to lowest
            });

            return $result[0];
        }
    }
}
