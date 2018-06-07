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

  $KUUZU_Language->loadDefinitions('create_account_success');

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'));

  if (sizeof($_SESSION['navigation']->snapshot) > 0) {
    $origin_href = KUUZU::link($_SESSION['navigation']->snapshot['page'], tep_array_to_string($_SESSION['navigation']->snapshot['get'], array(session_name())));
    $_SESSION['navigation']->clear_snapshot();
  } else {
    $origin_href = KUUZU::link('index.php');
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<div class="contentContainer">
  <div class="contentText">
    <div class="alert alert-success">
      <?php echo KUUZU::getDef('text_account_created', ['contact_us_link' => KUUZU::link('contact_us.php')]); ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo HTML::button(KUUZU::getDef('image_button_continue'), 'fa fa-angle-right', $origin_href, null, 'btn-success'); ?></div>
  </div>
</div>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
