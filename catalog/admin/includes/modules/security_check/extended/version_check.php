<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Cache;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheckExtended_version_check {
    var $type = 'warning';
    var $has_doc = true;

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/extended/version_check');

      $this->title = KUUZU::getDef('module_security_check_extended_version_check_title');
    }

    function pass() {
      $VersionCache = new Cache('core_version_check');

      return $VersionCache->exists() && ($VersionCache->getTime() > strtotime('-30 days'));
    }

    function getMessage() {
      return '<a href="' . KUUZU::link('online_update.php') . '">' . KUUZU::getDef('module_security_check_extended_version_check_error') . '</a>';
    }
  }
?>
