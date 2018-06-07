<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Modules;

interface ShippingInterface
{
    public function quote();
    public function check();
    public function install();
    public function remove();
    public function keys();
}
