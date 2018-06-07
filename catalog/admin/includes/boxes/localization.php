<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  $cl_box_groups[] = array(
    'heading' => KUUZU::getDef('box_heading_localization'),
    'apps' => array(
      array(
        'code' => FILENAME_CURRENCIES,
        'title' => KUUZU::getDef('box_localization_currencies'),
        'link' => KUUZU::link(FILENAME_CURRENCIES)
      ),
      array(
        'code' => FILENAME_LANGUAGES,
        'title' => KUUZU::getDef('box_localization_languages'),
        'link' => KUUZU::link(FILENAME_LANGUAGES)
      ),
      array(
        'code' => FILENAME_ORDERS_STATUS,
        'title' => KUUZU::getDef('box_localization_orders_status'),
        'link' => KUUZU::link(FILENAME_ORDERS_STATUS)
      )
    )
  );
?>
