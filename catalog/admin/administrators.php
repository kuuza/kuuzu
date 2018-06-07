<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Hash;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        $username = HTML::sanitize($_POST['username']);
        $password = HTML::sanitize($_POST['password']);

        $Qcheck = $KUUZU_Db->get('administrators', 'id', ['user_name' => $username], null, 1);

        if (!$Qcheck->check()) {
          $KUUZU_Db->save('administrators', [
            'user_name' => $username,
            'user_password' => Hash::encrypt($password)
          ]);
        } else {
          $KUUZU_MessageStack->add(KUUZU::getDef('error_administrator_exists'), 'error');
        }

        KUUZU::redirect(FILENAME_ADMINISTRATORS);
        break;
      case 'save':
        $username = HTML::sanitize($_POST['username']);
        $password = HTML::sanitize($_POST['password']);

        $Qcheck = $KUUZU_Db->get('administrators', [
          'id',
          'user_name'
        ], [
          'id' => (int)$_GET['aID']
        ]);

// update username in current session if changed
        if ( ($Qcheck->valueInt('id') === $_SESSION['admin']['id']) && ($username !== $_SESSION['admin']['username']) ) {
          $_SESSION['admin']['username'] = $username;
        }

        $KUUZU_Db->save('administrators', [
          'user_name' => $username
        ], [
          'id' => (int)$_GET['aID']
        ]);

        if (tep_not_null($password)) {
          $KUUZU_Db->save('administrators', [
            'user_password' => Hash::encrypt($password)
          ], [
            'id' => (int)$_GET['aID']
          ]);
        }

        KUUZU::redirect(FILENAME_ADMINISTRATORS, 'aID=' . (int)$_GET['aID']);
        break;
      case 'deleteconfirm':
        $id = (int)$_GET['aID'];

        $Qcheck = $KUUZU_Db->get('administrators', ['id', 'user_name'], ['id' => $id]);

        if ($_SESSION['admin']['id'] === $Qcheck->valueInt('id')) {
          unset($_SESSION['admin']);
        }

        $KUUZU_Db->delete('administrators', ['id' => $id]);

        KUUZU::redirect(FILENAME_ADMINISTRATORS);
        break;
    }
  }

  $show_listing = true;

  require($kuuTemplate->getFile('template_top.php'));

  if (empty($action)) {
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('image_insert'), 'fa fa-plus', KUUZU::link('administrators.php', 'action=new'), null, 'btn-info'); ?>
</div>

<?php
  }
?>

<h2><i class="fa fa-users"></i> <a href="<?= KUUZU::link('administrators.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<?php
  if (!empty($action)) {
    $heading = $contents = [];

    if ($action != 'new') {
      if (isset($_GET['aID'])) {
        $Qadmin = $KUUZU_Db->get('administrators', ['id', 'user_name'], ['id' => (int)$_GET['aID']]);

        if ($Qadmin->fetch() !== false) {
          $aInfo = new objectInfo($Qadmin->toArray());

          switch ($action) {
            case 'edit':
              $heading[] = array('text' => HTML::outputProtected($aInfo->user_name));

              $contents = array('form' => HTML::form('administrator', KUUZU::link(FILENAME_ADMINISTRATORS, 'aID=' . $aInfo->id . '&action=save'), 'post', 'autocomplete="off"'));
              $contents[] = array('text' => KUUZU::getDef('text_info_edit_intro'));
              $contents[] = array('text' => KUUZU::getDef('text_info_username') . '<br />' . HTML::inputField('username', $aInfo->user_name));
              $contents[] = array('text' => KUUZU::getDef('text_info_new_password') . '<br />' . HTML::passwordField('password'));
              $contents[] = array('text' => HTML::button(KUUZU::getDef('image_save'), 'fa fa-save', null, null, 'btn-success') . HTML::button(KUUZU::getDef('image_cancel'), null, KUUZU::link(FILENAME_ADMINISTRATORS), null, 'btn-link'));
              break;

            case 'delete':
              $heading[] = array('text' => HTML::outputProtected($aInfo->user_name));

              $contents = array('form' => HTML::form('administrator', KUUZU::link(FILENAME_ADMINISTRATORS, 'aID=' . $aInfo->id . '&action=deleteconfirm')));
              $contents[] = array('text' => KUUZU::getDef('text_info_delete_intro'));
              $contents[] = array('text' => '<strong>' . HTML::outputProtected($aInfo->user_name) . '</strong>');
              $contents[] = array('text' => HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', null, null, 'btn-danger') . HTML::button(KUUZU::getDef('image_cancel'), null, KUUZU::link(FILENAME_ADMINISTRATORS), null, 'btn-link'));
              break;
          }
        }
      }
    } else {
      $heading[] = array('text' => KUUZU::getDef('text_info_heading_new_administrator'));

      $contents = array('form' => HTML::form('administrator', KUUZU::link(FILENAME_ADMINISTRATORS, 'action=insert'), 'post', 'autocomplete="off"'));
      $contents[] = array('text' => KUUZU::getDef('text_info_insert_intro'));
      $contents[] = array('text' => KUUZU::getDef('text_info_username') . '<br />' . HTML::inputField('username'));
      $contents[] = array('text' => KUUZU::getDef('text_info_password') . '<br />' . HTML::passwordField('password'));
      $contents[] = array('text' => HTML::button(KUUZU::getDef('image_save'), 'fa fa-save', null, null, 'btn-success') . HTML::button(KUUZU::getDef('image_cancel'), null, KUUZU::link(FILENAME_ADMINISTRATORS), null, 'btn-link'));
    }

    if (tep_not_null($heading) && tep_not_null($contents)) {
      $show_listing = false;

      echo HTML::panel($heading, $contents, ['type' => 'info']);
    }
  }

  if ($show_listing === true) {
?>

<table class="kuuzu-table table table-hover">
  <thead>
    <tr class="info">
      <th><?= KUUZU::getDef('table_heading_administrators'); ?></th>
      <th class="action"></th>
    </tr>
  </thead>
  <tbody>

<?php
    $Qadmins = $KUUZU_Db->get('administrators', ['id', 'user_name'], null, 'user_name');

    while ($Qadmins->fetch()) {
?>

    <tr>
      <td><?= $Qadmins->valueProtected('user_name'); ?></td>
      <td class="action"><a href="<?= KUUZU::link('administrators.php', 'aID=' . $Qadmins->valueInt('id') . '&action=edit'); ?>"><i class="fa fa-pencil" title="<?= KUUZU::getDef('image_edit'); ?>"></i></a><a href="<?= KUUZU::link('administrators.php', 'aID=' . $Qadmins->valueInt('id') . '&action=delete'); ?>"><i class="fa fa-trash" title="<?= KUUZU::getDef('image_delete'); ?>"></i></a></td>
    </tr>

<?php
    }
?>

  </tbody>
</table>

<?php
  }

  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
