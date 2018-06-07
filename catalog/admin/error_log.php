<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\DateTime;
  use Kuuzu\KU\ErrorHandler;
  use Kuuzu\KU\FileSystem;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $files = [];

  foreach (glob(ErrorHandler::getDirectory() . 'errors-*.txt') as $f) {
    $key = basename($f, '.txt');

    if (preg_match('/^errors-([0-9]{4})([0-9]{2})([0-9]{2})$/', $key, $matches)) {
      $files[$key] = [
        'path' => $f,
        'key' => $key,
        'date' => DateTime::toShort($matches[1] . '-' . $matches[2] . '-' . $matches[3]),
        'size' => filesize($f)
      ];
    }
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'delete':
        if (isset($_GET['log']) && array_key_exists($_GET['log'], $files)) {
          if (unlink($files[$_GET['log']]['path'])) {
            $KUUZU_MessageStack->add(KUUZU::getDef('ms_success_delete', [
              'log' => $files[$_GET['log']]['key']
            ]), 'success');
          } else {
            $KUUZU_MessageStack->add(KUUZU::getDef('ms_error_delete', [
              'log' => $files[$_GET['log']]['key']
            ]), 'error');
          }
        }

        KUUZU::redirect('error_log.php');
        break;

      case 'deleteAll':
        $result = true;

        foreach ($files as $f) {
          if (!unlink($f['path'])) {
            $result = false;
          }
        }

        if ($result === true) {
          $KUUZU_MessageStack->add(KUUZU::getDef('ms_success_delete_all'), 'success');
        } else {
          $KUUZU_MessageStack->add(KUUZU::getDef('ms_error_delete_all'), 'success');
        }

        KUUZU::redirect('error_log.php');
        break;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));

  if (($action == 'view') && isset($_GET['log']) && array_key_exists($_GET['log'], $files)) {
    $log = $files[$_GET['log']];
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link('error_log.php'), null, 'btn-info') . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash-o', KUUZU::link('error_log.php', 'action=delete&log=' . $log['key']), null, 'btn-danger'); ?>
</div>

<h2><i class="fa fa-exclamation-circle"></i> <a href="<?= KUUZU::link('error_log.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<h3><?= HTML::outputProtected($log['date']); ?></h3>

<p>
  <?= HTML::textareaField('log', '100', '30', file_get_contents($log['path']), 'readonly', false); ?>
</p>

<?php
  } else {
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('button_delete_all'), 'fa fa-trash', KUUZU::link('error_log.php', 'action=deleteAll'), null, 'btn-danger'); ?>
</div>

<h2><i class="fa fa-exclamation-circle"></i> <a href="<?= KUUZU::link('error_log.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<table class="kuuzu-table table table-hover">
  <thead>
    <tr class="info">
      <th><?= KUUZU::getDef('table_heading_filename'); ?></th>
      <th class="text-right"><?= KUUZU::getDef('table_heading_filesize'); ?></th>
      <th class="action"></th>
    </tr>
  </thead>
  <tbody>

<?php
    foreach ($files as $f) {
?>

    <tr>
      <td><?= $f['date']; ?></td>
      <td class="text-right"><?= $f['size']; ?></td>
      <td class="action"><a href="<?= KUUZU::link('error_log.php', 'action=view&log=' . $f['key']); ?>"><i class="fa fa-file-text-o" title="<?= KUUZU::getDef('button_view'); ?>"></i></a></td>
    </tr>

<?php
    }
?>

  </tbody>
</table>

<p>
  <?=
    KUUZU::getDef('log_directory', [
      'path' => FileSystem::displayPath(ErrorHandler::getDirectory())
    ]);
  ?>
</p>

<?php
  }

  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
