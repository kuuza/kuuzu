<?php
use Kuuzu\KU\KUUZU;
?>
<div class="col-sm-<?php echo $content_width; ?> text-center-xs copyright">
  <?=
    KUUZU::getDef('footer_text_body', [
      'year' => date('Y'),
      'store_url' => KUUZU::link('index.php'),
      'store_name' => STORE_NAME
    ]);
  ?>
</div>
