<?php
use Kuuzu\KU\KUUZU;
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo KUUZU::getDef('module_boxes_search_box_title'); ?></div>
  <div class="panel-body text-center"><?php echo $form_output; ?></div>
  <div class="panel-footer"><?php echo KUUZU::getDef('module_boxes_search_box_text') . '<br /><a href="' . KUUZU::link('advanced_search.php') . '"><strong>' . KUUZU::getDef('module_boxes_search_box_advanced_search') . '</strong></a>'; ?></div>
</div>
