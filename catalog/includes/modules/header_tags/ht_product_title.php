<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class ht_product_title {
    var $code = 'ht_product_title';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_header_tags_product_title_title');
      $this->description = KUUZU::getDef('module_header_tags_product_title_description');

      if ( defined('MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_PRODUCT_TITLE_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $kuuTemplate;

      $KUUZU_Db = Registry::get('Db');
      $KUUZU_Language = Registry::get('Language');

      if ( (basename($PHP_SELF) == 'product_info.php') || (basename($PHP_SELF) == 'product_reviews.php') ) {
        if (isset($_GET['products_id'])) {
          $Qproduct = $KUUZU_Db->prepare('select pd.products_name, pd.products_seo_title from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
          $Qproduct->bindInt(':products_id', $_GET['products_id']);
          $Qproduct->bindInt(':language_id', $KUUZU_Language->getId());
          $Qproduct->execute();

          if ($Qproduct->fetch() !== false) {
            if ( tep_not_null($Qproduct->value('products_seo_title')) && (MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE == 'True') ) {
              $kuuTemplate->setTitle($Qproduct->value('products_seo_title') . KUUZU::getDef('module_header_tags_product_seo_separator') . $kuuTemplate->getTitle());
            }
            else {
              $kuuTemplate->setTitle($Qproduct->value('products_name') . KUUZU::getDef('module_header_tags_product_seo_separator') . $kuuTemplate->getTitle());
            }
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Product Title Module',
        'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to allow product titles to be added to the page title?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'SEO Title Override?',
        'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to allow product titles to be over-ridden by your SEO Titles (if set)?',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'SEO Breadcrumb Override?',
        'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_BREADCRUMB_OVERRIDE',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to allow product names in the breadcrumb to be over-ridden by your SEO Titles (if set)?',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SORT_ORDER',
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
      return array('MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS', 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_TITLE_OVERRIDE', 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SEO_BREADCRUMB_OVERRIDE', 'MODULE_HEADER_TAGS_PRODUCT_TITLE_SORT_ORDER');
    }
  }
?>
