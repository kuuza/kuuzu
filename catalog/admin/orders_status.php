<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['oID'])) $orders_status_id = HTML::sanitize($_GET['oID']);

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $orders_status_name_array = $_POST['orders_status_name'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = array('orders_status_name' => HTML::sanitize($orders_status_name_array[$language_id]),
                                  'public_flag' => ((isset($_POST['public_flag']) && ($_POST['public_flag'] == '1')) ? '1' : '0'),
                                  'downloads_flag' => ((isset($_POST['downloads_flag']) && ($_POST['downloads_flag'] == '1')) ? '1' : '0'));

          if ($action == 'insert') {
            if (empty($orders_status_id)) {
              $Qnext = $KUUZU_Db->get('orders_status', 'max(orders_status_id) as orders_status_id');
              $orders_status_id = $Qnext->valueInt('orders_status_id') + 1;
            }

            $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                     'language_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $KUUZU_Db->save('orders_status', $sql_data_array);
          } elseif ($action == 'save') {
            $KUUZU_Db->save('orders_status',
              $sql_data_array,
            [
              'orders_status_id' => (int)$orders_status_id,
              'language_id' => (int)$language_id
            ]);
          }
        }

        if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
          $KUUZU_Db->save('configuration', [
            'configuration_value' => $orders_status_id
          ], [
            'configuration_key' => 'DEFAULT_ORDERS_STATUS_ID'
          ]);
        }

        KUUZU::redirect(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id);
        break;
      case 'deleteconfirm':
        $oID = HTML::sanitize($_GET['oID']);

        $Qstatus = $KUUZU_Db->get('configuration', 'configuration_value', ['configuration_key' => 'DEFAULT_ORDERS_STATUS_ID']);

        if ($Qstatus->value('configuration_value') == $oID) {
          $KUUZU_Db->save('configuration', [
            'configuration_value' => ''
          ], [
            'configuration_key' => 'DEFAULT_ORDERS_STATUS_ID'
          ]);
        }

        $KUUZU_Db->delete('orders_status', ['orders_status_id' => $oID]);

        KUUZU::redirect(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']);
        break;
      case 'delete':
        $oID = HTML::sanitize($_GET['oID']);

        $Qstatus = $KUUZU_Db->get('orders', 'orders_status', ['orders_status' => (int)$oID], null, 1);

        $remove_status = true;
        if ($oID == DEFAULT_ORDERS_STATUS_ID) {
          $remove_status = false;
          $KUUZU_MessageStack->add(KUUZU::getDef('error_remove_default_order_status'), 'error');
        } elseif ($Qstatus->fetch() !== false) {
          $remove_status = false;
          $KUUZU_MessageStack->add(KUUZU::getDef('error_status_used_in_orders'), 'error');
        } else {
          $Qhistory = $KUUZU_Db->get('orders_status_history', 'orders_status_id', ['orders_status_id' => (int)$oID], null, 1);
          if ($Qhistory->fetch() !== false) {
            $remove_status = false;
            $KUUZU_MessageStack->add(KUUZU::getDef('error_status_used_in_history'), 'error');
          }
        }
        break;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo KUUZU::getDef('heading_title'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_orders_status'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo KUUZU::getDef('table_heading_public_status'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo KUUZU::getDef('table_heading_downloads_status'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
  $Qstatus = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS * from :table_orders_status where language_id = :language_id order by orders_status_id limit :page_set_offset, :page_set_max_results');
  $Qstatus->bindInt(':language_id', $KUUZU_Language->getId());
  $Qstatus->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qstatus->execute();

  while ($Qstatus->fetch()) {
    if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ((int)$_GET['oID'] === $Qstatus->valueInt('orders_status_id')))) && !isset($oInfo) && (substr($action, 0, 3) != 'new')) {
      $oInfo = new objectInfo($Qstatus->toArray());
    }

    if (isset($oInfo) && is_object($oInfo) && ($Qstatus->valueInt('orders_status_id') === (int)$oInfo->orders_status_id)) {
      echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $Qstatus->valueInt('orders_status_id')) . '\'">' . "\n";
    }

    if ((int)DEFAULT_ORDERS_STATUS_ID == $Qstatus->valueInt('orders_status_id')) {
      echo '                <td class="dataTableContent"><strong>' . $Qstatus->value('orders_status_name') . ' (' . KUUZU::getDef('text_default') . ')</strong></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $Qstatus->value('orders_status_name') . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="center"><?php echo HTML::image(KUUZU::linkImage('icons/' . (($Qstatus->valueInt('public_flag') === 1) ? 'tick.gif' : 'cross.gif'))); ?></td>
                <td class="dataTableContent" align="center"><?php echo HTML::image(KUUZU::linkImage('icons/' . (($Qstatus->valueInt('downloads_flag') === 1) ? 'tick.gif' : 'cross.gif'))); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($oInfo) && is_object($oInfo) && ($Qstatus->valueInt('orders_status_id') === (int)$oInfo->orders_status_id)) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif'), ''); } else { echo '<a href="' . KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $Qstatus->valueInt('orders_status_id')) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qstatus->getPageSetLabel(KUUZU::getDef('text_display_number_of_orders_status')); ?></td>
                    <td class="smallText" align="right"><?php echo $Qstatus->getPageSetLinks(); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td class="smallText" colspan="2" align="right"><?php echo HTML::button(KUUZU::getDef('image_insert'), 'fa fa-plus', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=new')); ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_new_orders_status') . '</strong>');

      $contents = array('form' => HTML::form('status', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert')));
      $contents[] = array('text' => KUUZU::getDef('text_info_insert_intro'));

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<br />' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . HTML::inputField('orders_status_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_orders_status_name') . $orders_status_inputs_string);
      $contents[] = array('text' => '<br />' . HTML::checkboxField('public_flag', '1') . ' ' . KUUZU::getDef('text_set_public_status'));
      $contents[] = array('text' => HTML::checkboxField('downloads_flag', '1') . ' ' . KUUZU::getDef('text_set_downloads_status'));
      $contents[] = array('text' => '<br />' . HTML::checkboxField('default') . ' ' . KUUZU::getDef('text_set_default'));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_edit_orders_status') . '</strong>');

      $contents = array('form' => HTML::form('status', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save')));
      $contents[] = array('text' => KUUZU::getDef('text_info_edit_intro'));

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<br />' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . HTML::inputField('orders_status_name[' . $languages[$i]['id'] . ']', tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_orders_status_name') . $orders_status_inputs_string);
      $contents[] = array('text' => '<br />' . HTML::checkboxField('public_flag', '1', $oInfo->public_flag) . ' ' . KUUZU::getDef('text_set_public_status'));
      $contents[] = array('text' => HTML::checkboxField('downloads_flag', '1', $oInfo->downloads_flag) . ' ' . KUUZU::getDef('text_set_downloads_status'));
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<br />' . HTML::checkboxField('default') . ' ' . KUUZU::getDef('text_set_default'));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_delete_orders_status') . '</strong>');

      $contents = array('form' => HTML::form('status', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm')));
      $contents[] = array('text' => KUUZU::getDef('text_info_delete_intro'));
      $contents[] = array('text' => '<br /><strong>' . $oInfo->orders_status_name . '</strong>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id)));
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<strong>' . $oInfo->orders_status_name . '</strong>');

        $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit')) . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete')));

        $orders_status_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br />' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $orders_status_inputs_string);
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
