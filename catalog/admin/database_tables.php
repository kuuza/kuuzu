<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  require('includes/application_top.php');

  function tep_dt_get_tables() {
    $KUUZU_Db = Registry::get('Db');

    $result = array();

    $Qtables = $KUUZU_Db->query('show table status');

    while ($Qtables->fetch()) {
      $result[] = $Qtables->value('Name');
    }

    return $result;
  }

  $mysql_charsets = [
    [
      'id' => 'auto',
      'text' => KUUZU::getDef('action_utf8_conversion_from_autodetect')
    ]
  ];

  $Qcharsets = $KUUZU_Db->query('show character set');

  while ($Qcharsets->fetch()) {
    $mysql_charsets[] = [
      'id' => $Qcharsets->value('Charset'),
      'text' => KUUZU::getDef('action_utf8_conversion_from', ['char_set' => $Qcharsets->value('Charset')])
    ];
  }

  $action = null;
  $actions = array(array('id' => 'check',
                         'text' => KUUZU::getDef('action_check_tables')),
                   array('id' => 'analyze',
                         'text' => KUUZU::getDef('action_analyze_tables')),
                   array('id' => 'optimize',
                         'text' => KUUZU::getDef('action_optimize_tables')),
                   array('id' => 'repair',
                         'text' => KUUZU::getDef('action_repair_tables')),
                   array('id' => 'utf8',
                         'text' => KUUZU::getDef('action_utf8_conversion')));

  if ( isset($_POST['action']) ) {
    if ( in_array($_POST['action'], array('check', 'analyze', 'optimize', 'repair', 'utf8')) ) {
      if ( isset($_POST['id']) && is_array($_POST['id']) && !empty($_POST['id']) ) {
        $tables = tep_dt_get_tables();

        foreach ( $_POST['id'] as $key => $value ) {
          if ( !in_array($value, $tables) ) {
            unset($_POST['id'][$key]);
          }
        }

        if ( !empty($_POST['id']) ) {
          $action = $_POST['action'];
        }
      }
    }
  }

  switch ( $action ) {
    case 'check':
    case 'analyze':
    case 'optimize':
    case 'repair':
      tep_set_time_limit(0);

      $table_headers = array(KUUZU::getDef('table_heading_table'),
                             KUUZU::getDef('table_heading_msg_type'),
                             KUUZU::getDef('table_heading_msg'),
                             HTML::checkboxField('masterblaster'));

      $table_data = array();

      foreach ( $_POST['id'] as $table ) {
        $current_table = null;

        $Qaction = $KUUZU_Db->query($action . ' table ' . $table);

        while ($Qaction->fetch()) {
          $table_data[] = [
            ($table != $current_table) ? HTML::outputProtected($table) : '',
            $Qaction->valueProtected('Msg_type'),
            $Qaction->valueProtected('Msg_text'),
            ($table != $current_table) ? HTML::checkboxField('id[]', $table, isset($_POST['id']) && in_array($table, $_POST['id'])) : ''
          ];

          $current_table = $table;
        }
      }

      break;

    case 'utf8':
      $charset_pass = false;

      if ( isset($_POST['from_charset']) ) {
        if ( $_POST['from_charset'] == 'auto' ) {
          $charset_pass = true;
        } else {
          foreach ( $mysql_charsets as $c ) {
            if ( $_POST['from_charset'] == $c['id'] ) {
              $charset_pass = true;
              break;
            }
          }
        }
      }

      if ( $charset_pass === false ) {
        KUUZU::redirect('database_tables.php');
      }

      tep_set_time_limit(0);

      if ( isset($_POST['dryrun']) ) {
        $table_headers = array(KUUZU::getDef('table_heading_queries'));
      } else {
        $table_headers = array(KUUZU::getDef('table_heading_table'),
                               KUUZU::getDef('table_heading_msg'),
                               HTML::checkboxField('masterblaster'));
      }

      $table_data = array();

      foreach ( $_POST['id'] as $table ) {
        $result = 'OK';

        $queries = array();

        $Qcols = $KUUZU_Db->query('show full columns from ' . $table);

        while ($Qcols->fetch()) {
          if ( $Qcols->hasValue('Collation') && tep_not_null($Qcols->value('Collation')) ) {
            if ( $_POST['from_charset'] == 'auto' ) {
              $old_charset = substr($Qcols->value('Collation'), 0, strpos($Qcols->value('Collation'), '_'));
            } else {
              $old_charset = $_POST['from_charset'];
            }

            $queries[] = 'update ' . $table . ' set ' . $Qcols->value('Field') . ' = convert(binary convert(' . $Qcols->value('Field') . ' using ' . $old_charset . ') using utf8) where char_length(' . $Qcols->value('Field') . ') = length(convert(binary convert(' . $Qcols->value('Field') . ' using ' . $old_charset . ') using utf8))';
          }
        }

        $query = 'alter table ' . $table . ' convert to character set utf8 collate utf8_unicode_ci';

        if ( isset($_POST['dryrun']) ) {
          $table_data[] = array($query);

          foreach ( $queries as $q ) {
            $table_data[] = array($q);
          }
        } else {
          if ($KUUZU_Db->exec($query) !== false) {
            foreach ( $queries as $q ) {
              if ($KUUZU_Db->exec($q) === false) {
                $result = implode(' - ', $KUUZU_Db->errorInfo());
                break;
              }
            }
          } else {
            $result = implode(' - ', $KUUZU_Db->errorInfo());
          }
        }

        if ( !isset($_POST['dryrun']) ) {
          $table_data[] = array(HTML::outputProtected($table),
                                HTML::outputProtected($result),
                                HTML::checkboxField('id[]', $table, true));
        }
      }

      break;

    default:
      $table_headers = [
        KUUZU::getDef('table_heading_table'),
        KUUZU::getDef('table_heading_rows'),
        KUUZU::getDef('table_heading_size'),
        KUUZU::getDef('table_heading_engine'),
        KUUZU::getDef('table_heading_collation'),
        HTML::checkboxField('masterblaster')
      ];

      $table_data = [];

      $Qstatus = $KUUZU_Db->query('show table status');

      while ($Qstatus->fetch()) {
        $table_data[] = [
          $Qstatus->valueProtected('Name'),
          $Qstatus->valueProtected('Rows'),
          round(($Qstatus->value('Data_length') + $Qstatus->value('Index_length')) / 1024 / 1024, 2) . 'M',
          $Qstatus->valueProtected('Engine'),
          $Qstatus->valueProtected('Collation'),
          HTML::checkboxField('id[]', $Qstatus->value('Name'))
        ];
      }
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

<?php
  if ( isset($action) ) {
    echo '<div style="float: right;">' . HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link('database_tables.php')) . '</div>';
  }
?>

<h1 class="pageHeading"><?php echo KUUZU::getDef('heading_title'); ?></h1>

<?php
  echo HTML::form('sql', KUUZU::link('database_tables.php'));
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">

<?php
  foreach ( $table_headers as $th ) {
    echo '    <td class="dataTableHeadingContent">' . $th . '</td>' . "\n";
  }
?>
  </tr>

<?php
  foreach ( $table_data as $td ) {
    echo '  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";

    foreach ( $td as $data ) {
      echo '    <td class="dataTableContent">' . $data . '</td>' . "\n";
    }

    echo '  </tr>' . "\n";
  }
?>

</table>

<?php
  if ( !isset($_POST['dryrun']) ) {
?>

<div class="main" style="text-align: right;">
  <?php echo '<span class="runUtf8" style="display: none;">' . KUUZU::getDef('action_utf8_dry_run', ['dryrun' => HTML::checkboxField('dryrun')]) . '</span>' . HTML::selectField('action', $actions, '', 'id="sqlActionsMenu"') . '<span class="runUtf8" style="display: none;">&nbsp;' . HTML::selectField('from_charset', $mysql_charsets) . '</span>&nbsp;' . HTML::button(KUUZU::getDef('button_action_go')); ?>
</div>

<?php
  }
?>

</form>

<script type="text/javascript">
$(function() {
  if ( $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').length > 0 ) {
    $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').click(function() {
      $('form[name="sql"] input[type="checkbox"][name="id[]"]').prop('checked', $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').prop('checked'));
    });
  }

  if ( $('#sqlActionsMenu').val() == 'utf8' ) {
    $('.runUtf8').show();
  }

  $('#sqlActionsMenu').change(function() {
    var selected = $(this).val();

    if ( selected == 'utf8' ) {
      $('.runUtf8').show();
    } else {
      $('.runUtf8').hide();
    }
  });
});
</script>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
