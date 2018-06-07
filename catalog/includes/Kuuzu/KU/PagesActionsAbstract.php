<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

abstract class PagesActionsAbstract implements \Kuuzu\KU\PagesActionsInterface
{
    protected $page;
    protected $file;
    protected $is_rpc = false;

    public function __construct(\Kuuzu\KU\PagesInterface $page)
    {
        $this->page = $page;

        if (isset($this->file)) {
            $this->page->setFile($this->file);
        }
    }

    public function isRPC()
    {
        return ($this->is_rpc === true);
    }
}
