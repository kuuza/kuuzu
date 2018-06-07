<?php
use Kuuzu\KU\KUUZU;

?>
<div class="col-sm-<?php echo $content_width; ?> reviews">
  <h4 class="page-header"><?php echo KUUZU::getDef('reviews_text_title'); ?></h4>
  <?php echo $review_data; ?>
</div>

