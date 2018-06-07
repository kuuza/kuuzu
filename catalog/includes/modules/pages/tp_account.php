<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  class tp_account {
    var $group = 'account';

    function prepare() {
      global $kuuTemplate;

      $kuuTemplate->_data[$this->group] = array('account' => array('title' => KUUZU::getDef('my_account_title'),
                                                                   'sort_order' => 10,
                                                                   'links' => array('edit' => array('title' => KUUZU::getDef('my_account_information'),
                                                                                                    'link' => KUUZU::link('account_edit.php'),
                                                                                                    'icon' => 'fa fa-fw fa-user'),
                                                                                    'address_book' => array('title' => KUUZU::getDef('my_account_address_book'),
                                                                                                            'link' => KUUZU::link('address_book.php'),
                                                                                                            'icon' => 'fa fa-fw fa-home'),
                                                                                    'password' => array('title' => KUUZU::getDef('my_account_password'),
                                                                                                        'link' => KUUZU::link('account_password.php'),
                                                                                                        'icon' => 'fa fa-fw fa-cog'))),
                                                'orders' => array('title' => KUUZU::getDef('my_orders_title'),
                                                                  'sort_order' => 20,
                                                                  'links' => array('history' => array('title' => KUUZU::getDef('my_orders_view'),
                                                                                                      'link' => KUUZU::link('account_history.php'),
                                                                                                      'icon' => 'fa fa-fw fa-shopping-cart'))),
                                                'notifications' => array('title' => KUUZU::getDef('email_notifications_title'),
                                                                         'sort_order' => 30,
                                                                         'links' => array('newsletters' => array('title' => KUUZU::getDef('email_notifications_newsletters'),
                                                                                                                 'link' => KUUZU::link('account_newsletters.php'),
                                                                                                                 'icon' => 'fa fa-fw fa-envelope'),
                                                                                          'products' => array('title' => KUUZU::getDef('email_notifications_products'),
                                                                                                              'link' => KUUZU::link('account_notifications.php'),
                                                                                                              'icon' => 'fa fa-fw fa-send'))));
    }

    function build() {
      global $kuuTemplate;

      foreach ( $kuuTemplate->_data[$this->group] as $key => $row ) {
        $arr[$key] = $row['sort_order'];
      }
      array_multisort($arr, SORT_ASC, $kuuTemplate->_data[$this->group]);

      $output = '<div class="col-sm-12">';

      foreach ( $kuuTemplate->_data[$this->group] as $group ) {
        $output .= '<h2>' . $group['title'] . '</h2>' .
                   '<div class="contentText">' .
                   '  <ul class="list-unstyled">';

        foreach ( $group['links'] as $entry ) {
          $output .= '    <li>';

          if ( isset($entry['icon']) ) {
            $output .= '<i class="' . $entry['icon'] . '"></i> ';
          }

          $output .= (tep_not_null($entry['link'])) ? '<a href="' . $entry['link'] . '">' . $entry['title'] . '</a>' : $entry['title'];

          $output .= '    </li>';
        }

        $output .= '  </ul>' .
                   '</div>';
      }

      $output .= '</div>';

      $kuuTemplate->addContent($output, $this->group);
    }
  }
?>
