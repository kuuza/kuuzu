<?php
use Kuuzu\KU\DateTime;
use Kuuzu\KU\KUUZU;
?>
<div class="col-sm-<?php echo $content_width; ?> upcoming-products">

  <table class="table table-striped table-condensed">
    <tbody>
      <tr>
        <th><?php echo KUUZU::getDef('module_content_upcoming_products_table_heading_products'); ?></th>
        <th class="text-right"><?php echo KUUZU::getDef('module_content_upcoming_products_table_heading_date_expected'); ?></th>
      </tr>
      <?php
      foreach ($products as $product) {
        echo '<tr>';
        echo '  <td><a href="' . KUUZU::link('product_info.php', 'products_id=' . (int)$product['products_id']) . '">' . $product['products_name'] . '</a></td>';
        echo '  <td class="text-right">' . DateTime::toShort($product['date_expected']) . '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>

</div>
