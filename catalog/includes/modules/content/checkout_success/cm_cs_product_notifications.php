<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class cm_cs_product_notifications {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = KUUZU::getDef('module_content_checkout_success_product_notifications_title');
      $this->description = KUUZU::getDef('module_content_checkout_success_product_notifications_description');

      if ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS == 'True');
      }
    }

    function execute() {
      global $kuuTemplate, $order_id;

      $KUUZU_Db = Registry::get('Db');

      if ( isset($_SESSION['customer_id']) ) {
        $Qglobal = $KUUZU_Db->get('customers_info', 'global_product_notifications', ['customers_info_id' => $_SESSION['customer_id']]);

        if ( $Qglobal->valueInt('global_product_notifications') !== 1 ) {
          if ( isset($_GET['action']) && ($_GET['action'] == 'update') ) {
            if ( isset($_POST['notify']) && is_array($_POST['notify']) && !empty($_POST['notify']) ) {
              $notify = array_unique($_POST['notify']);

              foreach ( $notify as $n ) {
                if ( is_numeric($n) && ($n > 0) ) {
                  $Qcheck = $KUUZU_Db->get('products_notifications', 'products_id', ['products_id' => (int)$n, 'customers_id' => $_SESSION['customer_id']], null, 1);

                  if ( $Qcheck->fetch() === false ) {
                    $KUUZU_Db->save('products_notifications', [
                      'products_id' => (int)$n,
                      'customers_id' => $_SESSION['customer_id'],
                      'date_added' => 'now()'
                    ]);
                  }
                }
              }
            }
          }

          $products_displayed = array();

          $Qproducts = $KUUZU_Db->get('orders_products', ['products_id', 'products_name'], ['orders_id' => $order_id], 'products_name');

          while ($Qproducts->fetch()) {
            if ( !isset($products_displayed[$Qproducts->valueInt('products_id')]) ) {
              $products_displayed[$Qproducts->valueInt('products_id')]  = '<div class="checkbox"><label>' . HTML::checkboxField('notify[]', $Qproducts->valueInt('products_id')) . ' ' . $Qproducts->value('products_name') . '</label></div>';
            }
          }

          $products_notifications = implode('', $products_displayed);

          ob_start();
          include('includes/modules/content/' . $this->group . '/templates/product_notifications.php');
          $template = ob_get_clean();

          $kuuTemplate->addContent($template, $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Product Notifications Module',
        'configuration_key' => 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Should the product notifications block be shown on the checkout success page?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER',
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
      return array('MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_STATUS','MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_SORT_ORDER');
    }
  }
?>
