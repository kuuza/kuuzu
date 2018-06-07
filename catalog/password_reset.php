<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Hash;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\Is;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $KUUZU_Language->loadDefinitions('password_reset');

  $error = false;

  if ( !isset($_GET['account']) || !isset($_GET['key']) ) {
    $error = true;

    $messageStack->add_session('password_forgotten', KUUZU::getDef('text_no_reset_link_found'));
  }

  if ($error == false) {
    $email_address = HTML::sanitize($_GET['account']);
    $password_key = HTML::sanitize($_GET['key']);

    if ( (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) || (Is::email($email_address) == false) ) {
      $error = true;

      $messageStack->add_session('password_forgotten', KUUZU::getDef('text_no_email_address_found'));
    } elseif (strlen($password_key) != 40) {
      $error = true;

      $messageStack->add_session('password_forgotten', KUUZU::getDef('text_no_reset_link_found'));
    } else {
      $Qcheck = $KUUZU_Db->prepare('select c.customers_id, c.customers_email_address, ci.password_reset_key, ci.password_reset_date from :table_customers c, :table_customers_info ci where c.customers_email_address = :customers_email_address and c.customers_id = ci.customers_info_id');
      $Qcheck->bindValue(':customers_email_address', $email_address);
      $Qcheck->execute();

      if ( $Qcheck->fetch() !== false ) {
        if ( empty($Qcheck->value('password_reset_key')) || ($Qcheck->value('password_reset_key') != $password_key) || (strtotime($Qcheck->value('password_reset_date') . ' +1 day') <= time()) ) {
          $error = true;

          $messageStack->add_session('password_forgotten', KUUZU::getDef('text_no_reset_link_found'));
        }
      } else {
        $error = true;

        $messageStack->add_session('password_forgotten', KUUZU::getDef('text_no_email_address_found'));
      }
    }
  }

  if ($error == true) {
    KUUZU::redirect('password_forgotten.php');
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    $password_new = HTML::sanitize($_POST['password']);
    $password_confirmation = HTML::sanitize($_POST['confirmation']);

    if (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('password_reset', KUUZU::getDef('entry_password_new_error', ['min_length' => ENTRY_PASSWORD_MIN_LENGTH]));
    } elseif ($password_new != $password_confirmation) {
      $error = true;

      $messageStack->add('password_reset', KUUZU::getDef('entry_password_new_error_not_matching'));
    }

    if ($error == false) {
      $KUUZU_Db->save('customers', ['customers_password' => Hash::encrypt($password_new)], ['customers_id' => $Qcheck->valueInt('customers_id')]);

      $KUUZU_Db->save('customers_info', ['customers_info_date_account_last_modified' => 'now()', 'password_reset_key' => 'null', 'password_reset_date' => 'null'], ['customers_info_id' => $Qcheck->valueInt('customers_id')]);

      $messageStack->add_session('login', KUUZU::getDef('success_password_reset'), 'success');

      KUUZU::redirect('login.php');
    }
  }

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('login.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<?php
  if ($messageStack->size('password_reset') > 0) {
    echo $messageStack->output('password_reset');
  }
?>

<?php echo HTML::form('password_reset', KUUZU::link('password_reset.php', 'account=' . $email_address . '&key=' . $password_key . '&action=process'), 'post', 'class="form-horizontal"', ['tokenize' => true]); ?>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-info"><?php echo KUUZU::getDef('text_main'); ?></div>

    <div class="form-group has-feedback">
      <label for="inputPassword" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_password'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::passwordField('password', NULL, 'required aria-required="true" autofocus="autofocus" id="inputPassword" autocomplete="new-password" placeholder="' . KUUZU::getDef('entry_password_text') . '"', 'password'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
    <div class="form-group has-feedback">
      <label for="inputConfirm" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_password_confirmation'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::passwordField('confirmation', NULL, 'required aria-required="true" id="inputConfirm" autocomplete="new-password" placeholder="' . KUUZU::getDef('entry_password_confirmation_text') . '"', 'password'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo HTML::button(KUUZU::getDef('image_button_continue'), 'fa fa-angle-right', null, null, 'btn-success'); ?></div>
  </div>
</div>

</form>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
