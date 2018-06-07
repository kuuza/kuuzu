<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Is;

class ip_address
{
    public static function execute($ip)
    {
        $ip = trim($ip);

        return !empty($ip) && filter_var($ip, FILTER_VALIDATE_IP, [
            'flags' => FILTER_FLAG_IPV4
        ]);
    }
}
