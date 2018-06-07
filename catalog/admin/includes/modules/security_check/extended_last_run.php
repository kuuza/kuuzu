<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheck_extended_last_run {
    var $type = 'warning';

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/extended_last_run');
    }

    function pass() {
      global $PHP_SELF;

      $KUUZU_Db = Registry::get('Db');

      if ( $PHP_SELF == 'security_checks.php' ) {
        if ( defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME') ) {
          $KUUZU_Db->save('configuration', [
            'configuration_value' => time(),
          ], [
            'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME'
          ]);
        } else {
          $KUUZU_Db->save('configuration', [
            'configuration_title' => 'Security Check Extended Last Run',
            'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME',
            'configuration_value' => time(),
            'configuration_description' => 'The date and time the last extended security check was performed.',
            'configuration_group_id' => '6',
            'date_added' => 'now()'
          ]);
        }

        return true;
      }

      return defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME') && (MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME > strtotime('-30 days'));
    }

    function getMessage() {
      return '<a href="' . KUUZU::link('security_checks.php') . '">' . KUUZU::getDef('module_security_check_extended_last_run_old') . '</a>';
    }
  }
?>
