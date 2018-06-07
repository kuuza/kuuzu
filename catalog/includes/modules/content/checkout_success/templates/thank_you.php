<?php
use Kuuzu\KU\KUUZU;

?>
<div class="panel panel-success">
  <div class="panel-heading">
    <?php echo KUUZU::getDef('module_content_checkout_success_text_thanks_for_shopping'); ?>
  </div>

  <div class="panel-body">
    <p><?php echo KUUZU::getDef('module_content_checkout_success_text_success'); ?></p>
    <p><?php echo KUUZU::getDef('module_content_checkout_success_text_see_orders', ['account_history_link' => KUUZU::link('account_history.php')]); ?></p>
    <p><?php echo KUUZU::getDef('module_content_checkout_success_text_contact_store_owner', ['contact_us_link' =>  KUUZU::link('contact_us.php')]); ?></p>
  </div>
</div>
