<?php
use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;
?>
<div class="login-form <?php echo (MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-success">
    <div class="panel-body">
      <h2><?php echo KUUZU::getDef('module_content_login_heading_returning_customer'); ?></h2>

      <p class="alert alert-success"><?php echo KUUZU::getDef('module_content_login_text_returning_customer'); ?></p>

      <?php echo HTML::form('login', KUUZU::link('login.php', 'action=process'), 'post', '', ['tokenize' => true]); ?>

      <div class="form-group">
        <?php echo HTML::inputField('email_address', NULL, 'autofocus="autofocus" required id="inputEmail" placeholder="' . KUUZU::getDef('entry_email_address_text') . '"', 'email'); ?>
      </div>

      <div class="form-group">
        <?php echo HTML::passwordField('password', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . KUUZU::getDef('entry_password_text') . '"', 'password'); ?>
      </div>

      <p class="text-right"><?php echo HTML::button(KUUZU::getDef('image_button_login'), 'fa fa-sign-in', null, null, 'btn-success btn-block'); ?></p>

      </form>
    </div>
  </div>

    <p><?php echo '<a class="btn btn-default" role="button" href="' . KUUZU::link('password_forgotten.php') . '">' . KUUZU::getDef('module_content_login_text_password_forgotten') . '</a>'; ?></p>

</div>
