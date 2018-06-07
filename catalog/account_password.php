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

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    KUUZU::redirect('login.php');
  }

  $KUUZU_Language->loadDefinitions('account_password');

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    $password_current = HTML::sanitize($_POST['password_current']);
    $password_new = HTML::sanitize($_POST['password_new']);
    $password_confirmation = HTML::sanitize($_POST['password_confirmation']);

    $error = false;

    if (strlen($password_new) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_password', KUUZU::getDef('entry_password_new_error', ['min_length' => ENTRY_PASSWORD_MIN_LENGTH]));
    } elseif ($password_new != $password_confirmation) {
      $error = true;

      $messageStack->add('account_password', KUUZU::getDef('entry_password_new_error_not_matching'));
    }

    if ($error == false) {
      $Qcheck = $KUUZU_Db->prepare('select customers_password from :table_customers where customers_id = :customers_id');
      $Qcheck->bindInt(':customers_id', $_SESSION['customer_id']);
      $Qcheck->execute();

      if (Hash::verify($password_current, $Qcheck->value('customers_password'))) {
        $KUUZU_Db->save('customers', ['customers_password' => Hash::encrypt($password_new)], ['customers_id' => (int)$_SESSION['customer_id']]);
        $KUUZU_Db->save('customers_info', ['customers_info_date_account_last_modified' => 'now()'], ['customers_info_id' => (int)$_SESSION['customer_id']]);

        $messageStack->add_session('account', KUUZU::getDef('success_password_updated'), 'success');

        KUUZU::redirect('account.php');
      } else {
        $error = true;

        $messageStack->add('account_password', KUUZU::getDef('error_current_password_not_matching'));
      }
    }
  }

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('account.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'), KUUZU::link('account_password.php'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<?php
  if ($messageStack->size('account_password') > 0) {
    echo $messageStack->output('account_password');
  }
?>

<?php echo HTML::form('account_password', KUUZU::link('account_password.php'), 'post', 'class="form-horizontal"', ['tokenize' => true, 'action' => 'process']); ?>

<?php
  $Qcustomer = $KUUZU_Db->get('customers', 'customers_email_address', ['customers_id' => (int)$_SESSION['customer_id']]);
  echo HTML::hiddenField('username', $Qcustomer->value('customers_email_address'), 'readonly autocomplete="username"');
?>

<div class="contentContainer">
  <p class="text-danger text-right"><?php echo KUUZU::getDef('form_required_information'); ?></p>

  <div class="contentText">
    <div class="form-group has-feedback">
      <label for="inputCurrent" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_password_current'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::passwordField('password_current', NULL, 'required aria-required="true" autofocus="autofocus" id="inputCurrent" autocomplete="current-password" placeholder="' . KUUZU::getDef('entry_password_current_text') . '"', 'password'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
    <div class="form-group has-feedback">
      <label for="inputPassword" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_password_new'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::passwordField('password_new', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . KUUZU::getDef('entry_password_new_text') . '"', 'password'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
    <div class="form-group has-feedback">
      <label for="inputConfirmation" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_password_confirmation'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::passwordField('password_confirmation', NULL, 'required aria-required="true" id="inputConfirmation" autocomplete="new-password" placeholder="' . KUUZU::getDef('entry_password_confirmation_text') . '"', 'password'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
  </div>

  <div class="buttonSet row">
    <div class="col-xs-6"><?php echo HTML::button(KUUZU::getDef('image_button_back'), 'fa fa-angle-left', KUUZU::link('account.php')); ?></div>
    <div class="col-xs-6 text-right"><?php echo HTML::button(KUUZU::getDef('image_button_continue'), 'fa fa-angle-right', null, null, 'btn-success'); ?></div>
  </div>

</div>

</form>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
