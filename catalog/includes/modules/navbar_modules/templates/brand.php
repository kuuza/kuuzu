<?php
// in a template so that shopowners
// don't have to change the main file!

use Kuuzu\KU\KUUZU;
?>

<?=
  KUUZU::getDef('module_navbar_brand_public_text', [
    'store_url' => KUUZU::link('index.php'),
    'store_name' => STORE_NAME
  ]);
?>
