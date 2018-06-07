<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Apps;
  use Kuuzu\KU\Registry;

  class order_total {
    var $modules;

    protected $lang;

// class constructor
    function __construct() {
      $this->lang = Registry::get('Language');

      if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $class = Apps::getModuleClass($value, 'OrderTotal');

            Registry::set('OrderTotal_' . str_replace('\\', '_', $value), new $class);
          } else {
            $this->lang->loadDefinitions('modules/order_total/' . pathinfo($value, PATHINFO_FILENAME));
            include('includes/modules/order_total/' . $value);

            $class = substr($value, 0, strrpos($value, '.'));
            $GLOBALS[$class] = new $class;
          }
        }
      }
    }

    function process() {
      $order_total_array = array();
      if (is_array($this->modules)) {
        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $KUUZU_OTM = Registry::get('OrderTotal_' . str_replace('\\', '_', $value));
          } else {
            $class = substr($value, 0, strrpos($value, '.'));

            $KUUZU_OTM = $GLOBALS[$class];
          }

          if ($KUUZU_OTM->enabled) {
            $KUUZU_OTM->output = array();
            $KUUZU_OTM->process();

            for ($i=0, $n=sizeof($KUUZU_OTM->output); $i<$n; $i++) {
              if (tep_not_null($KUUZU_OTM->output[$i]['title']) && tep_not_null($KUUZU_OTM->output[$i]['text'])) {
                $order_total_array[] = [
                  'code' => $KUUZU_OTM->code,
                  'title' => $KUUZU_OTM->output[$i]['title'],
                  'text' => $KUUZU_OTM->output[$i]['text'],
                  'value' => $KUUZU_OTM->output[$i]['value'],
                  'sort_order' => $KUUZU_OTM->sort_order
                ];
              }
            }
          }
        }
      }

      return $order_total_array;
    }

    function output() {
      $output_string = '';
      if (is_array($this->modules)) {
        foreach($this->modules as $value) {
          if (strpos($value, '\\') !== false) {
            $KUUZU_OTM = Registry::get('OrderTotal_' . str_replace('\\', '_', $value));
          } else {
            $class = substr($value, 0, strrpos($value, '.'));

            $KUUZU_OTM = $GLOBALS[$class];
          }

          if ($KUUZU_OTM->enabled) {
            $size = sizeof($KUUZU_OTM->output);
            for ($i=0; $i<$size; $i++) {
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main">' . $KUUZU_OTM->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main">' . $KUUZU_OTM->output[$i]['text'] . '</td>' . "\n" .
                                '              </tr>';
            }
          }
        }
      }

      return $output_string;
    }
  }
?>
