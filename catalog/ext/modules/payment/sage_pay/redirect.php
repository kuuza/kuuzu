<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  chdir('../../../../');
  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot(array('page' => 'checkout_payment.php'));
    KUUZU::redirect('login.php');
  }

  if ( isset($_GET['payment_error']) && tep_not_null($_GET['payment_error']) ) {
    $redirect_url = KUUZU::link('checkout_payment.php', 'payment_error=' . $_GET['payment_error'] . (isset($_GET['error']) && tep_not_null($_GET['error']) ? '&error=' . $_GET['error'] : ''));
  } else {
    $hidden_params = '';

    if ($_SESSION['payment'] == 'sage_pay_direct') {
      $redirect_url = KUUZU::link('checkout_process.php', 'check=3D');
      $hidden_params = HTML::hiddenField('MD', $_POST['MD']) . HTML::hiddenField('PaRes', $_POST['PaRes']);
    } else {
      $redirect_url = KUUZU::link('checkout_success.php');
    }
  }

  $KUUZU_Language->loadDefinitions('checkout_confirmation');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo KUUZU::getDef('html_params'); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo KUUZU::getDef('charset'); ?>">
<title><?php echo KUUZU::getDef('title', ['store_name' => STORE_NAME]); ?></title>
<base href="<?= KUUZU::getConfig('http_server', 'Shop') . KUUZU::getConfig('http_path', 'Shop'); ?>">
</head>
<body>
<form name="redirect" action="<?php echo $redirect_url; ?>" method="post" target="_top"><?php echo $hidden_params; ?>
<noscript>
  <p align="center" class="main">The transaction is being finalized. Please click continue to finalize your order.</p>
  <p align="center" class="main"><input type="submit" value="Continue" /></p>
</noscript>
</form>
<script>
document.redirect.submit();
</script>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
