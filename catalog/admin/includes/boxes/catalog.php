<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

use Kuuzu\KU\KUUZU;

$admin_menu['shop']['catalog']['categories'] = KUUZU::link('categories.php');

  $cl_box_groups[] = array(
    'heading' => KUUZU::getDef('box_heading_catalog'),
    'apps' => array(
      array(
        'code' => FILENAME_PRODUCTS_ATTRIBUTES,
        'title' => KUUZU::getDef('box_catalog_categories_products_attributes'),
        'link' => KUUZU::link(FILENAME_PRODUCTS_ATTRIBUTES)
      ),
      array(
        'code' => FILENAME_MANUFACTURERS,
        'title' => KUUZU::getDef('box_catalog_manufacturers'),
        'link' => KUUZU::link(FILENAME_MANUFACTURERS)
      ),
      array(
        'code' => FILENAME_REVIEWS,
        'title' => KUUZU::getDef('box_catalog_reviews'),
        'link' => KUUZU::link(FILENAME_REVIEWS)
      ),
      array(
        'code' => FILENAME_SPECIALS,
        'title' => KUUZU::getDef('box_catalog_specials'),
        'link' => KUUZU::link(FILENAME_SPECIALS)
      ),
      array(
        'code' => FILENAME_PRODUCTS_EXPECTED,
        'title' => KUUZU::getDef('box_catalog_products_expected'),
        'link' => KUUZU::link(FILENAME_PRODUCTS_EXPECTED)
      )
    )
  );
?>
