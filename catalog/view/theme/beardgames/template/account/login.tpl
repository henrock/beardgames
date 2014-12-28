<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<div id="content" class="clearfix"><?php echo $content_top; ?>
  <h1><i class="fa fa-user"></i> <?php echo $heading_title; ?></h1>
  <div class="left checkout-box">
    <h2><?php echo $text_new_customer; ?></h2>
    <div class="">
      <p><b><?php echo $text_register; ?></b></p>
      <p><?php echo $text_register_account; ?></p>
      <div class="buttons" style="margin-top: 10px;">
        <a href="<?php echo $register; ?>" class="button"><i class='fa fa-sign-in'></i> Gå vidare</a></div>
      </div>
  </div>
  <div id="login" class="right checkout-box">
    <h2><?php echo $text_returning_customer; ?></h2>
    <form action="" method="post" id="login-form">
      <div class="checkout-form clearfix">
        <div><span id="capslocktext">Obs! Det verkar som att du har Caps lock på</span></div>
        <div class="checkout-textform label"><?php echo $entry_email; ?></div>
        <div class="checkout-textform"><input type="text" name="email" value="" /></div>
        <div class="checkout-textform label"><?php echo $entry_password; ?></div>
        <div class="checkout-textform"><input type="password" name="password" value="" /></div>
        <div class="checkout-textform label"></div>
        <div class="checkout-textform"><a href="<?php echo $forgotten; ?>" class="forgotten-password"><?php echo $text_forgotten; ?></a></div>
      </div>
      <div class="buttons">
        <a type="button" id="button-login" class="button"><i class='fa fa-sign-in'></i> Logga in</a>
      </div>
    </form>
  </div>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$(function() {
  Corel.alignCheckoutBoxes();
});
$('#login-form input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#login-form').submit();
	}
});
$('#button-login').click(function() {
  $('#login-form').submit();
});
//--></script> 
<?php echo $footer; ?>