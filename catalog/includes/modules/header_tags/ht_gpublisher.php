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

  class ht_gpublisher {
    var $code = 'ht_gpublisher';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_header_tags_gpublisher_title');
      $this->description = KUUZU::getDef('module_header_tags_gpublisher_description');

      if ( defined('MODULE_HEADER_TAGS_GPUBLISHER_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GPUBLISHER_STATUS == 'True');
      }
    }

    function execute() {
      global $kuuTemplate;

      $kuuTemplate->addBlock('<link rel="publisher" href="' . HTML::output(MODULE_HEADER_TAGS_GPUBLISHER_ID) . '" />' . PHP_EOL, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_GPUBLISHER_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable G+ Publisher Module',
        'configuration_key' => 'MODULE_HEADER_TAGS_GPUBLISHER_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Add G+ Publisher Link to your shop?  You MUST have a BUSINESS G+ account.  Once installed and configured, don\'t forget to link your G+ page back to your website.<br><br><b>Helper Links:</b><br>http://www.google.com/+/business/<br>http://www.advancessg.com/googles-relpublisher-tag-is-for-all-business-and-brand-websites-not-just-publishers/',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'G+ Publisher Address',
        'configuration_key' => 'MODULE_HEADER_TAGS_GPUBLISHER_ID',
        'configuration_value' => '',
        'configuration_description' => 'Your G+ URL.',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER',
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
      return array('MODULE_HEADER_TAGS_GPUBLISHER_STATUS', 'MODULE_HEADER_TAGS_GPUBLISHER_ID', 'MODULE_HEADER_TAGS_GPUBLISHER_SORT_ORDER');
    }
  }

