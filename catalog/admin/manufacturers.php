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
        if (isset($_GET['mID'])) $manufacturers_id = HTML::sanitize($_GET['mID']);
        $manufacturers_name = HTML::sanitize($_POST['manufacturers_name']);

        $sql_data_array = array('manufacturers_name' => $manufacturers_name);

        if ($action == 'insert') {
          $insert_sql_data = array('date_added' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $KUUZU_Db->save('manufacturers', $sql_data_array);
          $manufacturers_id = $KUUZU_Db->lastInsertId();
        } elseif ($action == 'save') {
          $update_sql_data = array('last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          $KUUZU_Db->save('manufacturers', $sql_data_array, [
            'manufacturers_id' => (int)$manufacturers_id
          ]);
        }

        $manufacturers_image = new upload('manufacturers_image');
        $manufacturers_image->set_destination(KUUZU::getConfig('dir_root', 'Shop') . 'images/');

        if ($manufacturers_image->parse() && $manufacturers_image->save()) {
          $KUUZU_Db->save('manufacturers', [
            'manufacturers_image' => $manufacturers_image->filename
          ], [
            'manufacturers_id' => (int)$manufacturers_id
          ]);
        }

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $manufacturers_url_array = $_POST['manufacturers_url'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = array('manufacturers_url' => HTML::sanitize($manufacturers_url_array[$language_id]));

          if ($action == 'insert') {
            $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                     'languages_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            $KUUZU_Db->save('manufacturers_info', $sql_data_array);
          } elseif ($action == 'save') {
            $KUUZU_Db->save('manufacturers_info', $sql_data_array, [
              'manufacturers_id' => (int)$manufacturers_id,
              'languages_id' => $KUUZU_Language->getId()
            ]);
          }
        }

        Cache::clear('manufacturers');

        KUUZU::redirect(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id);
        break;
      case 'deleteconfirm':
        $manufacturers_id = HTML::sanitize($_GET['mID']);

        if (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on')) {
          $Qmanufacturer = $KUUZU_Db->get('manufacturers', 'manufacturers_image', ['manufacturers_id' => (int)$manufacturers_id]);

          if (tep_not_null($Qmanufacturer->value('manufacturers_image'))) {
            $image_location = KUUZU::getConfig('dir_root', 'Shop') . 'images/' . $Qmanufacturer->value('manufacturers_image');

            if (is_file($image_location)) unlink($image_location);
          }
        }

        $KUUZU_Db->delete('manufacturers', ['manufacturers_id' => (int)$manufacturers_id]);
        $KUUZU_Db->delete('manufacturers_info', ['manufacturers_id' => (int)$manufacturers_id]);

        if (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on')) {
          $Qproducts = $KUUZU_Db->get('products', 'products_id', ['manufacturers_id' => (int)$manufacturers_id]);
          while ($Qproducts->fetch()) {
            tep_remove_product($Qproducts->value('products_id'));
          }
        } else {
          $KUUZU_Db->save('products', [
            'manufacturers_id' => ''
          ], [
            'manufacturers_id' => (int)$manufacturers_id
          ]);
        }

        Cache::clear('manufacturers');

        KUUZU::redirect(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']);
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
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_manufacturers'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
  $Qmanufacturers = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from :table_manufacturers order by manufacturers_name limit :page_set_offset, :page_set_max_results');
  $Qmanufacturers->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qmanufacturers->execute();

  while ($Qmanufacturers->fetch()) {
    if ((!isset($_GET['mID']) || (isset($_GET['mID']) && ((int)$_GET['mID'] === $Qmanufacturers->valueInt('manufacturers_id')))) && !isset($mInfo) && (substr($action, 0, 3) != 'new')) {
      $Qproducts = $KUUZU_Db->get('products', 'count(*) as products_count', ['manufacturers_id' => $Qmanufacturers->valueInt('manufacturers_id')]);

      $mInfo_array = array_merge($Qmanufacturers->toArray(), $Qproducts->toArray());
      $mInfo = new objectInfo($mInfo_array);
    }

    if (isset($mInfo) && is_object($mInfo) && ($Qmanufacturers->valueInt('manufacturers_id') === (int)$mInfo->manufacturers_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id') . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id')) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $Qmanufacturers->value('manufacturers_name'); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($mInfo) && is_object($mInfo) && ($Qmanufacturers->valueInt('manufacturers_id') === (int)$mInfo->manufacturers_id)) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif')); } else { echo '<a href="' . KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id')) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qmanufacturers->getPageSetLabel(KUUZU::getDef('text_display_number_of_manufacturers')); ?></td>
                    <td class="smallText" align="right"><?php echo $Qmanufacturers->getPageSetLinks(); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (empty($action)) {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo HTML::button(KUUZU::getDef('image_insert'), 'fa fa-plus', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . (isset($mInfo) ? '&mID=' . $mInfo->manufacturers_id : '') . '&action=new')); ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_heading_new_manufacturer') . '</strong>');

      $contents = array('form' => HTML::form('manufacturers', KUUZU::link(FILENAME_MANUFACTURERS, 'action=insert', 'post', 'enctype="multipart/form-data"')));
      $contents[] = array('text' => KUUZU::getDef('text_new_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_name') . '<br />' . HTML::inputField('manufacturers_name'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_image') . '<br />' . HTML::fileField('manufacturers_image'));

      $manufacturer_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . HTML::inputField('manufacturers_url[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_url') . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_heading_edit_manufacturer') . '</strong>');

      $contents = array('form' => HTML::form('manufacturers', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"')));
      $contents[] = array('text' => KUUZU::getDef('text_edit_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_name') . '<br />' . HTML::inputField('manufacturers_name', $mInfo->manufacturers_name));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_image') . '<br />' . HTML::fileField('manufacturers_image') . '<br />' . $mInfo->manufacturers_image);

      $manufacturer_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $manufacturer_inputs_string .= '<br />' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . HTML::inputField('manufacturers_url[' . $languages[$i]['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_manufacturers_url') . $manufacturer_inputs_string);
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_heading_delete_manufacturer') . '</strong>');

      $contents = array('form' => HTML::form('manufacturers', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm')));
      $contents[] = array('text' => KUUZU::getDef('text_delete_intro'));
      $contents[] = array('text' => '<br /><strong>' . $mInfo->manufacturers_name . '</strong>');
      $contents[] = array('text' => '<br />' . HTML::checkboxField('delete_image', '', true) . ' ' . KUUZU::getDef('text_delete_image'));

      if ($mInfo->products_count > 0) {
        $contents[] = array('text' => '<br />' . HTML::checkboxField('delete_products') . ' ' . KUUZU::getDef('text_delete_products'));
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_delete_warning_products', ['products_count' =>  $mInfo->products_count]));
      }

      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id)));
      break;
    default:
      if (isset($mInfo) && is_object($mInfo)) {
        $heading[] = array('text' => '<strong>' . $mInfo->manufacturers_name . '</strong>');

        $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit')) . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_date_added') . ' ' . DateTime::toShort($mInfo->date_added));
        if (tep_not_null($mInfo->last_modified)) $contents[] = array('text' => KUUZU::getDef('text_last_modified') . ' ' . DateTime::toShort($mInfo->last_modified));
        $contents[] = array('text' => '<br />' . tep_info_image($mInfo->manufacturers_image, $mInfo->manufacturers_name));
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_products') . ' ' . $mInfo->products_count);
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
