<?php
use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;
?>
<div id="headerShortcuts" class="col-sm-<?php echo $content_width; ?> text-right buttons">
  <div class="btn-group">
<?php
  echo HTML::button(KUUZU::getDef('module_content_header_buttons_title_cart_contents') . ($_SESSION['cart']->count_contents() > 0 ? ' (' . $_SESSION['cart']->count_contents() . ')' : ''), 'fa fa-shopping-cart', KUUZU::link('shopping_cart.php')) .
       HTML::button(KUUZU::getDef('module_content_header_buttons_title_checkout'), 'fa fa-credit-card', KUUZU::link('checkout_shipping.php')) .
       HTML::button(KUUZU::getDef('module_content_header_buttons_title_my_account'), 'fa fa-user', KUUZU::link('account.php'));

  if (isset($_SESSION['customer_id'])) {
    echo HTML::button(KUUZU::getDef('module_content_header_buttons_title_logoff'), 'fa fa-sign-out', KUUZU::link('logoff.php'));
  }
?>
  </div>
</div>

