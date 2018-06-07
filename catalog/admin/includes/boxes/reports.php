<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  $cl_box_groups[] = array(
    'heading' => KUUZU::getDef('box_heading_reports'),
    'apps' => array(
      array(
        'code' => FILENAME_STATS_PRODUCTS_VIEWED,
        'title' => KUUZU::getDef('box_reports_products_viewed'),
        'link' => KUUZU::link(FILENAME_STATS_PRODUCTS_VIEWED)
      ),
      array(
        'code' => FILENAME_STATS_PRODUCTS_PURCHASED,
        'title' => KUUZU::getDef('box_reports_products_purchased'),
        'link' => KUUZU::link(FILENAME_STATS_PRODUCTS_PURCHASED)
      ),
      array(
        'code' => FILENAME_STATS_CUSTOMERS,
        'title' => KUUZU::getDef('box_reports_orders_total'),
        'link' => KUUZU::link(FILENAME_STATS_CUSTOMERS)
      )
    )
  );
?>
