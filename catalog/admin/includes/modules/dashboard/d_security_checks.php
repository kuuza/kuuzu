<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class d_security_checks {
    var $code = 'd_security_checks';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function d_security_checks() {
      $this->title = KUUZU::getDef('module_admin_dashboard_security_checks_title');
      $this->description = KUUZU::getDef('module_admin_dashboard_security_checks_description');

      if ( defined('MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_STATUS == 'True');
      }
    }

    function getOutput() {
      global $PHP_SELF;

      $KUUZU_MessageStack = Registry::get('MessageStack');

      $secCheck_types = array('info', 'warning', 'error');

      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      $secmodules_array = array();
      if ($secdir = @dir(KUUZU::getConfig('dir_root') . 'includes/modules/security_check/')) {
        while ($file = $secdir->read()) {
          if (!is_dir(KUUZU::getConfig('dir_root') . 'includes/modules/security_check/' . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $secmodules_array[] = $file;
            }
          }
        }
        sort($secmodules_array);
        $secdir->close();
      }

      foreach ($secmodules_array as $secmodule) {
        include(KUUZU::getConfig('dir_root') . 'includes/modules/security_check/' . $secmodule);

        $secclass = 'securityCheck_' . substr($secmodule, 0, strrpos($secmodule, '.'));
        if (class_exists($secclass)) {
          $secCheck = new $secclass;

          if ( !$secCheck->pass() ) {
            if (!in_array($secCheck->type, $secCheck_types)) {
              $secCheck->type = 'info';
            }

            $KUUZU_MessageStack->add($secCheck->getMessage(), $secCheck->type, 'securityCheckModule');
          }
        }
      }

      if (!$KUUZU_MessageStack->exists('securityCheckModule')) {
        $KUUZU_MessageStack->add(KUUZU::getDef('module_admin_dashboard_security_checks_success'), 'success', 'securityCheckModule');
      }

      $output = $KUUZU_MessageStack->get('securityCheckModule');

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Security Checks Module',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to run the security checks for this installation?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_SORT_ORDER',
        'configuration_value' => '0',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'date_added' => 'now()'
      ]);
    }

    function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    function keys() {
      return array('MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_STATUS', 'MODULE_ADMIN_DASHBOARD_SECURITY_CHECKS_SORT_ORDER');
    }
  }
?>
