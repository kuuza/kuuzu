<?php
use Kuuzu\KU\KUUZU;
?>
<li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo isset($_SESSION['customer_id']) ? KUUZU::getDef('module_navbar_account_logged_in', ['customer_first_name' => $_SESSION['customer_first_name']]) : KUUZU::getDef('module_navbar_account_logged_out'); ?></a>
  <ul class="dropdown-menu">
    <?php
    if (isset($_SESSION['customer_id'])) {
      echo '<li><a href="' . KUUZU::link('logoff.php') . '">' . KUUZU::getDef('module_navbar_account_logoff') . '</a></li>';
    }
    else {
      echo '<li><a href="' . KUUZU::link('login.php') . '">' . KUUZU::getDef('module_navbar_account_login') . '</a></li>';
      echo '<li><a href="' . KUUZU::link('create_account.php') . '">' . KUUZU::getDef('module_navbar_account_register') . '</a></li>';
    }
    ?>
    <li class="divider"></li>
    <li><?php echo '<a href="' . KUUZU::link('account.php') . '">' . KUUZU::getDef('module_navbar_account') . '</a>'; ?></li>
    <li><?php echo '<a href="' . KUUZU::link('account_history.php') . '">' . KUUZU::getDef('module_navbar_account_history') . '</a>'; ?></li>
    <li><?php echo '<a href="' . KUUZU::link('address_book.php') . '">' . KUUZU::getDef('module_navbar_account_address_book') . '</a>'; ?></li>
    <li><?php echo '<a href="' . KUUZU::link('account_password.php') . '">' . KUUZU::getDef('module_navbar_account_password') . '</a>'; ?></li>
  </ul>
</li>