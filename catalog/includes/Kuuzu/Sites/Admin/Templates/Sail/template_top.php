<?php
use Kuuzu\KU\KUUZU;
?>
<!DOCTYPE html>
<html <?= KUUZU::getDef('html_params'); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= KUUZU::getDef('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?= KUUZU::getDef('title', ['store_name' => STORE_NAME]); ?></title>
<base href="<?= KUUZU::getConfig('http_server', 'Admin') . KUUZU::getConfig('http_path', 'Admin'); ?>" />
<meta name="generator" content="Kuuzu" />
<link rel="stylesheet" type="text/css" href="<?= KUUZU::link('Shop/ext/jquery/ui/redmond/jquery-ui-1.11.4.min.css', '', false); ?>">
<script type="text/javascript" src="<?= KUUZU::link('Shop/ext/jquery/jquery-3.1.1.min.js', '', false); ?>"></script>
<script type="text/javascript" src="<?= KUUZU::link('Shop/ext/jquery/ui/jquery-ui-1.11.4.min.js', '', false); ?>"></script>

<link href="<?= KUUZU::link('Shop/ext/bootstrap/css/bootstrap.min.css', '', false); ?>" rel="stylesheet">
<link href="<?= KUUZU::link('Shop/ext/font-awesome/4.7.0/css/font-awesome.min.css', '', false); ?>" rel="stylesheet">
<link href="<?= KUUZU::link('Shop/ext/smartmenus/jquery.smartmenus.bootstrap.css', '', false); ?>" rel="stylesheet">
<link href="<?= KUUZU::link('Shop/ext/chartist/chartist.min.css', '', false); ?>" rel="stylesheet">

<?php
  if (tep_not_null(KUUZU::getDef('jquery_datepicker_i18n_code'))) {
?>
<script type="text/javascript" src="<?= KUUZU::link('Shop/ext/jquery/ui/i18n/datepicker-' . KUUZU::getDef('jquery_datepicker_i18n_code') . '.js', '', false); ?>"></script>
<script type="text/javascript">
$.datepicker.setDefaults($.datepicker.regional['<?= KUUZU::getDef('jquery_datepicker_i18n_code'); ?>']);
</script>
<?php
  }
?>

<link rel="stylesheet" type="text/css" href="<?= $kuuTemplate->getPublicFile('css/stylesheet.css'); ?>">
<script src="<?= KUUZU::linkPublic('js/general.js'); ?>"></script>
</head>
<body>

<?php require($kuuTemplate->getFile('header.php')); ?>

<div id="contentText" class="container-fluid">
