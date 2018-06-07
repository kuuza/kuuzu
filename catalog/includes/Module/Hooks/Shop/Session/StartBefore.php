<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU\Module\Hooks\Shop\Session;

use Kuuzu\KU\KUUZU;

class StartBefore
{
    public function execute($parameters) {
        if (SESSION_BLOCK_SPIDERS == 'True') {
            $user_agent = '';

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            }

            if (!empty($user_agent)) {
                foreach (file(KUUZU::getConfig('dir_root') . 'includes/spiders.txt') as $spider) {
                    if (!empty($spider)) {
                        if (strpos($user_agent, $spider) !== false) {
                            $parameters['can_start'] = false;
                            break;
                        }
                    }
                }
            }
        }
    }
}
