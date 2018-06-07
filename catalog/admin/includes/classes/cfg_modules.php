<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class cfg_modules {
    var $_modules = array();

    protected $lang;

    function cfg_modules() {
      global $PHP_SELF;

      $this->lang = Registry::get('Language');

      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      $directory = KUUZU::getConfig('dir_root') . 'includes/modules/cfg_modules';

      if ($dir = @dir($directory)) {
        while ($file = $dir->read()) {
          if (!is_dir($directory . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $class = substr($file, 0, strrpos($file, '.'));

              $this->lang->loadDefinitions('modules/cfg_modules/' . pathinfo($file, PATHINFO_FILENAME));

              include(KUUZU::getConfig('dir_root') . 'includes/modules/cfg_modules/' . $class . '.php');

              $m = new $class();

              $this->_modules[] = array('code' => $m->code,
                                        'directory' => $m->directory,
                                        'language_directory' => $m->language_directory,
                                        'key' => $m->key,
                                        'title' => $m->title,
                                        'template_integration' => $m->template_integration,
                                        'site' => $m->site);
            }
          }
        }
      }
    }

    function getAll() {
      return $this->_modules;
    }

    function get($code, $key) {
      foreach ($this->_modules as $m) {
        if ($m['code'] == $code) {
          return $m[$key];
        }
      }
    }

    function exists($code) {
      foreach ($this->_modules as $m) {
        if ($m['code'] == $code) {
          return true;
        }
      }

      return false;
    }
  }
?>
