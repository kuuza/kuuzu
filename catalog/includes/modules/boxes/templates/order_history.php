<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo KUUZU::getDef('module_boxes_order_history_box_title'); ?></div>
  <div class="panel-body">
    <ul class="list-unstyled">
      <?php echo $customer_orders_string; ?>
    </ul>
  </div>
</div>
