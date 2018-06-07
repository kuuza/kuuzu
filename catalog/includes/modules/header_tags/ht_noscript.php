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

  class ht_noscript {
    var $code = 'ht_noscript';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_header_tags_noscript_title');
      $this->description = KUUZU::getDef('module_header_tags_noscript_description');

      if ( defined('MODULE_HEADER_TAGS_NOSCRIPT_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_NOSCRIPT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_NOSCRIPT_STATUS == 'True');
      }
    }

    function execute() {
      global $kuuTemplate;

      $kuuTemplate->addBlock('<noscript><div class="no-script"><div class="no-script-inner">' . HTML::output(KUUZU::getDef('module_header_tags_noscript_text')) . '</div></div></noscript>', $this->group);
      $kuuTemplate->addBlock('<style>.no-script { border: 1px solid #ddd; border-width: 0 0 1px; background: #ffff90; font: 14px verdana; line-height: 2; text-align: center; color: #2f2f2f; } .no-script .no-script-inner { margin: 0 auto; padding: 5px; } .no-script p { margin: 0; }</style>', $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_NOSCRIPT_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable No Script Module',
        'configuration_key' => 'MODULE_HEADER_TAGS_NOSCRIPT_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Add message for people with .js turned off?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_HEADER_TAGS_NOSCRIPT_SORT_ORDER',
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
      return array('MODULE_HEADER_TAGS_NOSCRIPT_STATUS', 'MODULE_HEADER_TAGS_NOSCRIPT_SORT_ORDER');
    }
  }
?>
