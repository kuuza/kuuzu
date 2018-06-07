<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Modules;

interface AdminDashboardInterface
{
    public function getOutput();
    public function install();
    public function keys();
    public function isEnabled();
    public function check();
    public function remove();
}
