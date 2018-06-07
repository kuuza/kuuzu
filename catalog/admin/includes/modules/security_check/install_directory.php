<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheck_install_directory {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/install_directory');
    }

    function pass() {
      return !is_dir(KUUZU::getConfig('dir_root', 'Shop') . 'install');
    }

    function getMessage() {
      return KUUZU::getDef('warning_install_directory_exists', [
        'install_path' => KUUZU::getConfig('dir_root', 'Shop') . 'install'
      ]);
    }
  }
?>
