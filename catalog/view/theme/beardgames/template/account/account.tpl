<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php //echo $column_left; ?><?php //echo $column_right; ?>
<div id="content" class="clearfix"><?php echo $content_top; ?>
  <h1><i class="fa fa-user"></i> <?php echo $heading_title; ?></h1>
  <div class="left checkout-box">
    <h2>Redigera konto</h2>
    <ul>
      <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
      <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li>&nbsp;</li>
      <li><a href="<?php echo $logout; ?>">Logga ut</a></li>
    </ul>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 