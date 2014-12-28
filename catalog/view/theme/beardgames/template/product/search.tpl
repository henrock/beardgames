<?php echo $header; ?><?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-search"></i> <?php echo $heading_title; ?></h1>
  <b><?php echo $text_critea; ?></b>
  <p><?php echo $entry_search; ?>
    <?php if ($search) { ?>
    <input type="text" name="search" value="<?php echo $search; ?>" />
    <?php } else { ?>
    <input type="text" name="search" value="<?php echo $search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '000000'" style="color: #999;" />
    <?php } ?>
    <select name="category_id">
      <option value="0"><?php echo $text_category; ?></option>
      <?php foreach ($categories as $category_1) { ?>
      <?php if ($category_1['category_id'] == $category_id) { ?>
      <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
      <?php } ?>
      <?php foreach ($category_1['children'] as $category_2) { ?>
      <?php if ($category_2['category_id'] == $category_id) { ?>
      <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
      <?php } ?>
      <?php foreach ($category_2['children'] as $category_3) { ?>
      <?php if ($category_3['category_id'] == $category_id) { ?>
      <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
      <?php } ?>
      <?php } ?>
      <?php } ?>
      <?php } ?>
    </select>
    <?php if ($sub_category) { ?>
    <input type="checkbox" name="sub_category" value="1" id="sub_category" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="sub_category" value="1" id="sub_category" />
    <?php } ?>
    <label for="sub_category"><?php echo $text_sub_category; ?></label>
  </p>
  <?php if ($description) { ?>
  <input type="checkbox" name="description" value="1" id="description" checked="checked" />
  <?php } else { ?>
  <input type="checkbox" name="description" value="1" id="description" />
  <?php } ?>
  <label for="description"><?php echo $entry_description; ?></label>
  <div class="buttons">
    <div class="right"><input type="button" value="<?php echo $button_search; ?>" id="button-search" class="button" /></div>
  </div>
  <h2><?php echo $text_search; ?></h2>
  <?php if ($products) { ?>
  <div class="product-filter">
    <div class="display"><i class="fa fa-list fa-2x active"></i> <a onclick="display('grid');"><i class="fa fa-th-large fa-2x"></i></a></div>
    <div class="limit"><?php echo $text_limit; ?>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><?php echo $text_sort; ?>
      <select onchange="location = this.value;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="product-list">
    <?php foreach ($products as $key => $product) { ?>
    <div class="product">
      
      <div class="image">
        <a href="<?php echo $product['href']; ?>">
          <?php if ($product['thumb']) { ?>
          <img src="<?php echo $product['thumb']; ?>" class="list_image" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
          <?php } else { ?>
          <img src="<?php echo $no_image; ?>" class="list_image" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
          <?php } ?>
          <?php if ($product['grid_thumb']) { ?>
          <img src="<?php echo $product['grid_thumb']; ?>" class="grid_image" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
          <?php } else { ?>
          <img src="<?php echo $no_image_grid; ?>" class="grid_image" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
          <?php } ?>
        </a>
      </div>

      <div class="name"><a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>"><span class="list_name"><?php echo $product['name']; ?></span><span class="grid_name"><?php echo $product['grid_name']; ?></span></a></div>
      <div class="description"><?php echo $product['description']; ?></div>
      <!--<div class="quantity">
        <?php if($product['quantity'] > 0) { ?>
          <i class="fa fa-truck available"></i></span>
        <?php } else { ?>
          <i class="fa fa-truck unavailable"></i></span>
        <?php } ?>
      </div>-->
      <?php if ($product['price']) { ?>
      <div class="price">
        <?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?> kr
        <?php } else { ?>
        <span class="price-new" title="Ord. pris <?php echo $product['price']; ?> kr"><?php echo $product['special']; ?> kr</span>
        <?php } ?>
      </div>
      <?php } ?>
      <?php /*if ($product['rating']) { ?>
      <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
      <?php }*/ ?>
      <div class="cart">
        <a onclick="addToCart('<?php echo $product['product_id']; ?>',1,this);" class="button" title="Köp"><i class="fa fa-shopping-cart"></i></a>
      </div>
      <div class="wishlist">
      <?php if($product['is_in_wishlist']) { ?>
        <a onclick="addToWishList('<?php echo $product['product_id']; ?>', true, true);" style="display: none;" id="wishlistbutton_<?php echo $product['product_id']; ?>" class="button wishlistbutton" title="Önskelista"><i class="fa fa-star"></i></a>
        <a id="wishlistaddedbutton_<?php echo $product['product_id']; ?>" class="button wishlistaddedbutton" title="Redan tillagd i önskelista"><i class="fa fa-check"></i></a>
      <?php } else { ?>
        <a onclick="addToWishList('<?php echo $product['product_id']; ?>', true, true);" id="wishlistbutton_<?php echo $product['product_id']; ?>" class="button wishlistbutton" title="Önskelista"><i class="fa fa-star"></i></a>
        <a style="display: none;" id="wishlistaddedbutton_<?php echo $product['product_id']; ?>" class="button wishlistaddedbutton" title="Redan tillagd i önskelista"><i class="fa fa-check"></i></a>
       <?php } ?>
      </div>
      <div class="quantity">
      <?php if($product['quantity'] > 0) { ?>
        <i class="fa fa-cubes available" title="Finns i lager"></i></span>
      <?php } else { ?>
        <i class="fa fa-cubes unavailable" title="Finns ej i lager"></i></span>
      <?php } ?>
      </div>
      <!--<div class="compare">
        <a onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="button" title="Jämför"><i class="fa fa-exchange"></i></a>
      </div>-->
    </div>
    <?php } ?>
  </div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('#content input[name=\'search\']').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').bind('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').attr('disabled', 'disabled');
		$('input[name=\'sub_category\']').removeAttr('checked');
	} else {
		$('input[name=\'sub_category\']').removeAttr('disabled');
	}
});

$('select[name=\'category_id\']').trigger('change');

$('#button-search').bind('click', function() {
	url = 'index.php?route=product/search';
	
	var search = $('#content input[name=\'search\']').attr('value');
	
	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').attr('value');
	
	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}
	
	var sub_category = $('#content input[name=\'sub_category\']:checked').attr('value');
	
	if (sub_category) {
		url += '&sub_category=true';
	}
		
	var filter_description = $('#content input[name=\'description\']:checked').attr('value');
	
	if (filter_description) {
		url += '&description=true';
	}

	location = url;
});

function display(view) {
	if (view == 'list') {
    $('.product-grid').attr('class', 'product-list');
    
    $('.product-list > div').each(function(index, element) {
      html  = '<div class="right">';
      //html += '  <div class="quantity">' + $(element).find('.quantity').html() + '</div>';
      var price = $(element).find('.price').html();
      
      if (price != null) {
        html += '<div class="price">' + price  + '</div>';
      }
      html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
      html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';    
      html += '  <div class="quantity">' + $(element).find('.quantity').html() + '</div>';    
      
      html += '</div>';     
      
      html += '<div class="left">';
      
      $(element).find('.image').find('.list_image').show();
      $(element).find('.image').find('.grid_image').hide();
      var image = $(element).find('.image').html();
      
      if (image != null) { 
        html += '<div class="image">' + image + '</div>';
      }
                
      $(element).find('.name').find('.list_name').show();
      $(element).find('.name').find('.grid_name').hide();
      html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
      html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
      
      var rating = $(element).find('.rating').html();
      
      if (rating != null) {
        html += '<div class="rating">' + rating + '</div>';
      }
        
      html += '</div>';
            
      $(element).html(html);
    });   
    
    $('.display').html('<i class="fa fa-list fa-2x active"></i> <a onclick="display(\'grid\');" title="Visa som rutnät"><i class="fa fa-th fa-2x"></i></a>');
    
    $.totalStorage('display', 'list'); 
  } else {
    $('.product-list').attr('class', 'product-grid');
    var counter = 1;
    
    $('.product-grid > div').each(function(index, element) {
      if(counter % 5 == 0) {
        $(element).addClass('fifth');
      }
      counter++;
      html = '';
      
      $(element).find('.image').find('.grid_image').show();
      $(element).find('.image').find('.list_image').hide();
      var image = $(element).find('.image').html();
      
      if (image != null) {
        html += '<div class="image">' + image + '</div>';
      }
      
      $(element).find('.name').find('.grid_name').show();
      $(element).find('.name').find('.list_name').hide();
      html += '<div class="name_holder">';
      html += '<div class="name">' + $(element).find('.name').html() + '</div>';
      //html += '<div class="quantity">' + $(element).find('.quantity').html() + '</div>';
      html += '</div>';
      html += '<div class="description">' + $(element).find('.description').html() + '</div>';
              
      html += '<div class="price_holder">';    
      html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
      html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
      html += '<div class="quantity">' + $(element).find('.quantity').html() + '</div>';

      var price = $(element).find('.price').html();
      
      if (price != null) {
        html += '<div class="price">' + price  + '</div>';
      }
      html += '</div>';
      
      var rating = $(element).find('.rating').html();
      
      if (rating != null) {
        html += '<div class="rating">' + rating + '</div>';
      }
      
      $(element).html(html);
    }); 
          
    $('.display').html('<a onclick="display(\'list\');" title="Visa som lista"><i class="fa fa-list fa-2x"></i></a> <i class="fa fa-th fa-2x active"></i>');
    
    $.totalStorage('display', 'grid');
  }
}

view = $.totalStorage('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 
<?php echo $footer; ?>