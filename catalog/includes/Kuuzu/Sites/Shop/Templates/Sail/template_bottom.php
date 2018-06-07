      </div> <!-- bodyContent //-->

<?php
  if ($kuuTemplate->hasBlocks('boxes_column_left')) {
?>

      <div id="columnLeft" class="col-md-<?php echo $kuuTemplate->getGridColumnWidth(); ?>  col-md-pull-<?php echo $kuuTemplate->getGridContentWidth(); ?>">
        <?php echo $kuuTemplate->getBlocks('boxes_column_left'); ?>
      </div>

<?php
  }

  if ($kuuTemplate->hasBlocks('boxes_column_right')) {
?>

      <div id="columnRight" class="col-md-<?php echo $kuuTemplate->getGridColumnWidth(); ?>">
        <?php echo $kuuTemplate->getBlocks('boxes_column_right'); ?>
      </div>

<?php
  }
?>

    </div> <!-- row -->

  </div> <!-- bodyWrapper //-->

  <?php require($kuuTemplate->getFile('footer.php')); ?>

<script src="ext/bootstrap/js/bootstrap.min.js"></script>
<?php echo $kuuTemplate->getBlocks('footer_scripts'); ?>

</body>
</html>
