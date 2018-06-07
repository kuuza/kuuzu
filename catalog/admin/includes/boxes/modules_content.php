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
      $group['apps'][] = array('code' => 'modules_content.php',
                               'title' => KUUZU::getDef('modules_admin_menu_modules_content'),
                               'link' => KUUZU::link('modules_content.php'));

      break;
    }
  }
?>
