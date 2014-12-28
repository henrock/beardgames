<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
  <h1><?php echo $heading_title; ?></h1>
  <span class="refine-search">
    Denna kategori har <?php echo $product_total; ?> artiklar
    <a onclick="toggleFilter();"><?php echo $text_refine; ?></a> <i id="filter-icon" class="fa fa-caret-square-o-down"></i>
  </span>
  <?php echo $content_top; ?>
  <?php if ($thumb || $description) { ?>
  <div class="category-info">
    <?php if ($thumb) { ?>
    <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } else { ?>
    <div class="image"><img src="<?php echo $no_image; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>
    <?php if ($description) { ?>
    <?php echo $description; ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($categories) { ?>
  <h2><?php echo $text_refine; ?></h2>
  <div class="category-list">
    <?php if (count($categories) <= 5) { ?>
    <ul>
      <?php foreach ($categories as $category) { ?>
      <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
      <?php } ?>
    </ul>
    <?php } else { ?>
    <?php for ($i = 0; $i < count($categories);) { ?>
    <ul>
      <?php $j = $i + ceil(count($categories) / 4); ?>
      <?php for (; $i < $j; $i++) { ?>
      <?php if (isset($categories[$i])) { ?>
      <li><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
    </ul>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($products) { ?>
  <div class="product-filter">    
    <div class="limit">
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
    <div class="sort">
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
    <div class="display"><i class="fa fa-list fa-2x active"></i> <a onclick="display('grid');"><i class="fa fa-th-large fa-2x"></i></a></div>
  </div>
  <!--<div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div></div>-->
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