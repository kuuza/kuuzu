<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

// set the level of error reporting
  error_reporting(E_ALL & ~E_DEPRECATED);

  define('KUUZU_BASE_DIR', realpath(__DIR__ . '/../../includes/') . '/Kuuzu/');

  require(KUUZU_BASE_DIR . 'KU/KUUZU.php');
  spl_autoload_register('Kuuzu\KU\KUUZU::autoload');

  KUUZU::initialize();
?>
