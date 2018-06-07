<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Module\Hooks\Shop\Cart;

class AdditionalCheckoutButtons
{
    public function display() {
        global $payment_modules;

        return implode('', $payment_modules->checkout_initialization_method());
    }
}
