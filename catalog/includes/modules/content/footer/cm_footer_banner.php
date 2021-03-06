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

  class cm_footer_banner {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = KUUZU::getDef('module_content_footer_banner_title');
      $this->description = KUUZU::getDef('module_content_footer_banner_description');
      $this->description .= '<div class="secWarning">' . KUUZU::getDef('module_content_bootstrap_row_description') . '</div>';

      if ( defined('MODULE_CONTENT_FOOTER_BANNER_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_FOOTER_BANNER_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_FOOTER_BANNER_STATUS == 'True');
      }
    }

    function execute() {
      global $kuuTemplate;

      if (!empty(MODULE_CONTENT_FOOTER_BANNER_GROUP)) {
        $content_width = (int)MODULE_CONTENT_FOOTER_BANNER_CONTENT_WIDTH;

        if ($banner = tep_banner_exists('dynamic', MODULE_CONTENT_FOOTER_BANNER_GROUP)) {
          ob_start();
          include('includes/modules/content/' . $this->group . '/templates/banner.php');
          $template = ob_get_clean();

          $kuuTemplate->addContent($template, $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_FOOTER_BANNER_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Banner Footer Module',
        'configuration_key' => 'MODULE_CONTENT_FOOTER_BANNER_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to enable the Banner content module?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Banner Group',
        'configuration_key' => 'MODULE_CONTENT_FOOTER_BANNER_GROUP',
        'configuration_value' => '',
        'configuration_description' => 'Which group should the banner be displayed from?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'use_function' => 'cm_footer_banner::showBannerGroupSelection',
        'set_function' => 'cm_footer_banner::getBannerGroupSelection(',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Content Width',
        'configuration_key' => 'MODULE_CONTENT_FOOTER_BANNER_CONTENT_WIDTH',
        'configuration_value' => '3',
        'configuration_description' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_CONTENT_FOOTER_BANNER_SORT_ORDER',
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
      return array('MODULE_CONTENT_FOOTER_BANNER_STATUS', 'MODULE_CONTENT_FOOTER_BANNER_GROUP', 'MODULE_CONTENT_FOOTER_BANNER_CONTENT_WIDTH', 'MODULE_CONTENT_FOOTER_BANNER_SORT_ORDER');
    }

    public static function showBannerGroupSelection($value) {
      return empty($value) ? '--None--' : $value;
    }

    public static function getBannerGroupSelection($value, $key = '') {
      $KUUZU_Db = Registry::get('Db');

      $name = !empty($key) ? 'configuration[' . $key . ']' : 'configuration_value';

      $groups = [
        [
          'id' => '',
          'text' => '--None--'
        ]
      ];

      $Qgroups = $KUUZU_Db->query('select distinct banners_group from :table_banners order by banners_group');

      while ($Qgroups->fetch()) {
        $groups[] = [
          'id' => $Qgroups->value('banners_group'),
          'text' => $Qgroups->value('banners_group')
        ];
      }

      return HTML::selectField($name, $groups, $value);
    }
  }
