<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

use Kuuzu\KU\KUUZU;

$admin_menu['shop']['tools']['action_recorder'] = KUUZU::link('action_recorder.php');
$admin_menu['shop']['tools']['backup'] = KUUZU::link('backup.php');
$admin_menu['shop']['tools']['banner_manager'] = KUUZU::link('banner_manager.php');
$admin_menu['shop']['tools']['cache'] = KUUZU::link('cache.php');
$admin_menu['shop']['tools']['online_update'] = KUUZU::link('online_update.php');
$admin_menu['shop']['tools']['server_info'] = KUUZU::link('server_info.php');

  $cl_box_groups[] = array(
    'heading' => KUUZU::getDef('box_heading_tools'),
    'apps' => array(
      array(
        'code' => FILENAME_DEFINE_LANGUAGE,
        'title' => KUUZU::getDef('box_tools_define_language'),
        'link' => KUUZU::link(FILENAME_DEFINE_LANGUAGE)
      ),
      array(
        'code' => FILENAME_MAIL,
        'title' => KUUZU::getDef('box_tools_mail'),
        'link' => KUUZU::link(FILENAME_MAIL)
      ),
      array(
        'code' => FILENAME_NEWSLETTERS,
        'title' => KUUZU::getDef('box_tools_newsletter_manager'),
        'link' => KUUZU::link(FILENAME_NEWSLETTERS)
      ),
      array(
        'code' => FILENAME_SEC_DIR_PERMISSIONS,
        'title' => KUUZU::getDef('box_tools_sec_dir_permissions'),
        'link' => KUUZU::link(FILENAME_SEC_DIR_PERMISSIONS)
      ),
      array(
        'code' => FILENAME_WHOS_ONLINE,
        'title' => KUUZU::getDef('box_tools_whos_online'),
        'link' => KUUZU::link(FILENAME_WHOS_ONLINE)
      )
    )
  );
?>
