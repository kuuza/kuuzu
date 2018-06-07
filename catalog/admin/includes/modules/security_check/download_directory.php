<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheck_download_directory {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/download_directory');
    }

    function pass() {
      if (DOWNLOAD_ENABLED != 'true') {
        return true;
      }

      return is_dir(KUUZU::getConfig('dir_root', 'Shop') . 'download/');
    }

    function getMessage() {
      return KUUZU::getDef('warning_download_directory_non_existent', [
        'download_path' => KUUZU::getConfig('dir_root', 'Shop') . 'download/'
      ]);
    }
  }
?>
