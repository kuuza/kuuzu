<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Modules;

interface PaymentInterface
{
    public function update_status();
    public function javascript_validation();
    public function selection();
    public function pre_confirmation_check();
    public function confirmation();
    public function process_button();
    public function before_process();
    public function after_process();
    public function get_error();
    public function check();
    public function install();
    public function remove();
    public function keys();
}
