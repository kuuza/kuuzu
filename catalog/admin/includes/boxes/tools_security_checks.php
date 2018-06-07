<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == KUUZU::getDef('box_heading_tools') ) {
      $group['apps'][] = array('code' => 'security_checks.php',
                               'title' => KUUZU::getDef('modules_admin_menu_tools_security_checks'),
                               'link' => KUUZU::link('security_checks.php'));

      break;
    }
  }
?>
