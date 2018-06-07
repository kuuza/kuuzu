<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Is;

class email
{
    public static function execute($email, $disable_dns_check = false)
    {
        $email = trim($email);

        if (!empty($email) && (strlen($email) <= 255) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (($disable_dns_check === false) && (ENTRY_EMAIL_ADDRESS_CHECK == 'true')) {
                $domain = explode('@', $email);

                if (!checkdnsrr($domain[1], 'MX') && !checkdnsrr($domain[1], 'A')) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
