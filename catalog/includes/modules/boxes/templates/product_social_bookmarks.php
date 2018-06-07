<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo KUUZU::getDef('module_boxes_product_social_bookmarks_box_title'); ?></div>
  <div class="panel-body text-center"><?php echo implode(' ', $social_bookmarks); ?></div>
</div>
