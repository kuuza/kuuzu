<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheck_file_uploads {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/file_uploads');
    }

    function pass() {
      return (bool)ini_get('file_uploads');
    }

    function getMessage() {
      return KUUZU::getDef('warning_file_uploads_disabled');
    }
  }
?>
