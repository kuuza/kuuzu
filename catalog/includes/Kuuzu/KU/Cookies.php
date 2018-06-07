<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

use Kuuzu\KU\KUUZU;

class Cookies
{
    protected $domain;
    protected $path;

    public function __construct()
    {
        $this->domain = KUUZU::getConfig('http_cookie_domain');
        $this->path = KUUZU::getConfig('http_cookie_path');
    }

    public function set($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = true, $httponly = true)
    {
        return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly);
    }

    public function del($name, $path = null, $domain = null, $secure = true, $httponly = true)
    {
        if ($this->set($name, '', time() - 3600, $path, $domain, $secure, $httponly)) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }

            return true;
        }

        return false;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
