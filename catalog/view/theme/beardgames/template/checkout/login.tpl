<h1><i class="fa fa-user"></i> Identifiering</h1>
<div class="left checkout-box">
  <h2><?php echo $text_new_customer; ?></h2>
  <div class="checkout-form">
	  <label for="register">
		<?php if ($account == 'register') { ?>
		<input type="radio" name="account" value="register" id="register" checked="checked" />
		<?php } else { ?>
		<input type="radio" name="account" value="register" id="register" />
		<?php } ?>
		<b><?php echo $text_register; ?></b></label> <i class="fa fa-question-circle" title="<?php echo $text_register_account; ?>"></i>
	  <br />
	  <?php if ($guest_checkout) { ?>
	  <label for="guest">
		<?php if ($account == 'guest') { ?>
		<input type="radio" name="account" value="guest" id="guest" checked="checked" />
		<?php } else { ?>
		<input type="radio" name="account" value="guest" id="guest" />
		<?php } ?>
		<b><?php echo $text_guest; ?></b></label>
	  <br />
	  <?php } ?>
  </div>
  <div class="buttons">
  	<a type="button" id="button-account" class="button"><i class='fa fa-arrow-circle-right'></i> Fortsätt</a>
  </div>
</div>
<div id="login" class="right checkout-box">
  <h2><?php echo $text_returning_customer; ?></h2>
  <div class="checkout-form clearfix">
	<div class="checkout-textform label"><?php echo $entry_email; ?></div>
	<div class="checkout-textform"><input type="text" name="email" value="" /></div>
	<div class="checkout-textform label"><?php echo $entry_password; ?></div>
	<div class="checkout-textform"><input type="password" name="password" value="" /></div>
	<div class="checkout-textform label"></div>
	<div class="checkout-textform"><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></div>
  </div>
  <div class="buttons">
  	<a type="button" id="button-login" class="button"><i class='fa fa-sign-in'></i> Logga in</a>
  </div>
</div>