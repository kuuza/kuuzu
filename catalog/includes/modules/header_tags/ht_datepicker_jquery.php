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

  class ht_datepicker_jquery {
    var $code = 'ht_datepicker_jquery';
    var $group = 'footer_scripts';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = KUUZU::getDef('module_header_tags_datepicker_jquery_title');
      $this->description = KUUZU::getDef('module_header_tags_datepicker_jquery_description');

      if ( defined('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $kuuTemplate;

      if (tep_not_null(MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES)) {
        $pages_array = array();

        foreach (explode(';', MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES) as $page) {
          $page = trim($page);

          if (!empty($page)) {
            $pages_array[] = $page;
          }
        }

        if (in_array(basename($PHP_SELF), $pages_array)) {
          $kuuTemplate->addBlock('<script src="ext/datepicker/js/bootstrap-datepicker.js"></script>' . "\n", $this->group);
          $kuuTemplate->addBlock('<link rel="stylesheet" href="ext/datepicker/css/datepicker.css" />' . "\n", 'header_tags');
          $kuuTemplate->addBlock('<script>$(\'input[data-provide="datepicker"]\').datepicker({format: \'' . KUUZU::getDef('js_date_format') . '\',viewMode: 2});</script>', $this->group);
          // advanced search
          $kuuTemplate->addBlock('<script>var nowTemp = new Date(); var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0); $(\'#dfrom\').datepicker({format: \'' . KUUZU::getDef('js_date_format') . '\',onRender: function(date) {return date.valueOf() > now.valueOf() ? \'disabled\' : \'\';}}); </script>', $this->group);
          $kuuTemplate->addBlock('<script>$(\'#dto\').datepicker({format: \'' . KUUZU::getDef('js_date_format') . '\',onRender: function(date) {return date.valueOf() > now.valueOf() ? \'disabled\' : \'\';}});</script>', $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS');
    }

    function install() {
      $KUUZU_Db = Registry::get('Db');

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Enable Datepicker jQuery Module',
        'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS',
        'configuration_value' => 'True',
        'configuration_description' => 'Do you want to enable the Datepicker module?',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'set_function' => 'tep_cfg_select_option(array(\'True\', \'False\'), ',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Pages',
        'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES',
        'configuration_value' => implode(';', $this->get_default_pages()),
        'configuration_description' => 'The pages to add the Datepicker jQuery Scripts to.',
        'configuration_group_id' => '6',
        'sort_order' => '1',
        'use_function' => 'ht_datepicker_jquery_show_pages',
        'set_function' => 'ht_datepicker_jquery_edit_pages(',
        'date_added' => 'now()'
      ]);

      $KUUZU_Db->save('configuration', [
        'configuration_title' => 'Sort Order',
        'configuration_key' => 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER',
        'configuration_value' => '900',
        'configuration_description' => 'Sort order of display. Lowest is displayed first.',
        'configuration_group_id' => '6',
        'sort_order' => '0',
        'date_added' => 'now()'
      ]);
    }

    function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_DATEPICKER_JQUERY_STATUS', 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_PAGES', 'MODULE_HEADER_TAGS_DATEPICKER_JQUERY_SORT_ORDER');
    }

    function get_default_pages() {
      return array('advanced_search.php',
                   'account_edit.php',
                   'create_account.php');
    }
  }

  function ht_datepicker_jquery_show_pages($text) {
    return nl2br(implode("\n", explode(';', $text)));
  }

  function ht_datepicker_jquery_edit_pages($values, $key) {
    global $PHP_SELF;

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $files_array = array();
	  if ($dir = @dir(KUUZU::getConfig('dir_root', 'Shop'))) {
	    while ($file = $dir->read()) {
	      if (!is_dir(KUUZU::getConfig('dir_root', 'Shop') . $file)) {
	        if (substr($file, strrpos($file, '.')) == $file_extension) {
            $files_array[] = $file;
          }
        }
      }
      sort($files_array);
      $dir->close();
    }

    $values_array = explode(';', $values);

    $output = '';
    foreach ($files_array as $file) {
      $output .= HTML::checkboxField('ht_datepicker_jquery_file[]', $file, in_array($file, $values_array)) . '&nbsp;' . HTML::output($file) . '<br />';
    }

    if (!empty($output)) {
      $output = '<br />' . substr($output, 0, -6);
    }

    $output .= HTML::hiddenField('configuration[' . $key . ']', '', 'id="htrn_files"');

    $output .= '<script>
                function htrn_update_cfg_value() {
                  var htrn_selected_files = \'\';

                  if ($(\'input[name="ht_datepicker_jquery_file[]"]\').length > 0) {
                    $(\'input[name="ht_datepicker_jquery_file[]"]:checked\').each(function() {
                      htrn_selected_files += $(this).attr(\'value\') + \';\';
                    });

                    if (htrn_selected_files.length > 0) {
                      htrn_selected_files = htrn_selected_files.substring(0, htrn_selected_files.length - 1);
                    }
                  }

                  $(\'#htrn_files\').val(htrn_selected_files);
                }

                $(function() {
                  htrn_update_cfg_value();

                  if ($(\'input[name="ht_datepicker_jquery_file[]"]\').length > 0) {
                    $(\'input[name="ht_datepicker_jquery_file[]"]\').change(function() {
                      htrn_update_cfg_value();
                    });
                  }
                });
                </script>';

    return $output;
  }
?>
