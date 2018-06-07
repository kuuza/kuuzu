<?php
use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;
?>
<div class="create-account-link <?php echo (MODULE_CONTENT_CREATE_ACCOUNT_LINK_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-info">
    <div class="panel-body">
      <h2><?php echo KUUZU::getDef('module_content_login_heading_new_customer'); ?></h2>

      <p class="alert alert-info"><?php echo KUUZU::getDef('module_content_login_text_new_customer'); ?></p>
      <p><?php echo KUUZU::getDef('module_content_login_text_new_customer_introduction', ['store_name' => STORE_NAME]); ?></p>

      <p class="text-right"><?php echo HTML::button(KUUZU::getDef('image_button_continue'), 'fa fa-angle-right', KUUZU::link('create_account.php'), null, 'btn-primary btn-block'); ?></p>
    </div>
  </div>
</div>
