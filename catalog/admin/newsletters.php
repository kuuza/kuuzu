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

  if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $_GET['page'] = 1;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'lock':
      case 'unlock':
        $newsletter_id = HTML::sanitize($_GET['nID']);
        $status = (($action == 'lock') ? '1' : '0');

        $KUUZU_Db->save('newsletters', ['locked' => $status], ['newsletters_id' => (int)$newsletter_id]);

        KUUZU::redirect(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
        break;
      case 'insert':
      case 'update':
        if (isset($_POST['newsletter_id'])) $newsletter_id = HTML::sanitize($_POST['newsletter_id']);
        $newsletter_module = HTML::sanitize($_POST['module']);

        $allowed = array_map(function($v) {return basename($v, '.php');}, glob('includes/modules/newsletters/*.php'));
        if (!in_array($newsletter_module, $allowed)) {
          $KUUZU_MessageStack->add(KUUZU::getDef('error_newsletter_module_not_exists'), 'error');
          $newsletter_error = true;
        }

        $title = HTML::sanitize($_POST['title']);
        $content = $_POST['content'];
        $content_html = $_POST['content_html'];

        $newsletter_error = false;
        if (empty($title)) {
          $KUUZU_MessageStack->add(KUUZU::getDef('error_newsletter_title'), 'error');
          $newsletter_error = true;
        }

        if (empty($newsletter_module)) {
          $KUUZU_MessageStack->add(KUUZU::getDef('error_newsletter_module'), 'error');
          $newsletter_error = true;
        }

        if ($newsletter_error == false) {
          $sql_data_array = array('title' => $title,
                                  'content' => $content,
                                  'content_html' => $content_html,
                                  'module' => $newsletter_module);

          if ($action == 'insert') {
            $sql_data_array['date_added'] = 'now()';
            $sql_data_array['status'] = '0';
            $sql_data_array['locked'] = '0';

            $KUUZU_Db->save('newsletters', $sql_data_array);
            $newsletter_id = $KUUZU_Db->lastInsertId();
          } elseif ($action == 'update') {
            $KUUZU_Db->save('newsletters', $sql_data_array, ['newsletters_id' => (int)$newsletter_id]);
          }

          KUUZU::redirect(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $newsletter_id);
        } else {
          $action = 'new';
        }
        break;
      case 'deleteconfirm':
        $newsletter_id = HTML::sanitize($_GET['nID']);

        $KUUZU_Db->delete('newsletters', ['newsletters_id' => (int)$newsletter_id]);

        KUUZU::redirect(FILENAME_NEWSLETTERS, 'page=' . $_GET['page']);
        break;
      case 'delete':
      case 'new': if (!isset($_GET['nID'])) break;
      case 'send':
      case 'confirm_send':
        $newsletter_id = HTML::sanitize($_GET['nID']);

        $Qcheck = $KUUZU_Db->get('newsletters', 'locked', ['newsletters_id' => (int)$newsletter_id]);

        if ($Qcheck->fetch() !== false) {
          if ($Qcheck->valueInt('locked') < 1) {
            switch ($action) {
              case 'delete': $error = KUUZU::getDef('error_remove_unlocked_newsletter'); break;
              case 'new': $error = KUUZU::getDef('error_edit_unlocked_newsletter'); break;
              case 'send': $error = KUUZU::getDef('error_send_unlocked_newsletter'); break;
              case 'confirm_send': $error = KUUZU::getDef('error_send_unlocked_newsletter'); break;
            }

            $KUUZU_MessageStack->add($error, 'error');

            KUUZU::redirect(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
          }
        }
        break;
    }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo KUUZU::getDef('heading_title'); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if ($action == 'new') {
    $form_action = 'insert';

    $parameters = array('title' => '',
                        'content' => '',
                        'content_html' => '',
                        'module' => '');

    $nInfo = new objectInfo($parameters);

    if (isset($_GET['nID'])) {
      $form_action = 'update';

      $nID = HTML::sanitize($_GET['nID']);

      $Qnewsletter = $KUUZU_Db->get('newsletters', [
        'title',
        'content',
        'content_html',
        'module'
      ], [
        'newsletters_id' => (int)$nID
      ]);

      $nInfo->objectInfo($Qnewsletter->toArray());
    } elseif ($_POST) {
      $nInfo->objectInfo($_POST);
    }

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $directory_array = array();
    if ($dir = dir('includes/modules/newsletters/')) {
      while ($file = $dir->read()) {
        if (!is_dir('includes/modules/newsletters/' . $file)) {
          if (substr($file, strrpos($file, '.')) == $file_extension) {
            $directory_array[] = $file;
          }
        }
      }
      sort($directory_array);
      $dir->close();
    }

    for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
      $modules_array[] = array('id' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')), 'text' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')));
    }
?>
      <tr><?php echo HTML::form('newsletter', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&action=' . $form_action)); if ($form_action == 'update') echo HTML::hiddenField('newsletter_id', $nID); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo KUUZU::getDef('text_newsletter_module'); ?></td>
            <td class="main"><?php echo HTML::selectField('module', $modules_array, $nInfo->module); ?></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td class="main"><?php echo KUUZU::getDef('text_newsletter_title'); ?></td>
            <td class="main"><?php echo HTML::inputField('title', $nInfo->title) . KUUZU::getDef('text_field_required'); ?></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo KUUZU::getDef('text_newsletter_content'); ?></td>
            <td class="main">
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#html_email" aria-controls="html_email" role="tab" data-toggle="tab"><?= KUUZU::getDef('email_type_html'); ?></a></li>
                <li role="presentation"><a href="#plain_email" aria-controls="plain_email" role="tab" data-toggle="tab"><?= KUUZU::getDef('email_type_plain'); ?></a></li>
              </ul>

              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="html_email">
                  <?= HTML::textareaField('content_html', '60', '15', $nInfo->content_html); ?>
                </div>

                <div role="tabpanel" class="tab-pane" id="plain_email">
                  <?= HTML::textareaField('content', '60', '15', $nInfo->content); ?>
                </div>
              </div>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" align="right"><?php echo HTML::button(KUUZU::getDef('image_save'), 'fa fa-save') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&' . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] : ''))); ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } elseif ($action == 'preview') {
    $nID = HTML::sanitize($_GET['nID']);

    $Qnewsletter = $KUUZU_Db->get('newsletters', [
      'title',
      'content',
      'content_html',
      'module'
    ], [
      'newsletters_id' => (int)$nID
    ]);

    $nInfo = new objectInfo($Qnewsletter->toArray());
?>
      <tr>
        <td class="smallText" align="right"><?php echo HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'])); ?></td>
      </tr>
      <tr>
        <td>
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#html_preview" aria-controls="html_preview" role="tab" data-toggle="tab"><?= KUUZU::getDef('email_type_html'); ?></a></li>
            <li role="presentation"><a href="#plain_preview" aria-controls="plain_preview" role="tab" data-toggle="tab"><?= KUUZU::getDef('email_type_plain'); ?></a></li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="html_preview">
              <iframe id="newsletterHtmlPreviewContent" style="width: 100%; height: 400px; border: 0;"></iframe>

              <script id="newsletterHtmlPreview" type="x-tmpl-mustache">
                <?= HTML::outputProtected($nInfo->content_html); ?>
              </script>

              <script>
                $(function() {
                  var content = $('<div />').html($('#newsletterHtmlPreview').html()).text();
                  $('#newsletterHtmlPreviewContent').contents().find('html').html(content);
                });
              </script>
            </div>

            <div role="tabpanel" class="tab-pane" id="plain_preview">
              <?= nl2br(HTML::outputProtected($nInfo->content)); ?>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="smallText" align="right"><?php echo HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'])); ?></td>
      </tr>
<?php
  } elseif ($action == 'send') {
    $nID = HTML::sanitize($_GET['nID']);

    $Qnewsletter = $KUUZU_Db->get('newsletters', [
      'title',
      'content',
      'content_html',
      'module'
    ], [
      'newsletters_id' => (int)$nID
    ]);

    $nInfo = new objectInfo($Qnewsletter->toArray());

    $KUUZU_Language->loadDefinitions('modules/newsletters/' . $nInfo->module);
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content, $nInfo->content_html);
?>
      <tr>
        <td><?php if ($module->show_choose_audience) { echo $module->choose_audience(); } else { echo $module->confirm(); } ?></td>
      </tr>
<?php
  } elseif ($action == 'confirm') {
    $nID = HTML::sanitize($_GET['nID']);

    $Qnewsletter = $KUUZU_Db->get('newsletters', [
      'title',
      'content',
      'content_html',
      'module'
    ], [
      'newsletters_id' => (int)$nID
    ]);

    $nInfo = new objectInfo($Qnewsletter->toArray());

    $KUUZU_Language->loadDefinitions('modules/newsletters/' . $nInfo->module);
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content, $nInfo->content_html);
?>
      <tr>
        <td><?php echo $module->confirm(); ?></td>
      </tr>
<?php
  } elseif ($action == 'confirm_send') {
    $nID = HTML::sanitize($_GET['nID']);

    $Qnewsletter = $KUUZU_Db->get('newsletters', [
      'newsletters_id',
      'title',
      'content',
      'content_html',
      'module'
    ], [
      'newsletters_id' => (int)$nID
    ]);

    $nInfo = new objectInfo($Qnewsletter->toArray());

    $KUUZU_Language->loadDefinitions('modules/newsletters/' . $nInfo->module);
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content, $nInfo->content_html);
?>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" valign="middle"><?php echo HTML::image(KUUZU::linkImage('ani_send_email.gif'), KUUZU::getDef('image_ani_send_email')); ?></td>
            <td class="main" valign="middle"><strong><?php echo KUUZU::getDef('text_please_wait'); ?></strong></td>
          </tr>
        </table></td>
      </tr>
<?php
  tep_set_time_limit(0);
  flush();
  $module->send($nInfo->newsletters_id);
?>
      <tr>
        <td class="main"><font color="#ff0000"><strong><?php echo KUUZU::getDef('text_finished_sending_emails'); ?></strong></font></td>
      </tr>
      <tr>
        <td class="smallText"><?php echo HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'])); ?></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo KUUZU::getDef('table_heading_newsletters'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_size'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_module'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo KUUZU::getDef('table_heading_sent'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo KUUZU::getDef('table_heading_status'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo KUUZU::getDef('table_heading_action'); ?>&nbsp;</td>
              </tr>
<?php
    $Qnewsletters = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS newsletters_id, title, length(content) as content_length, module, date_added, date_sent, status, locked from :table_newsletters order by date_added desc limit :page_set_offset, :page_set_max_results');
    $Qnewsletters->setPageSet(MAX_DISPLAY_SEARCH_RESULTS);
    $Qnewsletters->execute();

    while ($Qnewsletters->fetch()) {
    if ((!isset($_GET['nID']) || (isset($_GET['nID']) && ((int)$_GET['nID'] === $Qnewsletters->valueInt('newsletters_id')))) && !isset($nInfo) && (substr($action, 0, 3) != 'new')) {
        $nInfo = new objectInfo($Qnewsletters->toArray());
      }

      if (isset($nInfo) && is_object($nInfo) && ($Qnewsletters->valueInt('newsletters_id') === (int)$nInfo->newsletters_id) ) {
        echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=preview') . '">' . HTML::image(KUUZU::linkImage('icons/preview.gif'), KUUZU::getDef('icon_preview')) . '</a>&nbsp;' . $Qnewsletters->value('title'); ?></td>
                <td class="dataTableContent" align="right"><?php echo number_format($Qnewsletters->valueInt('content_length')) . ' bytes'; ?></td>
                <td class="dataTableContent" align="right"><?php echo $Qnewsletters->value('module'); ?></td>
                <td class="dataTableContent" align="center"><?php if ($Qnewsletters->valueInt('status') === 1) { echo HTML::image(KUUZU::linkImage('icons/tick.gif'), KUUZU::getDef('icon_tick')); } else { echo HTML::image(KUUZU::linkImage('icons/cross.gif'), KUUZU::getDef('icon_cross')); } ?></td>
                <td class="dataTableContent" align="center"><?php if ($Qnewsletters->valueInt('locked') > 0) { echo HTML::image(KUUZU::linkImage('icons/locked.gif'), KUUZU::getDef('icon_locked')); } else { echo HTML::image(KUUZU::linkImage('icons/unlocked.gif'), KUUZU::getDef('icon_unlocked')); } ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($nInfo) && is_object($nInfo) && ($Qnewsletters->valueInt('newsletters_id') === (int)$nInfo->newsletters_id) ) { echo HTML::image(KUUZU::linkImage('icon_arrow_right.gif'), ''); } else { echo '<a href="' . KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $Qnewsletters->valueInt('newsletters_id')) . '">' . HTML::image(KUUZU::linkImage('icon_info.gif'), KUUZU::getDef('image_icon_info')) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qnewsletters->getPageSetLabel(KUUZU::getDef('text_display_number_of_newsletters')); ?></td>
                    <td class="smallText" align="right"><?php echo $Qnewsletters->getPageSetLinks(); ?></td>
                  </tr>
                  <tr>
                    <td class="smallText" align="right" colspan="2"><?php echo HTML::button(KUUZU::getDef('image_new_newsletter'), 'fa fa-plus', KUUZU::link(FILENAME_NEWSLETTERS, 'action=new')); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<strong>' . $nInfo->title . '</strong>');

      $contents = array('form' => HTML::form('newsletters', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=deleteconfirm')));
      $contents[] = array('text' => KUUZU::getDef('text_info_delete_intro'));
      $contents[] = array('text' => '<br /><strong>' . $nInfo->title . '</strong>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash') . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'])));
      break;
    default:
      if (isset($nInfo) && is_object($nInfo)) {
        $heading[] = array('text' => '<strong>' . $nInfo->title . '</strong>');

        if ($nInfo->locked > 0) {
          $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_edit'), 'fa fa-edit', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=new')) . HTML::button(KUUZU::getDef('image_delete'), 'fa fa-trash', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=delete')) . HTML::button(KUUZU::getDef('image_preview'), 'fa fa-file-o', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview')) . HTML::button(KUUZU::getDef('image_send'), 'fa fa-envelope', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=send')) . HTML::button(KUUZU::getDef('image_unlock'), 'fa fa-unlock', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=unlock')));
        } else {
          $contents[] = array('align' => 'center', 'text' => HTML::button(KUUZU::getDef('image_preview'), 'fa fa-file-o', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview')) . HTML::button(KUUZU::getDef('image_lock'), 'fa fa-lock', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=lock')));
        }
        $contents[] = array('text' => '<br />' . KUUZU::getDef('text_newsletter_date_added') . ' ' . DateTime::toShort($nInfo->date_added));
        if ($nInfo->status == '1') $contents[] = array('text' => KUUZU::getDef('text_newsletter_date_sent') . ' ' . DateTime::toShort($nInfo->date_sent));
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
