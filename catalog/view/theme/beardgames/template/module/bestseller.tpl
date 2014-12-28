<div id="featured" class="box">
  <h1><?php echo $heading_title; ?></h1>
  <div class="box-content clearfix">
    <?php foreach ($products as $key => $product) { ?>
        <div class="box-product <?php if($key == 0) echo "first"; ?>">
          <a href="<?php echo $product['href']; ?>">
            <div class="image">
            <?php if ($product['thumb']) { ?>
            <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['fullname']; ?>" title="<?php echo $product['fullname']; ?>" />
            <?php } else { ?>
            <img src="<?php echo $no_image; ?>" alt="<?php echo $product['fullname']; ?>" title="<?php echo $product['fullname']; ?>" />
            <?php } ?>
            <?php if ($product['price']) { ?>
            <div class="price">
              <?php if (!$product['special']) { ?>
              <?php 
                $price = $product['price']; 
                echo $price." ".$this->currency->getSymbolRight();
              ?>
              <?php } else { ?>
              <?php echo $product['special']." ".$this->currency->getSymbolRight(); ?>
              <?php } ?>
            </div>
            <?php } ?>
            <?php if ($product['special']) { ?>
              <?php $discount = round((($product['price'] - $product['special']) / $product['price']) * 100); ?>
              <div class="discount">
                <img src="catalog/view/theme/beardgames/image/discount.png" />
                <div>-<?php echo $discount; ?>%</div>
              </div>
            <?php } ?>
              <div class="fade" title="<?php echo $product['fullname']; ?>"></div>
            </div>
          </a>
          <div class="name"><a href="<?php echo $product['href']; ?>" title="<?php echo $product['fullname']; ?>"><?php echo $product['name']; ?></a></div>          
          <?php /*if ($product['rating']) { ?>
          <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
          <?php }*/ ?>
          <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><i class="fa fa-shopping-cart"></i> KÖP</a></div>
          <img src="catalog/view/theme/beardgames/image/box-shadow.png" class="box-shadow" />
        </div>
    <?php } ?>
  </div>
</div>
