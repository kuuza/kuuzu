<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class securityCheckExtended_mysql_utf8 {
    var $type = 'warning';
    var $has_doc = true;

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/extended/mysql_utf8');

      $this->title = KUUZU::getDef('module_security_check_extended_mysql_utf8_title');
    }

    function pass() {
      $KUUZU_Db = Registry::get('Db');

      $Qcheck = $KUUZU_Db->query('show table status');

      if ($Qcheck->fetch() !== false) {
        do {
          if ($Qcheck->hasValue('Collation') && ($Qcheck->value('Collation') != 'utf8_unicode_ci')) {
            return false;
          }
        } while ($Qcheck->fetch());
      }

      return true;
    }

    function getMessage() {
      return '<a href="' . KUUZU::link('database_tables.php') . '">' . KUUZU::getDef('module_security_check_extended_mysql_utf8_error') . '</a>';
    }
  }
?>
