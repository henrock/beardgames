<div class="clearfix checkout-header-group">
	<h1 class="checkout-header-left">
		<i class="fa fa-archive"></i> Frakt
	</h1>
	<div class="checkout-header-right">
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i>
	</div>
</div>

<div class="left checkout-box">
	
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<?php if ($shipping_methods) { ?>
	<h2>Välj fraktmetod</h2>
	<div class="checkout-form">
	<table class="radio">
	  <?php foreach ($shipping_methods as $shipping_method) { ?>
	  <tr>
		<td colspan="3"><b><?php echo $shipping_method['title']; ?></b></td>
	  </tr>
	  <?php if (!$shipping_method['error']) { ?>
	  <?php foreach ($shipping_method['quote'] as $quote) { ?>
	  <tr class="highlight">
		<td><?php if ($quote['code'] == $code || !$code) { ?>
		  <?php $code = $quote['code']; ?>
		  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
		  <?php } else { ?>
		  <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
		  <?php } ?></td>
		<td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label></td>
		<td style="text-align: right;"><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
	  </tr>
	  <?php } ?>
	  <?php } else { ?>
	  <tr>
		<td colspan="3"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
	  </tr>
	  <?php } ?>
	  <?php } ?>
	</table>
	</div>
	<br />
	<?php } ?>
	<h2><?php echo $text_comments; ?></h2>
	<div class="checkout-form">
		<textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
	</div>
	<br />
	
	
	<a id="button-shipping-method" class="button"><i class='fa fa-arrow-circle-right'></i> Fortsätt</a>
</div>