<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

abstract class ModulesAbstract
{
    public $code;
    protected $interface;
    protected $ns = 'Kuuzu\Apps\\';

    abstract public function getInfo($app, $key, $data);
    abstract public function getClass($module);

    final public function __construct()
    {
        $this->code = (new \ReflectionClass($this))->getShortName();

        $this->init();
    }

    protected function init()
    {
    }

    public function filter($modules, $filter)
    {
        return $modules;
    }
}
