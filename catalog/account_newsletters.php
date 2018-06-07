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

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    KUUZU::redirect('login.php');
  }

  $KUUZU_Language->loadDefinitions('account_newsletters');

  $Qnewsletter = $KUUZU_Db->prepare('select customers_newsletter from :table_customers where customers_id = :customers_id');
  $Qnewsletter->bindInt(':customers_id', $_SESSION['customer_id']);
  $Qnewsletter->execute();

  if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
    $newsletter_general = (isset($_POST['newsletter_general']) && ($_POST['newsletter_general'] == '1')) ? 1 : 0;

    if ($newsletter_general !== $Qnewsletter->valueInt('customers_newsletter')) {
      $newsletter_general = ($Qnewsletter->valueInt('customers_newsletter') === 1) ? 0 : 1;

      $KUUZU_Db->save('customers', ['customers_newsletter' => $newsletter_general], ['customers_id' => $_SESSION['customer_id']]);
    }

    $messageStack->add_session('account', KUUZU::getDef('success_newsletter_updated'), 'success');

    KUUZU::redirect('account.php');
  }

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('account.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'), KUUZU::link('account_newsletters.php'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<?php echo HTML::form('account_newsletter', KUUZU::link('account_newsletters.php'), 'post', 'class="form-horizontal"', ['tokenize' => true, 'action' => 'process']); ?>

<div class="contentContainer">
  <div class="contentText">
    <div class="form-group">
      <label class="control-label col-sm-4"><?php echo KUUZU::getDef('my_newsletters_general_newsletter'); ?></label>
      <div class="col-sm-8">
        <div class="checkbox">
          <label>
            <?php echo HTML::checkboxField('newsletter_general', '1', (($Qnewsletter->value('customers_newsletter') == '1') ? true : false)); ?>
            <?php if (tep_not_null(KUUZU::getDef('my_newsletters_general_newsletter_description'))) echo ' ' . KUUZU::getDef('my_newsletters_general_newsletter_description'); ?>
          </label>
        </div>
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
