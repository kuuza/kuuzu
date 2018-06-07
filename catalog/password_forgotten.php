<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Hash;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\Mail;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $KUUZU_Language->loadDefinitions('password_forgotten');

  $password_reset_initiated = false;

  if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    $email_address = HTML::sanitize($_POST['email_address']);

    $Qcheck = $KUUZU_Db->get('customers', ['customers_firstname', 'customers_lastname', 'customers_id'], ['customers_email_address' => $email_address]);

    if ( $Qcheck->fetch() !== false ) {
      $actionRecorder = new actionRecorder('ar_reset_password', $Qcheck->valueInt('customers_id'), $email_address);

      if ($actionRecorder->canPerform()) {
        $actionRecorder->record();

        $reset_key = Hash::getRandomString(40);

        $KUUZU_Db->save('customers_info', ['password_reset_key' => $reset_key, 'password_reset_date' => 'now()'], ['customers_info_id' => $Qcheck->valueInt('customers_id')]);

        $reset_key_url = KUUZU::link('password_reset.php', 'account=' . urlencode($email_address) . '&key=' . $reset_key, false);

        if ( strpos($reset_key_url, '&amp;') !== false ) {
          $reset_key_url = str_replace('&amp;', '&', $reset_key_url);
        }

        $passwordEmail = new Mail($email_address, $Qcheck->value('customers_firstname') . ' ' . $Qcheck->value('customers_lastname'), STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, KUUZU::getDef('email_password_reset_subject', ['store_name' => STORE_NAME]));
        $passwordEmail->setBodyHTML(KUUZU::getDef('email_password_reset_body_html', ['store_name' => STORE_NAME, 'store_email_address' => STORE_OWNER_EMAIL_ADDRESS, 'reset_url' => $reset_key_url]));
        $passwordEmail->setBodyPlain(KUUZU::getDef('email_password_reset_body', ['store_name' => STORE_NAME, 'store_email_address' => STORE_OWNER_EMAIL_ADDRESS, 'reset_url' => $reset_key_url]));
        $passwordEmail->send();

        $password_reset_initiated = true;
      } else {
        $actionRecorder->record(false);

        $messageStack->add('password_forgotten', KUUZU::getDef('error_action_recorder', ['module_action_recorder_reset_password_minutes' => (defined('MODULE_ACTION_RECORDER_RESET_PASSWORD_MINUTES') ? (int)MODULE_ACTION_RECORDER_RESET_PASSWORD_MINUTES : 5)]));
      }
    } else {
      $messageStack->add('password_forgotten', KUUZU::getDef('text_no_email_address_found'));
    }
  }

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('login.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'), KUUZU::link('password_forgotten.php'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<?php
  if ($messageStack->size('password_forgotten') > 0) {
    echo $messageStack->output('password_forgotten');
  }

  if ($password_reset_initiated == true) {
?>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-success"><?php echo KUUZU::getDef('text_password_reset_initiated'); ?></div>
  </div>
</div>

<?php
  } else {
?>

<?php echo HTML::form('password_forgotten', KUUZU::link('password_forgotten.php', 'action=process'), 'post', 'class="form-horizontal"', ['tokenize' => true]); ?>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-info"><?php echo KUUZU::getDef('text_main'); ?></div>

    <div class="form-group has-feedback">
      <label for="inputEmail" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_email_address'); ?></label>
      <div class="col-sm-9">
        <?php echo HTML::inputField('email_address', NULL, 'required aria-required="true" autofocus="autofocus" id="inputEmail" placeholder="' . KUUZU::getDef('entry_email_address_text') . '"', 'email'); ?>
        <?php echo KUUZU::getDef('form_required_input'); ?>
      </div>
    </div>
  </div>

  <div class="buttonSet row">
    <div class="col-xs-6"><?php echo HTML::button(KUUZU::getDef('image_button_back'), 'fa fa-angle-left', KUUZU::link('login.php')); ?></div>
    <div class="col-xs-6 text-right"><?php echo HTML::button(KUUZU::getDef('image_button_continue'), 'fa fa-angle-right', null, null, 'btn-success'); ?></div>
  </div>
</div>

</form>

<?php
  }

  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
