<?php echo $header; ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <div class="product-info">
    <div class="left">
      <?php if ($thumb) { ?>
      <div class="image"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="colorbox"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a></div>
      <?php } else { ?>
      <div class="image"><img src="<?php echo $no_image; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></div>
      <?php } ?>
      <?php if ($images) { ?>
      <div class="image-additional">
        <?php foreach ($images as $image) { ?>
        <a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
        <?php } ?>
      </div>
      <?php } ?>
    </div>
    <div class="middle">
      <?php if ($price) { ?>
      <div class="price">
        <?php if (!$special) { ?>
        <?php echo $price; ?>
        <?php } else { ?>
        <span class="price-new"><?php echo $special; ?></span>
        <?php } ?>
        <?php if ($tax) { ?>
        <span class="price-tax">(<?php echo $text_tax; ?> <?php echo $tax; ?>)</span><br />
        <?php } ?>
        <?php if ($points) { ?>
        <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span><br />
        <?php } ?>
      </div>
      <?php } ?>
      <div class="cart">
        <div>
          <input type="hidden" name="quantity" size="2" value="<?php echo $minimum; ?>" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
          &nbsp;
          <a class="button" id="button-cart"><i class="fa fa-shopping-cart"></i> <span>Lägg i kundvagn</span></a>
          <?php if(!$is_in_wishlist) { ?>
            <a class="button wishlistbutton" onclick="addToWishList('<?php echo $product_id; ?>', true);"><i class="fa fa-star"></i> <span>Önskelista</span></a>
            <a class="button wishlistaddedbutton" style="display: none;"><i class="fa fa-check"></i> <span>Tillagd i önskelista</span></a>
          <?php } else { ?>
            <a class="button wishlistbutton" style="display: none;" onclick="addToWishList('<?php echo $product_id; ?>', true);"><i class="fa fa-star"></i> <span>Önskelista</span></a>
            <a class="button wishlistaddedbutton"><i class="fa fa-check"></i> <span>Tillagd i önskelista</span></a>
          <?php } ?>
        </div>
        <?php if ($minimum > 1) { ?>
        <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?>
      </div>
      <div class="description">
        <?php echo $description; ?>
        <div id="review"><img src="/image/loading.gif" /> Laddar recensioner...</div>
      </div>
    </div>
    <div class="right">     
      <div class="product-info-box">
        <span class="header">Fakta:</span>
        <?php if ($manufacturer) { ?>
        <span class="left-col"><?php echo $text_manufacturer; ?></span> <span class="right-col"><?php echo $manufacturer; ?></span><br />
        <?php } ?>
        <span class="left-col"><?php echo $text_model; ?></span> <span class="right-col"><?php echo $model; ?></span><br />
        <span class="left-col"><?php echo $text_stock; ?></span> <span class="right-col"><?php echo $stock; ?></span><br />    
        <span class="left-col">Kundbetyg:</span> 
        <span class="right-col">
        <?php if ($reviews) { ?>
        <?php for($i=0;$i<$rating;$i++) { ?>
          <i class="fa fa-star"></i>
        <?php } ?>
        <?php for($i=5;$i>$rating;$i--) { ?>
          <i class="fa fa-star-o"></i>
        <?php } ?>
        <?php  } else { ?>
          Ej betygsatt
        <?php } ?>
        </span>
        <?php if($this->customer->isLogged()) { ?>
          <span class="review-link"><a onclick="$('#review-holder').slideToggle(200);">Recensera produkten</a> <i class="fa fa-caret-square-o-down" onclick="$('#review-holder').slideToggle(200);" style="cursor:pointer"></i></span>
          <div id="review-holder">
            <div id="review-form" class="product-info-popup">
              <div class="rating">
                Betyg: 
                <i class="fa fa-star-o" data-rating="1"></i>
                <i class="fa fa-star-o" data-rating="2"></i>
                <i class="fa fa-star-o" data-rating="3"></i>
                <i class="fa fa-star-o" data-rating="4"></i>
                <i class="fa fa-star-o" data-rating="5"></i>
              </div>
              <textarea name="text" placeholder="Recension på max 500 tecken..."></textarea>
              <br />
              <?php echo $entry_captcha; ?><br/>
              <input type="text" class="text" name="captcha" value="" /><br/>          
              <img src="index.php?route=product/product/captcha" alt="" id="captcha" />
              <br />
              <input type="hidden" name="rating" />
              <input type="hidden" name="name" value="" />
              <a class="button" id="button-review">Skicka</a>
              <img src="catalog/view/theme/beardgames/image/box-shadow.png" class="box-shadow" />
            </div>
          </div>
        <?php } ?>
        <?php if($age != '' || $game_time != '' || $players != '' || $languages != '' || $contents != '') { ?>
          <span class="header">Specifikationer:</span>
          <?php if($age != '') { ?>
          <span class="left-col">Ålder:</span> <span class="right-col"><?php echo $age; ?> år</span><br />
          <?php } ?>
          <?php if($contents != '') { ?>
          <span class="left-col">Innehåll:</span> <span class="right-col">
            <a onclick="$('#contents-holder').slideToggle(200);">Klicka för att visa</a> <i class="fa fa-caret-square-o-down" onclick="$('#contents-holder').slideToggle(200);" style="cursor:pointer"></i>
            <div id="contents-holder">
              <div id="contents" class="product-info-popup">
                <?php echo nl2br($contents); ?>
                <img src="catalog/view/theme/beardgames/image/box-shadow.png" class="box-shadow" />
              </div>
            </div>
          </span><br /> 
          <?php } ?>
          <?php if($game_time != '') { ?>
          <span class="left-col">Speltid:</span> <span class="right-col"><?php echo $game_time; ?></span><br /> 
          <?php } ?>
          <?php if($players != '') { ?>
          <span class="left-col">Spelare:</span> <span class="right-col"><?php echo $players; ?></span><br /> 
          <?php } ?>
          <?php if($languages != '') { ?>
          <span class="left-col">Språk:</span> <span class="right-col"><?php echo $languages; ?></span><br /> 
          <?php } ?>
        <?php } ?>
      </div>
      <span class="header">Dela denna titel:</span>
      <div class="share"><!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
          <a class="addthis_button_preferred_1"></a>
          <a class="addthis_button_preferred_2"></a>
          <a class="addthis_button_preferred_3"></a>
          <a class="addthis_button_preferred_4"></a>
          <a class="addthis_button_compact"></a>
          <a class="addthis_counter addthis_bubble_style"></a>
        </div>
        <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52e959c67da08c42"></script>
        <!-- AddThis Button END -->
      </div>
      <?php if ($products) { ?>
        <span class="header">Relaterade titlar:</span>
        <?php foreach ($products as $product) { ?>
          <?php if ($product['thumb']) { ?>
          <div class="image"><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
          <?php } else { ?>
          <div class="image"><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><img src="<?php echo $no_image_related; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.colorbox').colorbox({
		overlayClose: true,
		opacity: 0.5,
    returnFocus: false,
		rel: "colorbox"
	});
});
//--></script> 
<script type="text/javascript"><!--

$('#review-form .rating i').mouseenter(function() {
  $(this).prevAll().removeClass('fa-star-o').addClass('fa-star hover');
  $(this).removeClass('fa-star-o').addClass('fa-star hover');
  $(this).nextAll().removeClass('fa-star hover').addClass('fa-star-o');
});
$('#review-form .rating i').click(function() {
  var rating = $(this).data('rating');
  $('input[name=\'rating\']').val(rating);
  $(this).prevAll().removeClass('fa-star-o hover').addClass('fa-star chosen');
  $(this).removeClass('fa-star-o hover').addClass('fa-star chosen');
  $(this).nextAll().removeClass('fa-star hover chosen').addClass('fa-star-o');
});
$('#review-form .rating').mouseleave(function() {
  $('#review-form .rating i.chosen').removeClass('hover').removeClass('fa-star-o').addClass('fa-star');
  $('#review-form .rating i.hover').removeClass('hover').not('.chosen').removeClass('fa-star').addClass('fa-star-o');
});

$('select[name="profile_id"], input[name="quantity"]').change(function(){
    $.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name="product_id"], input[name="quantity"], select[name="profile_id"]'),
		dataType: 'json',
        beforeSend: function() {
            $('#profile-description').html('');
        },
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
            
			if (json['success']) {
                $('#profile-description').html(json['success']);
			}	
		}
	});
});
    
$('#button-cart').bind('click', function() {
  addToCart(<?php echo $product_id; ?>, 1, this);
	/*$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
                
                if (json['error']['profile']) {
                    $('select[name="profile_id"]').after('<span class="error">' + json['error']['profile'] + '</span>');
                }
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});*/
});
//--></script>
<?php if ($options) { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
<?php foreach ($options as $option) { ?>
<?php if ($option['type'] == 'file') { ?>
<script type="text/javascript"><!--
new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
	action: 'index.php?route=product/product/upload',
	name: 'file',
	autoSubmit: true,
	responseType: 'json',
	onSubmit: function(file, extension) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
	},
	onComplete: function(file, json) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
		
		$('.error').remove();
		
		if (json['success']) {
			alert(json['success']);
			
			$('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
		}
		
		if (json['error']) {
			$('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
		}
		
		$('.loading').remove();	
	}
});
//--></script>
<?php } ?>
<?php } ?>
<?php } ?>
<script type="text/javascript"><!--
$('#review .pagination a').live('click', function() {
	$('#review').fadeOut('slow');
		
	$('#review').load(this.href);
	
	$('#review').fadeIn('slow');
	
	return false;
});			

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').bind('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']').val()) + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-review').attr('disabled', true);
			$('#review-holder').after('<div class="attention"><img src="/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-review').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(data) {
			if (data['error']) {
				$('#review-holder').after('<div class="warning">' + data['error'] + '</div>');
			}
			
			if (data['success']) {
        $('#review-holder').slideUp(200);
				$('#review-holder').after('<div class="success">' + data['success'] + '</div>');
								
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']').val('');
				$('input[name=\'captcha\']').val('');
        $('#review-form .rating i').removeClass('hover chosen').removeClass('fa-star').addClass('fa-star-o');
        $('#captcha').attr('src', $('#captcha').attr('src') + '&' + new Date().getTime());
			}
		}
	});
});
//--></script> 
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
$(document).ready(function() {
	if ($.browser.msie && $.browser.version == 6) {
		$('.date, .datetime, .time').bgIframe();
	}

	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.datetime').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'h:m'
	});
	$('.time').timepicker({timeFormat: 'h:m'});
});
//--></script> 
<?php echo $footer; ?>