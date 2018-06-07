<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  class cfgm_navbar_modules {
    var $code = 'navbar_modules';
    var $directory;
    var $language_directory;
    var $site = 'Shop';
    var $key = 'MODULE_CONTENT_NAVBAR_INSTALLED';
    var $title;
    var $template_integration = false;

    function __construct() {
      $this->directory = KUUZU::getConfig('dir_root', $this->site) . 'includes/modules/navbar_modules/';
      $this->language_directory = KUUZU::getConfig('dir_root', $this->site) . 'includes/languages/';
      $this->title = KUUZU::getDef('module_cfg_module_content_navbar_title');
    }
  }