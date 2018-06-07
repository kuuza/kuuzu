<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  class cfgm_boxes {
    var $code = 'boxes';
    var $directory;
    var $language_directory;
    var $site = 'Shop';
    var $key = 'MODULE_BOXES_INSTALLED';
    var $title;
    var $template_integration = true;

    function __construct() {
      $this->directory = KUUZU::getConfig('dir_root', $this->site) . 'includes/modules/boxes/';
      $this->language_directory = KUUZU::getConfig('dir_root', $this->site) . 'includes/languages/';
      $this->title = KUUZU::getDef('module_cfg_module_boxes_title');
    }
  }
?>
