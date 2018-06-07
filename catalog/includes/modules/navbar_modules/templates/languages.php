<?php
use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;
use Kuuzu\KU\Registry;

$KUUZU_Language = Registry::get('Language');
?>

<li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-fw fa-language"></span> <?php echo $KUUZU_Language->get('name') . ' <span class="caret"></span>'; ?></a>

  <ul class="dropdown-menu">

<?php
foreach ($KUUZU_Language->getAll() as $code => $value) {
  echo '<li><a href="' . KUUZU::link($PHP_SELF, tep_get_all_get_params(array('language', 'currency')) . 'language=' . $code) . '">' . $KUUZU_Language->getImage($value['code']) . '&nbsp;' . $value['name'] . '</a></li>';
}
?>

  </ul>
</li>
