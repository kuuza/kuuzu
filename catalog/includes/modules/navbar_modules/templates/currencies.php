<?php
use Kuuzu\KU\KUUZU;

if (isset($currencies) && is_object($currencies)) {
?>

<li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
  <span class="fa fa-fw fa-money"></span>
  <?php echo KUUZU::getDef('module_navbar_currencies_selected_currency', ['currency' => $_SESSION['currency']]); ?>
  </a>

  <ul class="dropdown-menu">

<?php
  foreach ($currencies->currencies as $key => $value) {
    echo '<li><a href="' . KUUZU::link($PHP_SELF, tep_get_all_get_params(array('language', 'currency')) . 'currency=' . $key) . '">' . $value['title'] . '</a></li>';
  }
?>

  </ul>
</li>

<?php
}
?>
