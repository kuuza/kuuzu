<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

abstract class SitesAbstract implements \Kuuzu\KU\SitesInterface
{
    protected $code;
    protected $page;
    protected $app;
    protected $route;
    public $actions_index = 1;

    abstract protected function init();
    abstract public function setPage();

    final public function __construct()
    {
        $this->code = (new \ReflectionClass($this))->getShortName();

        return $this->init();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function hasPage()
    {
        return isset($this->page);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public static function resolveRoute(array $route, array $routes)
    {
    }
}
