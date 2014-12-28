<div class="clearfix checkout-header-group">
	<h1 class="checkout-header-left">
		<i class="fa fa-truck"></i> Leveransadress
	</h1>
	<div class="checkout-header-right">
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i> 
		<i class="fa fa-circle-o"></i>
	</div>
</div>

<div class="left checkout-box">
	<h2>Välj leveransadress, eller skapa en ny</h2>
	<div class="checkout-form clearl">
		<?php if ($addresses) { ?>
		<input type="radio" name="shipping_address" value="existing" id="shipping-address-existing" checked="checked" />
		<label for="shipping-address-existing"><?php echo $text_address_existing; ?></label>
		<br/>
		<input type="radio" name="shipping_address" value="new" id="shipping-address-new" />
		<label for="shipping-address-new"><?php echo $text_address_new; ?></label>
		<br/><br/>
		
		<div id="shipping-existing" class="clearl">
		  <select name="address_id" style="width: 100%; margin-bottom: 15px;">
			<?php foreach ($addresses as $address) { ?>
			<?php if ($address['address_id'] == $address_id) { ?>
			<option value="<?php echo $address['address_id']; ?>" selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
			<?php } else { ?>
			<option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>, <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>, <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
			<?php } ?>
			<?php } ?>
		  </select>
		</div>
		
		<?php } ?>
		<div id="shipping-new" style="display: <?php echo ($addresses ? 'none' : 'block'); ?>;" class="clearfix">
			<div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"/></span><?php echo $entry_firstname; ?></div>
			<div class="checkout-textform"><input type="text" name="firstname" value="" class="large-field" /></div>

			<div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"/></span><?php echo $entry_lastname; ?></div>
			<div class="checkout-textform"><input type="text" name="lastname" value="" class="large-field" /></div>
			
			<div class="checkout-textform label"><span class="required">&nbsp;</span><?php echo $entry_company; ?></div>
			<div class="checkout-textform"><input type="text" name="company" value="" class="large-field" /></div>

			<div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"/></span><?php echo $entry_address_1; ?></div>
			<div class="checkout-textform"><input type="text" name="address_1" value="" class="large-field" /></div>

			<div class="checkout-textform label"><span class="required">&nbsp;</span><?php echo $entry_address_2; ?></div>
			<div class="checkout-textform"><input type="text" name="address_2" value="" class="large-field" /></div>

			<div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"/></span><?php echo $entry_city; ?></div>
			<div class="checkout-textform"><input type="text" name="city" value="" class="large-field" /></div>
			
			<div class="checkout-textform label"><span id="shipping-postcode-required" class="required"><i class="fa fa-exclamation-circle"/></span><?php echo $entry_postcode; ?></div>
			<div class="checkout-textform"><input type="text" name="postcode" value="<?php echo $postcode; ?>" class="large-field" /></div>

			<!-- <div class="checkout-textform label"><span class="required">*</span> <?php echo $entry_country; ?></div>
			<div class="checkout-textform"><select name="country_id" class="large-field">
				  <option value=""><?php echo $text_select; ?></option>
				  <?php foreach ($countries as $country) { ?>
				  <?php if ($country['country_id'] == $country_id) { ?>
				  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select></div>
			
			<div class="checkout-textform label"><span class="required">*</span> <?php echo $entry_zone; ?></div>
			<div class="checkout-textform"><select name="zone_id" class="large-field">
				</select></div> -->

		</div>
	</div>
	<br/>
    <a id="button-shipping-address" class="button"><i class='fa fa-arrow-circle-right'></i> Fortsätt</a>
</div>
<script type="text/javascript"><!--
$('#shipping-address input[name=\'shipping_address\']').live('change', function() {
	if (this.value == 'new') {
		$('#shipping-existing').hide();
		$('#shipping-new').show();
	} else {
		$('#shipping-existing').show();
		$('#shipping-new').hide();
	}
});
//--></script> 
<script type="text/javascript"><!--
$('#shipping-address select[name=\'country_id\']').bind('change', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('#shipping-address select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#shipping-postcode-required').show();
			} else {
				$('#shipping-postcode-required').hide();
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
			
			$('#shipping-address select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#shipping-address select[name=\'country_id\']').trigger('change');
//--></script>