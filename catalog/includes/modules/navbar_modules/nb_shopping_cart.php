<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class nb_shopping_cart {
    var $code = 'nb_shopping_cart';
    var $group = 'navbar_modules_right';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_navbar_shopping_cart_title');
      $this->description = KUUZU::getDef('module_navbar_shopping_cart_description');

      if ( defined('MODULE_NAVBAR_SHOPPING_CART_STATUS') ) {
        $this->sort_order = MODULE_NAVBAR_SHOPPING_CART_SORT_ORDER;
        $this->enabled = (MODULE_NAVBAR_SHOPPING_CART_STATUS == 'True');

        switch (MODULE_NAVBAR_SHOPPING_CART_CONTENT_PLACEMENT) {
          case 'Home':
          $this->group = 'navbar_modules_home';
          break;
          case 'Left':
          $this->group = 'navbar_modules_left';
          break;
          case 'Right':
          $this->group = 'navbar_modules_right';
          break;
        }
      }
    }

    function getOutput() {
      global $kuuTemplate, $currencies;

      ob_start();
      require('includes/modules/navbar_modules/templates/shopping_cart.php');
      $data = ob_get_clean();

      $kuuTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_NAVBAR_SHOPPING_CART_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Shopping Cart Module',
        'configuration_key' => 'MODULE_NAVBAR_SHOPPING_CART_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to add the module to your Navbar?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Content Placement',
        'configuration_key' => 'MODULE_NAVBAR_SHOPPING_CART_CONTENT_PLACEMENT',
        'configuration_value' => 'Right',
        'configuration_description' => 'Should the module be loaded in the Left or Right or the Home area of the Navbar?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'Left\', \'Right\', \'Home\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_NAVBAR_SHOPPING_CART_SORT_ORDER',
        'configuration_value' => '550',
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
      return array('MODULE_NAVBAR_SHOPPING_CART_STATUS', 'MODULE_NAVBAR_SHOPPING_CART_CONTENT_PLACEMENT', 'MODULE_NAVBAR_SHOPPING_CART_SORT_ORDER');
    }
  }
