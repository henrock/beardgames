<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-home"></i> <?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="adress_form">
    <div class="content left checkout-box">
      <h2><?php echo $text_edit_address; ?></h2>
      <div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"></i></span><?php echo $entry_firstname; ?></div>
      <div class="checkout-textform"><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></div>
      <?php if ($error_firstname) { ?>
        <div class="checkout-textform"><span class="error"><?php echo $error_firstname; ?></span></div>
      <?php } ?>

      <div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"></i></span><?php echo $entry_lastname; ?></div>
      <div class="checkout-textform"><input type="text" name="lastname" value="<?php echo $lastname; ?>" /></div>
      <?php if ($error_lastname) { ?>
        <div class="checkout-textform"><span class="error"><?php echo $error_lastname; ?></span></div>
      <?php } ?>

      <div class="checkout-textform label"><span class="required">&nbsp;</span><?php echo $entry_company; ?></div>
      <div class="checkout-textform"><input type="text" name="company" value="<?php echo $company; ?>" /></div>

      <?php if ($company_id_display) { ?>
        <div class="checkout-textform label"><?php if ($company_id_required) { ?>
        <span class="required">*</span>
        <?php } ?>
        <?php echo $entry_company_id; ?></div>
        <div class="checkout-textform"><input type="text" name="company_id" value="" /></div>
      <?php } ?>
      <?php if ($tax_id_display) { ?>
        <div class="checkout-textform label"><?php if ($tax_id_required) { ?>
        <span class="required">*</span>
        <?php } ?>
        <?php echo $entry_tax_id; ?></div>
        <div class="checkout-textform"><input type="text" name="tax_id" value="" /></div>
      <?php } ?>

      <div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"></i></span><?php echo $entry_address_1; ?></div>
      <div class="checkout-textform"><input type="text" name="address_1" value="<?php echo $address_1; ?>" /></div>
      <?php if ($error_address_1) { ?>
        <div class="checkout-textform"><span class="error"><?php echo $error_address_1; ?></span></div>
      <?php } ?>

      <div class="checkout-textform label"><span class="required">&nbsp;</span><?php echo $entry_address_2; ?></div>
      <div class="checkout-textform"><input type="text" name="address_2" value="<?php echo $address_2; ?>" /></div>

      <div class="checkout-textform label"><span class="required"><i class="fa fa-exclamation-circle"></i></span><?php echo $entry_city; ?></div>
      <div class="checkout-textform"><input type="text" name="city" value="<?php echo $city; ?>" /></div>
      <?php if ($error_city) { ?>
        <div class="checkout-textform"><span class="error"><?php echo $error_city; ?></span></div>
      <?php } ?>

      <div class="checkout-textform label"><span id="payment-postcode-required" class="required"><i class="fa fa-exclamation-circle"></i></span><?php echo $entry_postcode; ?></div>
      <div class="checkout-textform"><input type="text" name="postcode" value="<?php echo $postcode; ?>" /></div>
      <?php if ($error_postcode) { ?>
        <div class="checkout-textform"><span class="error"><?php echo $error_postcode; ?></span></div>
      <?php } ?>

      <div class="checkout-textform label"><?php echo $entry_default; ?></div>
      <div class="checkout-textform label">
        <?php if ($default) { ?>
          <input type="radio" name="default" value="1" checked="checked" />
          <?php echo $text_yes; ?>
          <input type="radio" name="default" value="0" />
          <?php echo $text_no; ?>
          <?php } else { ?>
          <input type="radio" name="default" value="1" />
          <?php echo $text_yes; ?>
          <input type="radio" name="default" value="0" checked="checked" />
          <?php echo $text_no; ?>
        <?php } ?>
      </div>
    
      <div class="buttons">
		<a class="button" onclick="$('#adress_form').submit();"><i class="fa fa-arrow-circle-right"></i> <?php echo $button_continue; ?></a>
        <a href="<?php echo $back; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> <?php echo $button_back; ?></a>
      </div>
    </div>
    <input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
  </form>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('select[name=\'country_id\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=account/address/country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},		
		complete: function() {
			$('.wait').remove();
		},			
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
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
			
			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
//--></script> 
<?php echo $footer; ?>