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

  class sb_twitter {
    var $code = 'sb_twitter';
    var $title;
    var $description;
    var $sort_order;
    var $icon = 'twitter.png';
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_social_bookmarks_twitter_title');
      $this->public_title = KUUZU::getDef('module_social_bookmarks_twitter_public_title');
      $this->description = KUUZU::getDef('module_social_bookmarks_twitter_description');

      if ( defined('MODULE_SOCIAL_BOOKMARKS_TWITTER_STATUS') ) {
        $this->sort_order = MODULE_SOCIAL_BOOKMARKS_TWITTER_SORT_ORDER;
        $this->enabled = (MODULE_SOCIAL_BOOKMARKS_TWITTER_STATUS == 'True');
      }
    }

    function getOutput() {
      return '<a href="http://twitter.com/home?status=' . urlencode(KUUZU::link('product_info.php', 'products_id=' . $_GET['products_id'], false)) . '" target="_blank"><img src="' . KUUZU::linkImage('social_bookmarks/' . $this->icon) . '" border="0" title="' . HTML::outputProtected($this->public_title) . '" alt="' . HTML::outputProtected($this->public_title) . '" /></a>';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function getIcon() {
      return $this->icon;
    }

    function getPublicTitle() {
      return $this->public_title;
    }

    function check() {
      return defined('MODULE_SOCIAL_BOOKMARKS_TWITTER_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Twitter Module',
        'configuration_key' => 'MODULE_SOCIAL_BOOKMARKS_TWITTER_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to allow products to be shared through Twitter?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_SOCIAL_BOOKMARKS_TWITTER_SORT_ORDER',
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
      return array('MODULE_SOCIAL_BOOKMARKS_TWITTER_STATUS', 'MODULE_SOCIAL_BOOKMARKS_TWITTER_SORT_ORDER');
    }
  }
?>