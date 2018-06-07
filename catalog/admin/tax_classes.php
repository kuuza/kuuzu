<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

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
        $tax_class_title = HTML::sanitize($_POST['tax_class_title']);
        $tax_class_description = HTML::sanitize($_POST['tax_class_description']);

        $KUUZU_Db->save('tax_class', [
          'tax_class_title' => $tax_class_title,
          'tax_class_description' => $tax_class_description,
          'date_added' => 'now()'
        ]);

        KUUZU::redirect(FILENAME_TAX_CLASSES);
        break;
      case 'save':
        $tax_class_id = HTML::sanitize($_GET['tID']);
        $tax_class_title = HTML::sanitize($_POST['tax_class_title']);
        $tax_class_description = HTML::sanitize($_POST['tax_class_description']);

        $KUUZU_Db->save('tax_class', [
          'tax_class_title' => $tax_class_title,
          'tax_class_description' => $tax_class_description,
          'last_modified' => 'now()'
        ], [
          'tax_class_id' => (int)$tax_class_id
        ]);

        KUUZU::redirect(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tax_class_id);
        break;
      case 'deleteconfirm':
        $tax_class_id = HTML::sanitize($_GET['tID']);

        $KUUZU_Db->delete('tax_class', ['tax_class_id' => (int)$tax_class_id]);

        KUUZU::redirect(FILENAME_TAX_CLASSES, 'page=' . $_GET['page']);
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_tax_classes'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
  $Qclasses = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS tax_class_id, tax_class_title, tax_class_description, last_modified, date_added from :table_tax_class order by tax_class_title limit :page_set_offset, :page_set_max_results');
  $Qclasses->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qclasses->execute();

  while ($Qclasses->fetch()) {
    if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ((int)$_GET['tID'] === $Qclasses->valueInt('tax_class_id')))) && !isset($tcInfo) && (substr($action, 0, 3) != 'new')) {
      $tcInfo = new objectInfo($Qclasses->toArray());
    }

    if (isset($tcInfo) && is_object($tcInfo) && ($Qclasses->valueInt('tax_class_id') === (int)$tcInfo->tax_class_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo'              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $Qclasses->valueInt('tax_class_id')) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $Qclasses->value('tax_class_title'); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($tcInfo) && is_object($tcInfo) && ($Qclasses->valueInt('tax_class_id') === (int)$tcInfo->tax_class_id)) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif'), ''); } else { echo '<a href="' . KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $Qclasses->valueInt('tax_class_id')) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qclasses->getPageSetLabel(KUUZU::getDef('text_display_number_of_tax_classes')); ?></td>
                    <td class="smallText" align="right"><?php echo $Qclasses->getPageSetLinks(); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td class="smallText" colspan="2" align="right"><?php echo HTML::button(KUUZU::getDef('image_new_tax_class'), 'fa fa-plus', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=new')); ?></td>
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
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_new_tax_class') . '</strong>');

      $contents = array('form' => HTML::form('classes', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&action=insert')));
      $contents[] = array('text' => KUUZU::getDef('text_info_insert_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_class_title') . '<br />' . HTML::inputField('tax_class_title'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_class_description') . '<br />' . HTML::inputField('tax_class_description'));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_edit_tax_class') . '</strong>');

      $contents = array('form' => HTML::form('classes', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=save')));
      $contents[] = array('text' => KUUZU::getDef('text_info_edit_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_class_title') . '<br />' . HTML::inputField('tax_class_title', $tcInfo->tax_class_title));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_class_description') . '<br />' . HTML::inputField('tax_class_description', $tcInfo->tax_class_description));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_delete_tax_class') . '</strong>');

      $contents = array('form' => HTML::form('classes', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=deleteconfirm')));
      $contents[] = array('text' => KUUZU::getDef('text_info_delete_intro'));
      $contents[] = array('text' => '<br /><strong>' . $tcInfo->tax_class_title . '</strong>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id)));
      break;
    default:
      if (isset($tcInfo) && is_object($tcInfo)) {
        $heading[] = array('text' => '<strong>' . $tcInfo->tax_class_title . '</strong>');

        $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=edit')) . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link(FILENAME_TAX_CLASSES, 'page=' . $_GET['page'] . '&tID=' . $tcInfo->tax_class_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_date_added') . ' ' . DateTime::toShort($tcInfo->date_added));
        if (isset($tcInfo->last_modified)) {
          $contents[] = array('text' => KUUZU::getDef('text_info_last_modified') . ' ' . DateTime::toShort($tcInfo->last_modified));
        }
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_class_description') . '<br />' . $tcInfo->tax_class_description);
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
