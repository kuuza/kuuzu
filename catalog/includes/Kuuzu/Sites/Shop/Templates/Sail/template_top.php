<?php
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  $kuuTemplate->buildBlocks();

  if (!$kuuTemplate->hasBlocks('boxes_column_left')) {
    $kuuTemplate->setGridContentWidth($kuuTemplate->getGridContentWidth() + $kuuTemplate->getGridColumnWidth());
  }

  if (!$kuuTemplate->hasBlocks('boxes_column_right')) {
    $kuuTemplate->setGridContentWidth($kuuTemplate->getGridContentWidth() + $kuuTemplate->getGridColumnWidth());
  }
?>
<!DOCTYPE html>
<html <?php echo KUUZU::getDef('html_params'); ?>>
<head>
<meta charset="<?php echo KUUZU::getDef('charset'); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo HTML::outputProtected($kuuTemplate->getTitle()); ?></title>
<base href="<?= KUUZU::getConfig('http_server', 'Shop') . KUUZU::getConfig('http_path', 'Shop'); ?>">

<link href="ext/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- font awesome -->
<link href="ext/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<link href="<?= $kuuTemplate->getPublicFile('css/custom.css'); ?>" rel="stylesheet">
<link href="<?= $kuuTemplate->getPublicFile('css/user.css'); ?>" rel="stylesheet">

<!--[if lt IE 9]>
   <script src="ext/js/html5shiv.js"></script>
   <script src="ext/js/respond.min.js"></script>
   <script src="ext/js/excanvas.min.js"></script>
<![endif]-->

<script src="ext/jquery/jquery-3.1.1.min.js"></script>

<?php echo $kuuTemplate->getBlocks('header_tags'); ?>
</head>
<body>

  <?php echo $kuuTemplate->getContent('navigation'); ?>

  <div id="bodyWrapper" class="<?php echo BOOTSTRAP_CONTAINER; ?>">
    <div class="row">

      <?php require($kuuTemplate->getFile('header.php')); ?>

      <div id="bodyContent" class="col-md-<?php echo $kuuTemplate->getGridContentWidth(); ?> <?php echo ($kuuTemplate->hasBlocks('boxes_column_left') ? 'col-md-push-' . $kuuTemplate->getGridColumnWidth() : ''); ?>">
