<?php
// in a template so that shopowners
// don't have to change the main file!

use Kuuzu\KU\KUUZU;
?>

<?=
  KUUZU::getDef('module_navbar_home_public_text', [
    'store_url' => KUUZU::link('index.php')
  ]);
?>
