<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\DateTime;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\Is;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    KUUZU::redirect('login.php');
  }

  $KUUZU_Language->loadDefinitions('account_edit');

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    if (ACCOUNT_GENDER == 'true') $gender = HTML::sanitize($_POST['gender']);
    $firstname = HTML::sanitize($_POST['firstname']);
    $lastname = HTML::sanitize($_POST['lastname']);
    if (ACCOUNT_DOB == 'true') $dob = HTML::sanitize($_POST['dob']);
    $email_address = HTML::sanitize($_POST['email_address']);
    $telephone = HTML::sanitize($_POST['telephone']);
    $fax = HTML::sanitize($_POST['fax']);

    $error = false;

    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;

        $messageStack->add('account_edit', KUUZU::getDef('entry_gender_error'));
      }
    }

    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_first_name_error', ['min_length' => ENTRY_FIRST_NAME_MIN_LENGTH]));
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_last_name_error', ['min_length' => ENTRY_LAST_NAME_MIN_LENGTH]));
    }

    if (ACCOUNT_DOB == 'true') {
      $dobDateTime = new DateTime($dob);

      if ((strlen($dob) < ENTRY_DOB_MIN_LENGTH) || ($dobDateTime->isValid() === false)) {
        $error = true;

        $messageStack->add('account_edit', KUUZU::getDef('entry_date_of_birth_error'));
      }
    }

    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_email_address_error', ['min_length' => ENTRY_EMAIL_ADDRESS_MIN_LENGTH]));
    }

    if (!Is::email($email_address)) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_email_address_check_error'));
    }

    $Qcheck = $KUUZU_Db->prepare('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
    $Qcheck->bindValue(':customers_email_address', $email_address);
    $Qcheck->bindInt(':customers_id', $_SESSION['customer_id']);
    $Qcheck->execute();

    if ($Qcheck->fetch() !== false) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_email_address_error_exists'));
    }

    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('account_edit', KUUZU::getDef('entry_telephone_number_error', ['min_length' => ENTRY_TELEPHONE_MIN_LENGTH]));
    }

    if ($error == false) {
      $sql_data_array = array('customers_firstname' => $firstname,
                              'customers_lastname' => $lastname,
                              'customers_email_address' => $email_address,
                              'customers_telephone' => $telephone,
                              'customers_fax' => $fax);

      if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
      if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = $dobDateTime->getRaw(false);

      $KUUZU_Db->save('customers', $sql_data_array, ['customers_id' => (int)$_SESSION['customer_id']]);

      $KUUZU_Db->save('customers_info', ['customers_info_date_account_last_modified' => 'now()'], ['customers_info_id' => (int)$_SESSION['customer_id']]);

      $sql_data_array = ['entry_firstname' => $firstname,
                         'entry_lastname' => $lastname];

      $KUUZU_Db->save('address_book', $sql_data_array, ['customers_id' => (int)$_SESSION['customer_id'], 'address_book_id' => (int)$_SESSION['customer_default_address_id']]);

// reset the session variables
      $_SESSION['customer_first_name'] = $firstname;

      $messageStack->add_session('account', KUUZU::getDef('success_account_updated'), 'success');

      KUUZU::redirect('account.php');
    }
  }

  $Qaccount = $KUUZU_Db->prepare('select * from :table_customers where customers_id = :customers_id');
  $Qaccount->bindInt(':customers_id', $_SESSION['customer_id']);
  $Qaccount->execute();

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('account.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'), KUUZU::link('account_edit.php'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<?php
  if ($messageStack->size('account_edit') > 0) {
    echo $messageStack->output('account_edit');
  }
?>

<?php echo HTML::form('account_edit', KUUZU::link('account_edit.php'), 'post', 'class="form-horizontal"', ['tokenize' => true, 'action' => 'process']); ?>

<div class="contentContainer">
  <div class="text-danger text-right"><?php echo KUUZU::getDef('form_required_information'); ?></div>

  <?php
  if (ACCOUNT_GENDER == 'true') {
    if (isset($gender)) {
      $male = ($gender == 'm') ? true : false;
    } else {
      $male = ($Qaccount->value('customers_gender') == 'm') ? true : false;
    }
    $female = !$male;
  ?>
  <div class="form-group has-feedback">
    <label class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_gender'); ?></label>
    <div class="col-sm-9">
      <label class="radio-inline">
        <?php echo HTML::radioField('gender', 'm', $male, 'required aria-required="true" aria-describedby="atGender"') . ' ' . KUUZU::getDef('male'); ?>
      </label>
      <label class="radio-inline">
        <?php echo HTML::radioField('gender', 'f', $female) . ' ' . KUUZU::getDef('female'); ?>
      </label>
      <?php echo KUUZU::getDef('form_required_input'); ?>
      <?php if (tep_not_null(KUUZU::getDef('entry_gender_text'))) echo '<span id="atGender" class="help-block">' . KUUZU::getDef('entry_gender_text') . '</span>'; ?>
    </div>
  </div>
  <?php
  }
  ?>
  <div class="form-group has-feedback">
    <label for="inputFirstName" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_first_name'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('firstname', $Qaccount->value('customers_firstname'), 'required aria-required="true" id="inputFirstName" placeholder="' . KUUZU::getDef('entry_first_name_text') . '"'); ?>
      <?php echo KUUZU::getDef('form_required_input'); ?>
    </div>
  </div>
  <div class="form-group has-feedback">
    <label for="inputLastName" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_last_name'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('lastname', $Qaccount->value('customers_lastname'), 'required aria-required="true" id="inputLastName" placeholder="' . KUUZU::getDef('entry_last_name_text') . '"'); ?>
      <?php echo KUUZU::getDef('form_required_input'); ?>
    </div>
  </div>

  <?php
  if (ACCOUNT_DOB == 'true') {
?>
  <div class="form-group has-feedback">
    <label for="inputName" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_date_of_birth'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('dob', DateTime::toShort($Qaccount->value('customers_dob')), 'data-provide="datepicker" required aria-required="true" id="dob" placeholder="' . KUUZU::getDef('entry_date_of_birth_text') . '"'); ?>
      <?php echo KUUZU::getDef('form_required_input'); ?>
    </div>
  </div>
<?php
  }
?>

  <div class="form-group has-feedback">
    <label for="inputEmail" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_email_address'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('email_address', $Qaccount->value('customers_email_address'), 'required aria-required="true" id="inputEmail" placeholder="' . KUUZU::getDef('entry_email_address_text') . '"', 'email'); ?>
      <?php echo KUUZU::getDef('form_required_input'); ?>
    </div>
  </div>
  <div class="form-group has-feedback">
    <label for="inputTelephone" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_telephone_number'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('telephone', $Qaccount->value('customers_telephone'), 'required aria-required="true" id="inputTelephone" placeholder="' . KUUZU::getDef('entry_telephone_number_text') . '"', 'tel'); ?>
      <?php echo KUUZU::getDef('form_required_input'); ?>
    </div>
  </div>
  <div class="form-group">
    <label for="inputFax" class="control-label col-sm-3"><?php echo KUUZU::getDef('entry_fax_number'); ?></label>
    <div class="col-sm-9">
      <?php echo HTML::inputField('fax', $Qaccount->value('customers_fax'), 'id="inputFax" placeholder="' . KUUZU::getDef('entry_fax_number_text') . '"'); ?>
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
