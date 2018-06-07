<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

namespace Kuuzu\KU;

use Kuuzu\KU\HTML;
use Kuuzu\KU\KUUZU;
use Kuuzu\KU\Registry;

abstract class PagesAbstract implements \Kuuzu\KU\PagesInterface
{
    public $data = [];

    protected $code;
    protected $file = 'main.php';
    protected $use_site_template = true;
    protected $site;
    protected $actions_run = [];
    protected $ignored_actions = [];
    protected $is_rpc = false;

    protected $app;

    final public function __construct(\Kuuzu\KU\SitesInterface $site)
    {
        $this->code = (new \ReflectionClass($this))->getShortName();
        $this->site = $site;

        $this->init();
    }

    protected function init()
    {
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFile()
    {
        if (isset($this->file)) {
            return dirname(KUUZU::BASE_DIR) . '/' . str_replace('\\', '/', (new \ReflectionClass($this))->getNamespaceName()) . '/templates/' . $this->file;
        }
    }

    public function useSiteTemplate()
    {
        return $this->use_site_template;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function isActionRequest()
    {
        $furious_pete = [];

        if (count($_GET) > $this->site->actions_index) {
            $furious_pete = array_keys(array_slice($_GET, $this->site->actions_index, null, true));
        }

        if (!empty($furious_pete)) {
            $action = HTML::sanitize(basename($furious_pete[0]));

            if (!in_array($action, $this->ignored_actions) && $this->actionExists($action)) {
                return true;
            }
        }

        return false;
    }

    public function runAction($actions)
    {
        if (!is_array($actions)) {
            $actions = [
                $actions
            ];
        }

        $run = [];

        foreach ($actions as $action) {
            $run[] = $action;

            if ($this->actionExists($run)) {
                $this->actions_run[] = $action;

                $class = $this->getActionClassName($run);

                $ns = explode('\\', $class);

                if ((count($ns) > 2) && ($ns[0] == 'Kuuzu') && ($ns[1] == 'Apps')) {
                    if (isset($this->app) && is_subclass_of($this->app, 'Kuuzu\KU\AppAbstract')) {
                        if ($this->app->definitionsExist(implode('/', array_slice($ns, 4)))) {
                            $this->app->loadDefinitions(implode('/', array_slice($ns, 4)));
                        }
                    }
                }

                $action = new $class($this);

                $action->execute();

                if ($action->isRPC()) {
                    $this->is_rpc = true;
                }
            } else {
                break;
            }
        }
    }

    public function runActions()
    {
        $actions = $furious_pete = [];

        if (count($_GET) > $this->site->actions_index) {
            $furious_pete = array_keys(array_slice($_GET, $this->site->actions_index, null, true));
        }

        foreach ($furious_pete as $action) {
            $action = HTML::sanitize(basename($action));

            $actions[] = $action;

            if (in_array($action, $this->ignored_actions) || !$this->actionExists($actions)) {
                array_pop($actions);

                break;
            }
        }

        if (!empty($actions)) {
            $this->runAction($actions);
        }
    }

    public function actionExists($action)
    {
        if (!is_array($action)) {
            $action = [
                $action
            ];
        }

        $class = $this->getActionClassName($action);

        if (class_exists($class)) {
            if (is_subclass_of($class, 'Kuuzu\KU\PagesActionsInterface')) {
                return true;
            } else {
                trigger_error('Kuuzu\KU\PagesAbstract::actionExists() - ' . implode('\\', $action) . ': Action does not implement Kuuzu\KU\PagesActionInterface and cannot be loaded.');
            }
        }

        return false;
    }

    public function getActionsRun()
    {
        return $this->actions_run;
    }

    public function isRPC()
    {
        return ($this->is_rpc === true);
    }

    protected function getActionClassName($action)
    {
        if (!is_array($action)) {
            $action = [
                $action
            ];
        }

        return (new \ReflectionClass($this))->getNamespaceName() . '\\Actions\\' . implode('\\', $action);
    }
}
