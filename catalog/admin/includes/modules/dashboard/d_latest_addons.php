<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Cache;
  use Kuuzu\KU\DateTime;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\HTTP;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class d_latest_addons {
    var $code = 'd_latest_addons';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function d_latest_addons() {
      $this->title = KUUZU::getDef('module_admin_dashboard_latest_addons_title');
      $this->description = KUUZU::getDef('module_admin_dashboard_latest_addons_description');

      if ( defined('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS') ) {
        $this->sort_order = MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER;
        $this->enabled = (MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS == 'True');
      }
    }

    function getOutput() {
      $entries = [];

      $addonsCache = new Cache('kuuzu_website-addons-latest5');

      if ($addonsCache->exists(360)) {
        $entries = $addonsCache->get();
      } else {
        $response = HTTP::getResponse(['url' => 'https://kuuzu.org/index.php?RPC&GetLatestAddons']);

        if (!empty($response)) {
          $response = json_decode($response, true);

          if (is_array($response) && (count($response) === 5)) {
            $entries = $response;
          }
        }

        $addonsCache->save($entries);
      }

      $output = '<table class="table table-hover">
                   <thead>
                     <tr class="info">
                       <th>' . KUUZU::getDef('module_admin_dashboard_latest_addons_title') . '</th>
                       <th class="text-right">' . KUUZU::getDef('module_admin_dashboard_latest_addons_date') . '</th>
                     </tr>
                   </thead>
                   <tbody>';

      if (is_array($entries) && (count($entries) === 5)) {
        foreach ($entries as $item) {
          $output .= '    <tr>
                            <td><a href="' . HTML::outputProtected($item['link']) . '" target="_blank">' . HTML::outputProtected($item['title']) . '</a></td>
                            <td class="text-right" style="white-space: nowrap;">' . HTML::outputProtected(DateTime::toShort($item['date'])) . '</td>
                          </tr>';
        }
      } else {
        $output .= '    <tr>
                          <td colspan="2">' . KUUZU::getDef('module_admin_dashboard_latest_addons_feed_error') . '</td>
                        </tr>';
      }

      $output .= '    <tr>
                        <td class="text-right" colspan="2"><a href="https://kuuzu.org/apps" target="_blank" title="' . HTML::outputProtected(KUUZU::getDef('module_admin_dashboard_latest_addons_icon_site')) . '"><span class="fa fa-fw fa-home"></span></a></td>
                      </tr>
                    </tbody>
                  </table>';

      return $output;
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Latest Add-Ons Module',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to show the latest Kuuzu Add-Ons on the dashboard?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER',
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
      return array('MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_STATUS', 'MODULE_ADMIN_DASHBOARD_LATEST_ADDONS_SORT_ORDER');
    }
  }
?>
