<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Cache;
  use Kuuzu\KU\FileSystem;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'reset':
        Cache::clear($_GET['block']);
        break;

      case 'resetAll':
        Cache::clearAll();
        break;
    }

    KUUZU::redirect(FILENAME_CACHE);
  }

// check if the cache directory exists
  if (is_dir(Cache::getPath())) {
    if (!FileSystem::isWritable(Cache::getPath())) $KUUZU_MessageStack->add(KUUZU::getDef('error_cache_directory_not_writeable'), 'error');
  } else {
    $KUUZU_MessageStack->add(KUUZU::getDef('error_cache_directory_does_not_exist'), 'error');
  }

  $cache_files = [];

  foreach (glob(Cache::getPath() . '*.cache') as $c) {
    $key = basename($c, '.cache');

    if (($pos = strpos($key, '-')) !== false) {
      $cache_files[substr($key, 0, $pos)][] = $key;
    } else {
      $cache_files[$key][] = $key;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('image_delete'), 'fa fa-recycle', KUUZU::link('cache.php', 'action=resetAll'), null, 'btn-danger'); ?>
</div>

<h2><i class="fa fa-database"></i> <a href="<?= KUUZU::link('cache.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<table class="kuuzu-table table table-hover">
  <thead>
    <tr class="info">
      <th><?= KUUZU::getDef('table_heading_cache'); ?></th>
      <th class="text-right"><?= KUUZU::getDef('table_heading_cache_number_of_files'); ?></th>
      <th class="action"></th>
    </tr>
  </thead>
  <tbody>

<?php
  foreach (array_keys($cache_files) as $key) {
?>

    <tr>
      <td><?= $key; ?></td>
      <td class="text-right"><?= count($cache_files[$key]); ?></td>
      <td class="action"><a href="<?= KUUZU::link(FILENAME_CACHE, 'action=reset&block=' . $key); ?>"><i class="fa fa-recycle" title="<?= KUUZU::getDef('image_delete'); ?>"></i></a></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<p>
  <?= '<strong>' . KUUZU::getDef('text_cache_directory') . '</strong> ' . FileSystem::displayPath(Cache::getPath()); ?>
</p>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
