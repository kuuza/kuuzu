<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\DateTime;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $directory_array = array();
  if ($dir = @dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/')) {
    while ($file = $dir->read()) {
      if (!is_dir(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    if ($KUUZU_Language->definitionsExist('Shop/modules/action_recorder/' . pathinfo($file, PATHINFO_FILENAME))) {
      $KUUZU_Language->loadDefinitions('Shop/modules/action_recorder/' . pathinfo($file, PATHINFO_FILENAME));
    }

    include(KUUZU::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (class_exists($class)) {
      $GLOBALS[$class] = new $class;
    }
  }

  $modules_array = array();
  $modules_list_array = array(array('id' => '', 'text' => KUUZU::getDef('text_all_modules')));

  $Qmodules = $KUUZU_Db->get('action_recorder', 'distinct module', null, 'module');

  while ($Qmodules->fetch()) {
    $modules_array[] = $Qmodules->value('module');

    $modules_list_array[] = [
      'id' => $Qmodules->value('module'),
      'text' => (is_object($GLOBALS[$Qmodules->value('module')]) ? $GLOBALS[$Qmodules->value('module')]->title : $Qmodules->value('module'))
    ];
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'expire':
        $expired_entries = 0;

        if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
          if (is_object($GLOBALS[$_GET['module']])) {
            $expired_entries += $GLOBALS[$_GET['module']]->expireEntries();
          } else {
            $expired_entries = $KUUZU_Db->delete('action_recorder', [
              'module' => $_GET['module']
            ]);
          }
        } else {
          foreach ($modules_array as $module) {
            if (is_object($GLOBALS[$module])) {
              $expired_entries += $GLOBALS[$module]->expireEntries();
            }
          }
        }

        $KUUZU_MessageStack->add(KUUZU::getDef('success_expired_entries', ['expired_entries' =>  $expired_entries]), 'success');

        KUUZU::redirect(FILENAME_ACTION_RECORDER);

        break;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link('action_recorder.php', 'action=expire' . (isset($_GET['module']) && in_array($_GET['module'], $modules_array) ? '&module=' . $_GET['module'] : '')), null, 'btn-danger'); ?>
</div>

<h2><i class="fa fa-tasks"></i> <a href="<?= KUUZU::link('action_recorder.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<?php
  echo HTML::form('search', KUUZU::link('action_recorder.php'), 'get', 'class="form-inline"', ['session_id' => true]) .
       HTML::inputField('search', null, 'placeholder="' . KUUZU::getDef('text_filter_search') . '"') .
       HTML::selectField('module', $modules_list_array, null, 'onchange="this.form.submit();"') .
       '</form>';
?>

<table class="kuuzu-table table table-hover">
  <thead>
    <tr class="info">
      <th><?= KUUZU::getDef('table_heading_module'); ?></th>
      <th><?= KUUZU::getDef('table_heading_customer'); ?></th>
      <th><?= KUUZU::getDef('table_heading_identifier'); ?></th>
      <th class="text-right"><?= KUUZU::getDef('table_heading_date_added'); ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  $filter = array();

  if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
    $filter[] = 'module = :module';
  }

  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filter[] = 'identifier like :identifier';
  }

  $sql_query = 'select SQL_CALC_FOUND_ROWS * from :table_action_recorder';

  if (!empty($filter)) {
    $sql_query .= ' where ' . implode(' and ', $filter);
  }

  $sql_query .= ' order by date_added desc limit :page_set_offset, :page_set_max_results';

  $Qactions = $KUUZU_Db->prepare($sql_query);

  if (!empty($filter)) {
    if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
      $Qactions->bindValue(':module', $_GET['module']);
    }

    if (isset($_GET['search']) && !empty($_GET['search'])) {
      $Qactions->bindValue(':identifier', '%' . $_GET['search'] . '%');
    }
  }

  $Qactions->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
  $Qactions->execute();

  while ($Qactions->fetch()) {
    $module = $Qactions->value('module');

    $module_title = $Qactions->value('module');
    if (is_object($GLOBALS[$module])) {
      $module_title = $GLOBALS[$module]->title;
    }
?>

    <tr>
      <td><i class="fa <?= (($Qactions->valueInt('success') === 1) ? 'fa-check text-success' : 'fa-times text-danger'); ?>"></i> <?= $module_title; ?></td>
      <td><?= $Qactions->valueProtected('user_name') . ' [' . $Qactions->valueInt('user_id') . ']'; ?></td>
      <td><?= (tep_not_null($Qactions->value('identifier')) ? '<a href="' . KUUZU::link('action_recorder.php', 'search=' . $Qactions->value('identifier')) . '"><u>' . $Qactions->valueProtected('identifier') . '</u></a>': '(empty)'); ?></td>
      <td class="text-right"><?= DateTime::toShort($Qactions->value('date_added'), true); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<div>
  <span class="pull-right"><?= $Qactions->getPageSetLinks((isset($_GET['module']) && in_array($_GET['module'], $modules_array) && is_object($GLOBALS[$_GET['module']]) ? 'module=' . $_GET['module'] : null) . '&' . (isset($_GET['search']) && !empty($_GET['search']) ? 'search=' . $_GET['search'] : null)); ?></span>
  <?= $Qactions->getPageSetLabel(KUUZU::getDef('text_display_number_of_entries')); ?>
</div>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
