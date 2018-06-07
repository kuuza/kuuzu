<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\KUUZU;

  $cl_box_groups[] = array(
    'heading' => KUUZU::getDef('box_heading_customers'),
    'apps' => array(
      array(
        'code' => FILENAME_CUSTOMERS,
        'title' => KUUZU::getDef('box_customers_customers'),
        'link' => KUUZU::link(FILENAME_CUSTOMERS)
      )
    )
  );
?>
