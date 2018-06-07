<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;

http_response_code(404);
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Error - Page Not Found</title>
<link href="<?= KUUZU::link('Shop/ext/bootstrap/css/bootstrap.min.css', null, false); ?>" rel="stylesheet">
<link href="<?= KUUZU::link('Shop/ext/font-awesome/4.7.0/css/font-awesome.min.css', null, false); ?>" rel="stylesheet">
<script src="<?= KUUZU::link('Shop/ext/jquery/jquery-3.1.1.min.js', null, false); ?>"></script>
</head>
<body>
<div class="container">
  <div class="jumbotron" style="margin-top: 40px;">
    <h1>This Page is Missing</h1>

    <p>It looks like this page is missing. Please continue back to our website and try again.</p>

    <p style="margin-top: 40px;"><?= HTML::button('Return to website', 'fa fa-chevron-right', KUUZU::link('index.php'), null, 'btn-primary'); ?></p>
  </div>
</div>
<script src="<?= KUUZU::link('Shop/ext/bootstrap/js/bootstrap.min.js', null, false); ?>"></script>
</body>
</html>
