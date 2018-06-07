<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  foreach ( $cl_box_groups as &$group ) {
    if ( $group['heading'] == KUUZU::getDef('box_heading_modules') ) {
      $group['apps'][] = array('code' => 'modules_hooks.php',
                               'title' => KUUZU::getDef('modules_admin_menu_modules_hooks'),
                               'link' => KUUZU::link('modules_hooks.php'));

      break;
    }
  }
?>
