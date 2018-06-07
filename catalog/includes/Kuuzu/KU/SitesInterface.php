<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

interface SitesInterface
{
    public function hasPage();
    public function getPage();
    public function setPage();
    public static function resolveRoute(array $route, array $routes);
}
