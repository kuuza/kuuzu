<?php
  use Kuuzu\KU\KUUZU;
?>

</div>

<?php require($kuuTemplate->getFile('footer.php')); ?>

<script>
$(function() {
  var url = document.location.toString();

  if (url.match('#')) {
    if ($('.nav-tabs a[data-target="#' + url.split('#')[1] + '"]').length === 1) {
      $('.nav-tabs a[data-target="#' + url.split('#')[1] + '"]').tab('show');
    }
  }
});
</script>

<script src="<?= KUUZU::link('Shop/ext/bootstrap/js/bootstrap.min.js', '', false); ?>"></script>

<script src="<?= KUUZU::link('Shop/ext/smartmenus/jquery.smartmenus.min.js', '', false); ?>"></script>
<script src="<?= KUUZU::link('Shop/ext/smartmenus/jquery.smartmenus.bootstrap.min.js', '', false); ?>"></script>

<script src="<?= KUUZU::link('Shop/ext/mustache/mustache.min.js', '', false); ?>"></script>

<script src="<?= KUUZU::link('Shop/ext/sortable/sortable.min.js', '', false); ?>"></script>

<script src="<?= KUUZU::link('Shop/ext/chartist/chartist.min.js', '', false); ?>"></script>

</body>
</html>
