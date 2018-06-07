<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo KUUZU::getDef('module_boxes_information_box_title'); ?></div>
  <div class="panel-body">
    <ul class="list-unstyled">
      <li><a href="<?php echo KUUZU::link('shipping.php'); ?>"><?php echo KUUZU::getDef('module_boxes_information_box_shipping'); ?></a></li>
      <li><a href="<?php echo KUUZU::link('privacy.php'); ?>"><?php echo KUUZU::getDef('module_boxes_information_box_privacy'); ?></a></li>
      <li><a href="<?php echo KUUZU::link('conditions.php'); ?>"><?php echo KUUZU::getDef('module_boxes_information_box_conditions'); ?></a></li>
      <li><a href="<?php echo KUUZU::link('contact_us.php'); ?>"><?php echo KUUZU::getDef('module_boxes_information_box_contact'); ?></a></li>
    </ul>
  </div>
</div>
