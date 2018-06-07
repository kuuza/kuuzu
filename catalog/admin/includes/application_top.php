<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Apps;
  use Kuuzu\KU\HTTP;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  define('KUUZU_BASE_DIR', realpath(__DIR__ . '/../../includes/Kuuzu/') . '/');

// Set the level of error reporting
  error_reporting(E_ALL & ~E_DEPRECATED);

  require(KUUZU_BASE_DIR . 'KU/KUUZU.php');
  spl_autoload_register('Kuuzu\KU\KUUZU::autoload');

  KUUZU::initialize();

  if (PHP_VERSION_ID < 70000) {
    include(KUUZU::getConfig('dir_root', 'Shop') . 'includes/third_party/random_compat/random.php');
  }

  require('includes/filenames.php');
  require('includes/functions/general.php');
  require('includes/classes/logger.php');
  require('includes/classes/shopping_cart.php');
  require('includes/classes/table_block.php');
  require('includes/classes/box.php');
  require('includes/classes/object_info.php');
  require('includes/classes/upload.php');
  require('includes/classes/action_recorder.php');
  require('includes/classes/cfg_modules.php');

  require(KUUZU::getConfig('dir_root', 'Shop') . 'includes/classes/kuu_template.php');

  KUUZU::loadSite('Admin');

  if ((HTTP::getRequestType() === 'NONSSL') && ($_SERVER['REQUEST_METHOD'] === 'GET') && (parse_url(KUUZU::getConfig('http_server'), PHP_URL_SCHEME) == 'https')) {
    $url_req = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    HTTP::redirect($url_req, 301);
  }

  $KUUZU_Db = Registry::get('Db');
  $KUUZU_Hooks = Registry::get('Hooks');
  $KUUZU_Language = Registry::get('Language');
  $KUUZU_MessageStack = Registry::get('MessageStack');

// calculate category path
  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } else {
    $cPath = '';
  }

  if (tep_not_null($cPath)) {
    $cPath_array = tep_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $cPath_array = [];
    $current_category_id = 0;
  }

  $admin_menu = [];
  $cl_box_groups = array();
  $cl_apps_groups = array();

  if (isset($_SESSION['admin'])) {
    if ($dir = @dir(KUUZU::getConfig('dir_root') . 'includes/boxes')) {
      $files = array();

      while ($file = $dir->read()) {
        if (!is_dir($dir->path . '/' . $file)) {
          if (substr($file, strrpos($file, '.')) == '.php') {
            $files[] = $file;
          }
        }
      }

      $dir->close();

      natcasesort($files);

      foreach ( $files as $file ) {
        if ($KUUZU_Language->definitionsExist('modules/boxes/' . pathinfo($file, PATHINFO_FILENAME))) {
          $KUUZU_Language->loadDefinitions('modules/boxes/'. pathinfo($file, PATHINFO_FILENAME));
        }

        include($dir->path . '/' . $file);
      }
    }

    foreach (Apps::getModules('AdminMenu') as $m) {
      $appmenu = call_user_func([$m, 'execute']);

      if (is_array($appmenu) && !empty($appmenu)) {
        $cl_apps_groups[] = $appmenu;
      }
    }
  }

  usort($cl_box_groups, function ($a, $b) {
    return strcasecmp($a['heading'], $b['heading']);
  });

  foreach ( $cl_box_groups as &$group ) {
    usort($group['apps'], function ($a, $b) {
      return strcasecmp($a['title'], $b['title']);
    });
  }

  unset($group); // unset reference variable
?>
