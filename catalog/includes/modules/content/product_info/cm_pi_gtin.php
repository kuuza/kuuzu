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

  class cm_pi_gtin {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = KUUZU::getDef('module_content_product_info_gtin_title');
      $this->description = KUUZU::getDef('module_content_product_info_gtin_description');
      $this->description .= '<div class="secWarning">' . KUUZU::getDef('module_content_bootstrap_row_description') . '</div>';

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS == 'True');
      }
    }

    function execute() {
      global $kuuTemplate;

      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH;

      $KUUZU_Db = Registry::get('Db');

      $Qgtin = $KUUZU_Db->prepare('select products_gtin from :table_products where products_id = :products_id');
      $Qgtin->bindInt(':products_id', $_GET['products_id']);
      $Qgtin->execute();

      if ($Qgtin->fetch() !== false) {
        $gtin = $Qgtin->value('products_gtin');

        if (!empty($gtin)) {
          $gtin = substr($gtin, 0 - MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH);

          if (!empty($gtin)) {
            $gtin = HTML::outputProtected($gtin);

            ob_start();
            include('includes/modules/content/' . $this->group . '/templates/gtin.php');
            $template = ob_get_clean();

            $kuuTemplate->addContent($template, $this->group);
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable GTIN Module',
        'configuration_key' => 'MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Should this module be shown on the product info page?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Content Width',
        'configuration_key' => 'MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH',
        'configuration_value' => '6',
        'configuration_description' => 'What width container should the content be shown in?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Length of GTIN',
        'configuration_key' => 'MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH',
        'configuration_value' => '13',
        'configuration_description' => 'Length of GTIN. 14 (Industry Standard), 13 (eg ISBN codes and EAN UCC-13), 12 (UPC), 8 (EAN UCC-8)',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'set_function' => 'tep_cfg_select_option(array(\'14\', \'13\', \'12\', \'8\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER',
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
      return array('MODULE_CONTENT_PRODUCT_INFO_GTIN_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH', 'MODULE_CONTENT_PRODUCT_INFO_GTIN_SORT_ORDER');
    }
  }
