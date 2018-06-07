<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

use Kuuzu\KU\KUUZU;
use Kuuzu\KU\Registry;

$KUUZU_Language = Registry::get('Language');

$admin_menu['shop']['configuration']['administrators'] = KUUZU::link('administrators.php');

$Qgroups = $KUUZU_Db->get('configuration_group', [
  'configuration_group_id as cgID',
  'configuration_group_title as cgTitle'
], [
  'visible' => '1'
], 'sort_order');

while ($Qgroups->fetch()) {
  $KUUZU_Language->injectDefinitions([
    'admin_menu_shop_configuration_g' . $Qgroups->valueInt('cgID') => $Qgroups->value('cgTitle')
  ], 'global');

  $admin_menu['shop']['configuration']['g' . $Qgroups->valueInt('cgID')] = KUUZU::link('configuration.php', 'gID=' . $Qgroups->valueInt('cgID'));
}

  $cl_box_groups[] = [
    'heading' => KUUZU::getDef('box_heading_configuration'),
    'apps' => [
      [
        'code' => FILENAME_STORE_LOGO,
        'title' => KUUZU::getDef('box_configuration_store_logo'),
        'link' => KUUZU::link(FILENAME_STORE_LOGO)
      ]
    ]
  ];
?>
