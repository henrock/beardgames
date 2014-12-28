<h1>Skapa konto</h1>
<div class="checkout-box left">
  <h2><?php echo $text_your_details; ?></h2>
  <table class="form register-form">
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_firstname; ?>
			</td>
			<td>
				<input type="text" name="firstname" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_lastname; ?>
			</td>
			<td>
				<input type="text" name="lastname" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_email; ?>
			</td>
			<td>
				<input type="text" name="email" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_telephone; ?>
			</td>
			<td>
				<input type="text" name="telephone" value="" class="large-field" />
			</td>
		</tr>
		<!--
		<tr>
			<td>
				<span class="required"></span><?php //echo $entry_fax; ?><br />
			</td>
			<td>
				<input type="text" name="fax" value="" class="large-field" />
			</td>
		</tr>
		-->
  </table>
  <h2><?php echo $text_your_address; ?></h2>
    <table class="form register-form">
		<tr>
			<td>
				<span id="company-id-required" class="required"></span><?php echo $entry_company; ?>
			</td>
			<td>
				<input type="text" name="company" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_address_1; ?>
			</td>
			<td>
				<input type="text" name="address_1" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span id="company-id-required" class="required"></span><?php echo $entry_address_2; ?>
			</td>
			<td>
				<input type="text" name="address_2" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_city; ?>
			</td>
			<td>
				<input type="text" name="city" value="" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span id="payment-postcode-required" class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_postcode; ?>
			</td>
			<td>
				<input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_country; ?>
			</td>
			<td>
				<select name="country_id" class="large-field">
				  <option value=""><?php echo $text_select; ?></option>
				  <?php foreach ($countries as $country) { ?>
				  <?php if ($country['country_id'] == $country_id) { ?>
				  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select>
			</td>
		</tr>
	</table>
  <h2><?php echo $text_your_password; ?></h2>
		<table class="form register-form">
			<tr>
				<td>
					<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_password; ?>
				</td>
				<td>
					<input type="password" name="password" value="" class="large-field" /></td>
			</tr>
			<tr>
				<td>
					<span class="required"><i class="fa fa-exclamation-circle"></i></span> <?php echo $entry_confirm; ?>
				</td>
				<td>
					<input type="password" name="confirm" value="" class="large-field" /></td>
			</tr>
		</table>
		
  <input type="checkbox" name="newsletter" value="1" id="newsletter" />
  <label for="newsletter"><?php echo $entry_newsletter; ?></label>
  <br />
  <?php if ($shipping_required) { ?>
  <input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
  <label for="shipping"><?php echo $entry_shipping; ?></label>
  <br />
  <?php } ?>
  <br />
  <br />
		
		
	<div class="buttons">
		<?php if ($text_agree) { ?>
		<div class="buttons">
		  <div class="right"><?php echo $text_agree; ?>
			<input type="checkbox" name="agree" value="1" />
			<input type="button" value="<?php echo $button_continue; ?>" id="button-register" class="button" />
		  </div>
		</div>
		<?php } else { ?>
		<div class="buttons">
		  <div class="right">
			<input type="button" value="<?php echo $button_continue; ?>" id="button-register" class="button" />
		  </div>
		</div>
		<?php } ?>
	</div>
</div>

<script type="text/javascript"><!--
$('#payment-address input[name=\'customer_group_id\']:checked').live('change', function() {
	var customer_group = [];
	
<?php foreach ($customer_groups as $customer_group) { ?>
	customer_group[<?php echo $customer_group['customer_group_id']; ?>] = [];
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_display'] = '<?php echo $customer_group['company_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['company_id_required'] = '<?php echo $customer_group['company_id_required']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_display'] = '<?php echo $customer_group['tax_id_display']; ?>';
	customer_group[<?php echo $customer_group['customer_group_id']; ?>]['tax_id_required'] = '<?php echo $customer_group['tax_id_required']; ?>';
<?php } ?>	

	if (customer_group[this.value]) {
		if (customer_group[this.value]['company_id_display'] == '1') {
			$('#company-id-display').show();
		} else {
			$('#company-id-display').hide();
		}
		
		if (customer_group[this.value]['company_id_required'] == '1') {
			$('#company-id-required').show();
		} else {
			$('#company-id-required').hide();
		}
		
		if (customer_group[this.value]['tax_id_display'] == '1') {
			$('#tax-id-display').show();
		} else {
			$('#tax-id-display').hide();
		}
		
		if (customer_group[this.value]['tax_id_required'] == '1') {
			$('#tax-id-required').show();
		} else {
			$('#tax-id-required').hide();
		}	
	}
});

$('#payment-address input[name=\'customer_group_id\']:checked').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('#payment-address select[name=\'country_id\']').bind('change', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#payment-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#payment-postcode-required').show();
			} else {
				$('#payment-postcode-required').hide();
			}
			
			html = '<option value=""><?php echo $text_select; ?></option>';
			
			if (json['zone'] != '') {

				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';
	    			
					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}
	
	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}
			
			$('#payment-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#payment-address select[name=\'country_id\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('.colorbox').colorbox({
	width: 640,
	height: 480
});
//--></script> 