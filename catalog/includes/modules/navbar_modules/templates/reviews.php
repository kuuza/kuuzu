<?php
// in a template so that shopowners
// don't have to change the main file!

use Kuuzu\KU\KUUZU;
?>

<?=
  KUUZU::getDef('module_navbar_reviews_public_text', [
    'reviews_url' => KUUZU::link('reviews.php')
  ]);
?>
