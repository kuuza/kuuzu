<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\FileSystem;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheck_config_file_catalog {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/config_file_catalog');
    }

    function pass() {
      return !FileSystem::isWritable(KUUZU::getConfig('dir_root', 'Shop') . 'includes/configure.php');
    }

    function getMessage() {
      return KUUZU::getDef('warning_config_file_writeable', [
        'configure_file_path' => KUUZU::getConfig('dir_root', 'Shop') . 'includes/configure.php'
      ]);
    }
  }
?>
