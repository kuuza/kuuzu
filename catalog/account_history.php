<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\DateTime;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    KUUZU::redirect('login.php');
  }

  $KUUZU_Language->loadDefinitions('account_history');

  $breadcrumb->add(KUUZU::getDef('navbar_title_1'), KUUZU::link('account.php'));
  $breadcrumb->add(KUUZU::getDef('navbar_title_2'), KUUZU::link('account_history.php'));

  require($kuuTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo KUUZU::getDef('heading_title'); ?></h1>
</div>

<div class="contentContainer">

<?php
  $Qorders = $KUUZU_Db->prepare('select SQL_CALC_FOUND_ROWS o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from :table_orders o, :table_orders_total ot, :table_orders_status s where o.customers_id = :customers_id and o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id and s.public_flag = "1" order by o.orders_id desc limit :page_set_offset, :page_set_max_results');
  $Qorders->bindInt(':customers_id', $_SESSION['customer_id']);
  $Qorders->bindInt(':language_id', $KUUZU_Language->getId());
  $Qorders->setPageSet(MAX_DISPLAY_ORDER_HISTORY);
  $Qorders->execute();

  if ($Qorders->getPageSetTotalRows() > 0) {
    foreach ($Qorders->fetchAll() as $order) {
      $Qproducts = $KUUZU_Db->prepare('select count(*) as count from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindInt(':orders_id', $order['orders_id']);
      $Qproducts->execute();

      if (tep_not_null($order['delivery_name'])) {
        $order_type = KUUZU::getDef('text_order_shipped_to');
        $order_name = $order['delivery_name'];
      } else {
        $order_type = KUUZU::getDef('text_order_billed_to');
        $order_name = $order['billing_name'];
      }
?>

  <div class="contentText">
    <div class="panel panel-info">
      <div class="panel-heading"><strong><?php echo KUUZU::getDef('text_order_number') . ' ' . (int)$order['orders_id'] . ' <span class="contentText">(' . HTML::outputProtected($order['orders_status_name']) . ')</span>'; ?></strong></div>
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-6"><?php echo '<strong>' . KUUZU::getDef('text_order_date') . '</strong> ' . DateTime::toLong($order['date_purchased']) . '<br /><strong>' . $order_type . '</strong> ' . HTML::outputProtected($order_name); ?></div>
          <br class="visible-xs" />
          <div class="col-sm-6"><?php echo '<strong>' . KUUZU::getDef('text_order_products') . '</strong> ' . $Qproducts->valueInt('count') . '<br /><strong>' . KUUZU::getDef('text_order_cost') . '</strong> ' . strip_tags($order['order_total']); ?></div>
        </div>
      </div>
      <div class="panel-footer"><?php echo HTML::button(KUUZU::getDef('small_image_button_view'), 'fa fa-file', KUUZU::link('account_history_info.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'order_id=' . $order['orders_id']), null, 'btn-primary btn-xs'); ?></div>
    </div>
  </div>

<?php
    }
?>
  <div class="row">
    <div class="col-md-6 pagenumber"><?php echo $Qorders->getPageSetLabel(KUUZU::getDef('text_display_number_of_orders')); ?></div>
    <div class="col-md-6"><span class="pull-right pagenav"><?php echo $Qorders->getPageSetLinks(tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span><span class="pull-right"><?php echo KUUZU::getDef('text_result_page'); ?></span></div>
  </div>

<?php
  } else {
?>

  <div class="alert alert-info">
    <p><?php echo KUUZU::getDef('text_no_purchases'); ?></p>
  </div>

<?php
  }
?>

  <div class="buttonSet">
    <?php echo HTML::button(KUUZU::getDef('image_button_back'), 'fa fa-angle-left', KUUZU::link('account.php')); ?>
  </div>
</div>

<?php
  require($kuuTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
