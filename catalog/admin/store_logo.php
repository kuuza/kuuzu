<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\FileSystem;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $error = false;

        $store_logo = new upload('store_logo');
        $store_logo->set_extensions(array('png', 'gif', 'jpg'));
        $store_logo->set_destination(KUUZU::getConfig('dir_root', 'Shop') . 'images/');

        if ($store_logo->parse()) {
          if ($store_logo->save()) {
            $KUUZU_Db->save('configuration', [
              'configuration_value' => $store_logo->filename,
              'last_modified' => 'now()'
            ], [
              'configuration_key' => 'STORE_LOGO'
            ]);

            $KUUZU_MessageStack->add(KUUZU::getDef('success_logo_updated'), 'success');
          } else {
            $error = true;
          }
        } else {
          $error = true;
        }

        if ($error == false) {
          KUUZU::redirect(FILENAME_STORE_LOGO);
        }
        break;
    }
  }

  if (!FileSystem::isWritable(KUUZU::getConfig('dir_root', 'Shop') . 'images/')) {
    $KUUZU_MessageStack->add(KUUZU::getDef('error_images_directory_not_writeable', ['sec_dir_permissions_link' => KUUZU::link(FILENAME_SEC_DIR_PERMISSIONS)]), 'error');
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
        <td><?php echo HTML::image(KUUZU::linkImage('Shop/' . STORE_LOGO)); ?></td>
      </tr>
      <tr>
        <td><?php echo HTML::form('logo', KUUZU::link(FILENAME_STORE_LOGO, 'action=save'), 'post', 'enctype="multipart/form-data"'); ?>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main" valign="top"><?php echo KUUZU::getDef('text_logo_image'); ?></td>
              <td class="main"><?php echo HTML::fileField('store_logo'); ?></td>
              <td class="smallText"><?php echo HTML::button(KUUZU::getDef('image_save'), 'fa fa-save'); ?></td>
            </tr>
          </table>
        </form></td>
      </tr>
      <tr>
        <td class="main"><?php echo KUUZU::getDef('text_format_and_location'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo KUUZU::getConfig('dir_root', 'Shop') . 'images/' . STORE_LOGO; ?></td>
      </tr>
    </table>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
