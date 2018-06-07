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

  class securityCheck_session_storage {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/session_storage');
    }

    function pass() {
      return ((KUUZU::getConfig('store_sessions') != '') || FileSystem::isWritable(session_save_path()));
    }

    function getMessage() {
      if (KUUZU::getConfig('store_sessions') == '') {
        if (!is_dir(session_save_path())) {
          return KUUZU::getDef('warning_session_directory_non_existent', [
            'session_path' => session_save_path()
          ]);
        } elseif (!FileSystem::isWritable(session_save_path())) {
          return KUUZU::getDef('warning_session_directory_not_writeable', [
            'session_path' => session_save_path()
          ]);
        }
      }
    }
  }
?>
