<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

use Kuuzu\KU\FileSystem;
use Kuuzu\KU\KUUZU;

class ErrorHandler
{
    public static function initialize()
    {
        ini_set('display_errors', false);
        ini_set('html_errors', false);
        ini_set('ignore_repeated_errors', true);

        if (FileSystem::isWritable(static::getDirectory(), true)) {
            if (!is_dir(static::getDirectory())) {
                mkdir(static::getDirectory(), 0777, true);
            }
        }

        if (FileSystem::isWritable(static::getDirectory())) {
            ini_set('log_errors', true);
            ini_set('error_log', static::getDirectory() . 'errors-' . date('Ymd') . '.txt');
        }
    }

    public static function getDirectory()
    {
        return KUUZU::BASE_DIR . 'Work/Logs/';
    }
}
