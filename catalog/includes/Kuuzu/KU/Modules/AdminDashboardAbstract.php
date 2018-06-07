<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Modules;

use Kuuzu\KU\Registry;

abstract class AdminDashboardAbstract implements \Kuuzu\KU\Modules\AdminDashboardInterface
{
    public $code;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    protected $db;

    abstract protected function init();
    abstract public function getOutput();
    abstract public function install();
    abstract public function keys();

    final public function __construct()
    {
        $this->code = (new \ReflectionClass($this))->getShortName();

        $this->db = Registry::get('Db');

        $this->init();
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function check() {
        return isset($this->sort_order);
    }

    public function remove() {
        return $this->db->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }
}
