<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><a href="<?php echo KUUZU::link('reviews.php'); ?>"><?php echo KUUZU::getDef('module_boxes_reviews_box_title'); ?></a></div>
  <div class="panel-body"><?php echo $reviews_box_contents; ?></div>
</div>

