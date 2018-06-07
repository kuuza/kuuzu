<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Apps;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  require('includes/application_top.php');

  $Qcheck = $KUUZU_Db->get('configuration', 'configuration_value', ['configuration_key' => 'MODULE_CONTENT_INSTALLED'], null, 1);
  if ($Qcheck->fetch() === false) {
    $KUUZU_Db->save('configuration', [
      'configuration_title' => 'Installed Modules',
      'configuration_key' => 'MODULE_CONTENT_INSTALLED',
      'configuration_value' => '',
      'configuration_description' => 'This is automatically updated. No need to edit.',
      'configuration_group_id' => '6',
      'sort_order' => '0',
      'date_added' => 'now()'
    ]);
    define('MODULE_CONTENT_INSTALLED', '');
  }

  $modules_installed = (tep_not_null(MODULE_CONTENT_INSTALLED) ? explode(';', MODULE_CONTENT_INSTALLED) : array());
  $modules = array('installed' => array(), 'new' => array());

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

  if ($maindir = @dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/')) {
    while ($group = $maindir->read()) {
      if ( ($group != '.') && ($group != '..') && is_dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/' . $group)) {
        if ($dir = @dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/' . $group)) {
          while ($file = $dir->read()) {
            if (!is_dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/' . $group . '/' . $file)) {
              if (substr($file, strrpos($file, '.')) == $file_extension) {
                $class = substr($file, 0, strrpos($file, '.'));

                if (!class_exists($class)) {
                  if ($KUUZU_Language->definitionsExist('Shop/modules/content/' . $group . '/' . pathinfo($file, PATHINFO_FILENAME))) {
                    $KUUZU_Language->loadDefinitions('Shop/modules/content/' . $group . '/' . pathinfo($file, PATHINFO_FILENAME));
                  }

                  include(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/' . $group . '/' . $file);
                }

                if (class_exists($class)) {
                  $module = new $class();

                  if (in_array($group . '/' . $class, $modules_installed)) {
                    $modules['installed'][] = array('code' => $class,
                                                    'title' => $module->title,
                                                    'group' => $group,
                                                    'sort_order' => (int)$module->sort_order);
                  } else {
                    $modules['new'][] = array('code' => $class,
                                              'title' => $module->title,
                                              'group' => $group);
                  }
                }
              }
            }
          }

          $dir->close();
        }
      }
    }

    $maindir->close();

    foreach (Apps::getModules('Content') as $k => $class) {
      $module = new $class();

      if (in_array($k, $modules_installed)) {
        $modules['installed'][] = array('code' => $k,
                                        'title' => $module->title,
                                        'group' => $module->group,
                                        'sort_order' => (int)$module->sort_order);
      } else {
        $modules['new'][] = array('code' => $k,
                                  'title' => $module->title,
                                  'group' => $module->group);
      }
    }

    function _sortContentModulesInstalled($a, $b) {
      return strnatcmp($a['group'] . '-' . (int)$a['sort_order'] . '-' . $a['title'], $b['group'] . '-' . (int)$b['sort_order'] . '-' . $b['title']);
    }

    function _sortContentModuleFiles($a, $b) {
      return strnatcmp($a['group'] . '-' . $a['title'], $b['group'] . '-' . $b['title']);
    }

    usort($modules['installed'], '_sortContentModulesInstalled');
    usort($modules['new'], '_sortContentModuleFiles');
  }

// Update sort order in MODULE_CONTENT_INSTALLED
  $_installed = array();

  foreach ( $modules['installed'] as $m ) {
    if (strpos($m['code'], '\\') !== false) {
      $_installed[] = $m['code'];
    } else {
      $_installed[] = $m['group'] . '/' . $m['code'];
    }
  }

  if ( implode(';', $_installed) != MODULE_CONTENT_INSTALLED ) {
    Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $_installed), 'last_modified' => 'now()'], ['configuration_key' => 'MODULE_CONTENT_INSTALLED']);
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $_GET['module'] ) {
            foreach ($_POST['configuration'] as $key => $value) {
              $key = HTML::sanitize($key);
              $value = HTML::sanitize($value);

              $KUUZU_Db->save('configuration', [
                'configuration_value' => $value
              ], [
                'configuration_key' => $key
              ]);
            }

            break;
          }
        }

        KUUZU::redirect('modules_content.php', 'module=' . $_GET['module']);

        break;

      case 'install':
        $class = $code = $_GET['module'];

        foreach ( $modules['new'] as $m ) {
          if ( $m['code'] == $code ) {
            if (strpos($code, '\\') !== false) {
              $class = Apps::getModuleClass($code, 'Content');
            }

            $module = new $class();

            $module->install();

            $modules_installed[] = $m['group'] . '/' . $m['code'];

            Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed), 'last_modified' => 'now()'], ['configuration_key' => 'MODULE_CONTENT_INSTALLED']);

            KUUZU::redirect('modules_content.php', 'module=' . $code . '&action=edit');
          }
        }

        KUUZU::redirect('modules_content.php', 'action=list_new&module=' . $code);

        break;

      case 'remove':
        $class = $code = $_GET['module'];

        foreach ( $modules['installed'] as $m ) {
          if ( $m['code'] == $code ) {
            if (strpos($code, '\\') !== false) {
              $class = Apps::getModuleClass($code, 'Content');

              $installed_code = $m['code'];
            } else {
              $installed_code = $m['group'] . '/' . $m['code'];
            }

            $module = new $class();

            $module->remove();

            $modules_installed = explode(';', MODULE_CONTENT_INSTALLED);

            if (in_array($installed_code, $modules_installed)) {
              unset($modules_installed[array_search($installed_code, $modules_installed)]);
            }

            Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed), 'last_modified' => 'now()'], ['configuration_key' => 'MODULE_CONTENT_INSTALLED']);

            KUUZU::redirect('modules_content.php');
          }
        }

        KUUZU::redirect('modules_content.php', 'module=' . $code);

        break;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo KUUZU::getDef('heading_title'); ?></td>
<?php
  if ($action == 'list_new') {
    echo '            <td class="smallText" align="right">' . HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link('modules_content.php')) . '</td>';
  } else {
    echo '            <td class="smallText" align="right">' . HTML::button(KUUZU::getDef('image_module_install') . ' (' . count($modules['new']) . ')', 'fa fa-plus', KUUZU::link('modules_content.php', 'action=list_new')) . '</td>';
  }
?>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php
  if ( $action == 'list_new' ) {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_modules'); ?></td>
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_group'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
    foreach ( $modules['new'] as $m ) {
      if (strpos($m['code'], '\\') !== false) {
        $class = Apps::getModuleClass($m['code'], 'Content');

        $module = new $class();
        $module->code = $m['code'];
      } else {
        $module = new $m['code']();
      }

      if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'signature' => (isset($module->signature) ? $module->signature : null),
                             'api_version' => (isset($module->api_version) ? $module->api_version : null));

        $mInfo = new \ArrayObject($module_info, \ArrayObject::ARRAY_AS_PROPS);
      }

      if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link('modules_content.php', 'action=list_new&module=' . $module->code) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $module->title; ?></td>
                <td class="dataTableContent"><?php echo $module->group; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif')); } else { echo '<a href="' . KUUZU::link('modules_content.php', 'action=list_new&module=' . $module->code) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
            </table>
<?php
  } else {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_modules'); ?></td>
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_group'); ?></td>
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_sort_order'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
    foreach ( $modules['installed'] as $m ) {
      if (strpos($m['code'], '\\') !== false) {
        $class = Apps::getModuleClass($m['code'], 'Content');

        $module = new $class();
        $module->code = $m['code'];
      } else {
        $module = new $m['code']();
      }

      if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $module->code))) && !isset($mInfo)) {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'signature' => (isset($module->signature) ? $module->signature : null),
                             'api_version' => (isset($module->api_version) ? $module->api_version : null),
                             'sort_order' => (int)$module->sort_order,
                             'keys' => array());

        foreach ($module->keys() as $key) {
          $key = HTML::sanitize($key);

          $Qkeys = $KUUZU_Db->get('configuration', [
            'configuration_title',
            'configuration_value',
            'configuration_description',
            'use_function',
            'set_function'
          ], [
            'configuration_key' => $key
          ]);

          $module_info['keys'][$key] = [
            'title' => $Qkeys->value('configuration_title'),
            'value' => $Qkeys->value('configuration_value'),
            'description' => $Qkeys->value('configuration_description'),
            'use_function' => $Qkeys->value('use_function'),
            'set_function' => $Qkeys->value('set_function')
          ];
        }

        $mInfo = new \ArrayObject($module_info, \ArrayObject::ARRAY_AS_PROPS);
      }

      if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link('modules_content.php', 'module=' . $module->code) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $module->title; ?></td>
                <td class="dataTableContent"><?php echo $module->group; ?></td>
                <td class="dataTableContent"><?php echo $module->sort_order; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($module->code == $mInfo->code) ) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif')); } else { echo '<a href="' . KUUZU::link('modules_content.php', 'module=' . $module->code) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
            </table>
<?php
  }
?>
            <p class="smallText"><?php echo KUUZU::getDef('text_module_directory') . ' ' . KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/content/'; ?></p>
            </td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $keys = '';

      foreach ($mInfo->keys as $key => $value) {
        $keys .= '<strong>' . $value['title'] . '</strong><br />' . $value['description'] . '<br />';

        if ($value['set_function']) {
          eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
        } else {
          $keys .= HTML::inputField('configuration[' . $key . ']', $value['value']);
        }

        $keys .= '<br /><br />';
      }

      $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

      $heading[] = array('text' => '<strong>' . $mInfo->title . '</strong>');

      $contents = array('form' => HTML::form('modules', KUUZU::link('modules_content.php', 'module=' . $mInfo->code . '&action=save')));
      $contents[] = array('text' => $keys);
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link('modules_content.php', 'module=' . $mInfo->code)));

      break;

    default:
      if ( isset($mInfo) ) {
        $heading[] = array('text' => '<strong>' . $mInfo->title . '</strong>');

        if ($action == 'list_new') {
          $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_module_install'), 'fa fa-plus', KUUZU::link('modules_content.php', 'module=' . $mInfo->code . '&action=install')));

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $skuuversion) = explode('|', $mInfo->signature))) {
            $contents[] = array('text' => '<br />' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '&nbsp;<strong>' . KUUZU::getDef('text_info_version') . '</strong> ' . $sversion . ' (<a href="https://kuuzu.org/sig/' . $mInfo->signature . '" target="_blank">' . KUUZU::getDef('text_info_online_status') . '</a>)');
          }

          if (isset($mInfo->api_version)) {
            $contents[] = array('text' => HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '&nbsp;<strong>' . KUUZU::getDef('text_info_api_version') . '</strong> ' . $mInfo->api_version);
          }

          $contents[] = array('text' => '<br />' . $mInfo->description);
        } else {
          $keys = '';

          foreach ($mInfo->keys as $value) {
            $keys .= '<strong>' . $value['title'] . '</strong><br />';

            if ($value['use_function']) {
              $use_function = $value['use_function'];

              if (preg_match('/->/', $use_function)) {
                $class_method = explode('->', $use_function);

                if (!isset(${$class_method[0]}) || !is_object(${$class_method[0]})) {
                  include('includes/classes/' . $class_method[0] . '.php');
                  ${$class_method[0]} = new $class_method[0]();
                }

                $keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
              } else {
                $keys .= tep_call_function($use_function, $value['value']);
              }
            } else {
              $keys .= $value['value'];
            }

            $keys .= '<br /><br />';
          }

          $keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

          $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link('modules_content.php', 'module=' . $mInfo->code . '&action=edit')) . HTML::button(KUUZU::getDef('image_module_remove'), 'fa fa-minus', KUUZU::link('modules_content.php', 'module=' . $mInfo->code . '&action=remove')));

          if (isset($mInfo->signature) && (list($scode, $smodule, $sversion, $skuuversion) = explode('|', $mInfo->signature))) {
            $contents[] = array('text' => '<br />' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '&nbsp;<strong>' . KUUZU::getDef('text_info_version') . '</strong> ' . $sversion . ' (<a href="https://kuuzu.org/sig/' . $mInfo->signature . '" target="_blank">' . KUUZU::getDef('text_info_online_status') . '</a>)');
          }

          if (isset($mInfo->api_version)) {
            $contents[] = array('text' => HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '&nbsp;<strong>' . KUUZU::getDef('text_info_api_version') . '</strong> ' . $mInfo->api_version);
          }

          $contents[] = array('text' => '<br />' . $mInfo->description);
          $contents[] = array('text' => '<br />' . $keys);
        }
      }

      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
