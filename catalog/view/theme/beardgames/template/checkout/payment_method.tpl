<div class="clearfix checkout-header-group">
	<h1 class="checkout-header-left">
		<i class="fa fa-credit-card"></i> Betalning
	</h1>
	<div class="checkout-header-right">
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle"></i> 
		<i class="fa fa-circle-o"></i>
	</div>
</div>

<div class="left checkout-box">

	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	
	<?php if ($payment_methods) { ?>
	<h2>Välj betalningsmetod</h2>
	<div class="checkout-form">
		<table class="radio">
		  <?php foreach ($payment_methods as $payment_method) { ?>
		  <tr class="highlight">
			<td><?php if ($payment_method['code'] == $code || !$code) { ?>
			  <?php $code = $payment_method['code']; ?>
			  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
			  <?php } else { ?>
			  <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
			  <?php } ?></td>
			<td><label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label></td>
		  </tr>
		  <?php } ?>
		</table>
		<br />
		<?php } ?>
	</div>
	
	<h2>Lämna en kommentar till din order</h2>
	
	<div class="checkout-form">
		<textarea name="comment" rows="8" style="width: 98%;"><?php echo $comment; ?></textarea>
	</div>
	
	<br />
	<?php if ($text_agree) { ?>
	
	  Jag har läst och godkänt <a href="/villkor" target="_blank">villkoren</a>
		<?php if ($agree) { ?>
		<input type="checkbox" name="agree" value="1" checked="checked" />
		<?php } else { ?>
		<input type="checkbox" name="agree" value="1" />
		<?php } ?>
		<a type="button" id="button-payment-method" class="button"><i class='fa fa-arrow-circle-right'></i> Fortsätt</a>
	
	<?php } else { ?>
	
	<a id="button-payment-method" class="button"><i class='fa fa-arrow-circle-right'></i> Fortsätt</a>
	  
	<?php } ?>
</div>
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script> 