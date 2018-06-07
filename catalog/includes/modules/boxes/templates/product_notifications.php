<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><a href="<?php echo KUUZU::link('account_notifications.php'); ?>"><?php echo KUUZU::getDef('module_boxes_product_notifications_box_title'); ?></a></div>
  <div class="panel-body"><?php echo $notif_contents; ?></div>
</div>
