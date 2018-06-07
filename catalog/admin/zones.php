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
        $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
        $zone_code = HTML::sanitize($_POST['zone_code']);
        $zone_name = HTML::sanitize($_POST['zone_name']);

        $KUUZU_Db->save('zones', [
          'zone_country_id' => (int)$zone_country_id,
          'zone_code' => $zone_code,
          'zone_name' => $zone_name
        ]);

        KUUZU::redirect(FILENAME_ZONES);
        break;
      case 'save':
        $zone_id = HTML::sanitize($_GET['cID']);
        $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
        $zone_code = HTML::sanitize($_POST['zone_code']);
        $zone_name = HTML::sanitize($_POST['zone_name']);

        $KUUZU_Db->save('zones', [
          'zone_country_id' => (int)$zone_country_id,
          'zone_code' => $zone_code,
          'zone_name' => $zone_name
        ], [
          'zone_id' => (int)$zone_id
        ]);

        KUUZU::redirect(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $zone_id);
        break;
      case 'deleteconfirm':
        $zone_id = HTML::sanitize($_GET['cID']);

        $KUUZU_Db->delete('zones', ['zone_id' => (int)$zone_id]);

        KUUZU::redirect(FILENAME_ZONES, 'page=' . $_GET['page']);
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
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_country_name'); ?></td>
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_zone_name'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo KUUZU::getDef('table_heading_zone_code'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
  $Qzones = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS z.zone_id, c.countries_id, c.countries_name, z.zone_name, z.zone_code, z.zone_country_id from :table_zones z, :table_countries c where z.zone_country_id = c.countries_id order by c.countries_name, z.zone_name limit :page_set_offset, :page_set_max_results');
  $Qzones->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qzones->execute();

  while ($Qzones->fetch()) {
    if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qzones->valueInt('zone_id')))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
      $cInfo = new objectInfo($Qzones->toArray());
    }

    if (isset($cInfo) && is_object($cInfo) && ($Qzones->valueInt('zone_id') === (int)$cInfo->zone_id)) {
      echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $Qzones->valueInt('zone_id')) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $Qzones->value('countries_name'); ?></td>
                <td class="dataTableContent"><?php echo $Qzones->value('zone_name'); ?></td>
                <td class="dataTableContent" align="center"><?php echo $Qzones->value('zone_code'); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($Qzones->valueInt('zone_id') === (int)$cInfo->zone_id) ) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif'), ''); } else { echo '<a href="' . KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $Qzones->valueInt('zone_id')) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qzones->getPageSetLabel(KUUZU::getDef('text_display_number_of_zones')); ?></td>
                    <td class="smallText" align="right"><?php echo $Qzones->getPageSetLinks(); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td class="smallText" colspan="2" align="right"><?php echo HTML::button(KUUZU::getDef('image_new_zone'), 'fa fa-plus', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&action=new')); ?></td>
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
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_new_zone') . '</strong>');

      $contents = array('form' => HTML::form('zones', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&action=insert')));
      $contents[] = array('text' => KUUZU::getDef('text_info_insert_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_zones_name') . '<br />' . HTML::inputField('zone_name'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_zones_code') . '<br />' . HTML::inputField('zone_code'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_country_name') . '<br />' . HTML::selectField('zone_country_id', tep_get_countries()));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_edit_zone') . '</strong>');

      $contents = array('form' => HTML::form('zones', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=save')));
      $contents[] = array('text' => KUUZU::getDef('text_info_edit_intro'));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_zones_name') . '<br />' . HTML::inputField('zone_name', $cInfo->zone_name));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_zones_code') . '<br />' . HTML::inputField('zone_code', $cInfo->zone_code));
      $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_country_name') . '<br />' . HTML::selectField('zone_country_id', tep_get_countries(), $cInfo->countries_id));
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<strong>' . KUUZU::getDef('text_info_heading_delete_zone') . '</strong>');

      $contents = array('form' => HTML::form('zones', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=deleteconfirm')));
      $contents[] = array('text' => KUUZU::getDef('text_info_delete_intro'));
      $contents[] = array('text' => '<br /><strong>' . $cInfo->zone_name . '</strong>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id)));
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = array('text' => '<strong>' . $cInfo->zone_name . '</strong>');

        $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=edit')) . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link(FILENAME_ZONES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->zone_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_zones_name') . '<br />' . $cInfo->zone_name . ' (' . $cInfo->zone_code . ')');
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_info_country_name') . ' ' . $cInfo->countries_name);
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
