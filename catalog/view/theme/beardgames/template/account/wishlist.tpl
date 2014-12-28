<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>
<?php //echo $column_left; ?><?php //echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-star"></i> <?php echo $heading_title; ?></h1>
  <?php if ($products) { ?>
  <div class="wishlist-info">
    <table>
			<?php foreach ($products as $product) { ?>
      
				<tr id="wishlist-row<?php echo $product['product_id']; ?>">
				  <td class="image">
					<?php if ($product['thumb']) { ?>
					<a href="<?php echo $product['href']; ?>" style="display:block;height:45px;"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
					<?php } else { ?>
					<a href="<?php echo $product['href']; ?>" style="display:block;height:45px;"><img src="<?php echo $no_image; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
					<?php } ?>
				  </td>
				  <td class="name">
					<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><br/>
					<span><?php echo $product['model']; ?></span>
				  </td>
				  <td class="stock"><?php echo $product['stock']; ?></td>
				  <td class="price"><?php if ($product['price']) { ?>
					<div class="price">
					  <?php if (!$product['special']) { ?>
					  <?php echo $product['price']; ?> kr
					  <?php } else { ?>
					  <s><?php echo $product['price']; ?></s> <b><?php echo $product['special']; ?></b>
					  <?php } ?>
					</div>
					<?php } ?>
					</td>
				  <td class="action">
					<a class="button" onclick="addToCart('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart" alt="<?php echo $button_cart; ?>" title="<?php echo $button_cart; ?>" ></i> Köp</a>&nbsp;&nbsp;
					<a href="<?php echo $product['remove']; ?>" class="button"><i class="fa fa-times" alt="<?php echo $button_remove; ?>" title="<?php echo $button_remove; ?>"></i></a>
					</td>
				</tr>
				<tr><td colspan="10" style="background-color:#ffffff;height:10px;padding:0;"></tr>
      <?php } ?>
    </table>
  </div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a></div>
  </div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a></div>
  </div>
  <?php } ?>
  <?php //echo $content_bottom; ?></div>
<?php echo $footer; ?>