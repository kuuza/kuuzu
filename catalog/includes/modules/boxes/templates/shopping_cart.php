<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><a href="<?php echo KUUZU::link('shopping_cart.php'); ?>"><?php echo KUUZU::getDef('module_boxes_shopping_cart_box_title'); ?></a></div>
  <div class="panel-body">
    <ul class="list-unstyled">
      <?php echo $cart_contents_string; ?>
    </ul>
  </div>
</div>
