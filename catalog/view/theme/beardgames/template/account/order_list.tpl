<?php echo $header; ?><?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-th-list"></i> <?php echo $heading_title; ?></h1>
  <?php if ($orders) { ?>
  <?php foreach ($orders as $order) { ?>
  <div class="order-list checkout-box">
    <div class="order-id"><b><?php echo $text_order_id; ?></b> #<?php echo $order['order_id']; ?></div>
    <div class="order-status"><b><?php echo $text_status; ?></b> <?php echo $order['status']; ?></div>
    <div class="order-info">
      <a href="<?php echo $order['href']; ?>" class="button"><i class="fa fa-search" title="<?php echo $button_view; ?>"></i></a>
    </div>
    <div class="order-content">
      <div><b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
        <b><?php echo $text_products; ?></b> <?php echo $order['products']; ?></div>
      <div><b><?php echo $text_customer; ?></b> <?php echo $order['name']; ?><br />
        <b><?php echo $text_total; ?></b> <?php echo $order['total']; ?></div>
    </div>
  </div>
  <?php } ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php } ?>
  <div class="buttons">
      <a href="<?php echo $continue; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>