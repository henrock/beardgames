<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if(isset($og_data)) { ?>
<?php echo $og_data; ?>
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/beardgames/stylesheet/stylesheet.css" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<link href='http://fonts.googleapis.com/css?family=PT+Sans:700italic|Fugaz+One|Alegreya+SC:400,700,900|Open+Sans:400italic,400,600,700,800' rel='stylesheet' type='text/css'>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<!--[if IE 7]> 
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->
<?php if ($stores) { ?>
<script type="text/javascript"><!--
$(document).ready(function() {
<?php foreach ($stores as $store) { ?>
$('body').prepend('<iframe src="<?php echo $store; ?>" style="display: none;"></iframe>');
<?php } ?>
});
//--></script>
<?php } ?>
<?php echo $google_analytics; ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/beardgames/stylesheet/beardgames.min.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/beardgames/stylesheet/corel.css" />
</head>
<body>
 <div id="clouds"></div>
 <div id="top-clouds"></div>
 <?php if ($categories) { ?>
<div id="menu">
  <ul>
    <li class="cart">      
      <?php echo $cart; ?>
    </li>
    <?php foreach ($categories as $category) { ?>
    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
      <?php if ($category['children']) { ?>
      <div class="menu">
        <?php for ($i = 0; $i < count($category['children']);) { ?>
        <ul>
          <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
          <?php for (; $i < $j; $i++) { ?>
          <?php if (isset($category['children'][$i])) { ?>
          <li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
          <?php } ?>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>
      <?php } ?>
    </li>
    <?php } ?>
    <li class="tool arrow-up"><a class="fa fa-angle-double-up" title="Upp"></a></li>    
    <li class="tool">
      <a class="cart">
        <i class="fa fa-shopping-cart fa-lg" title="<?php echo $text_shopping_cart; ?>"></i>
        <span class="cart-total"><?php echo $total_quantity; ?></span>
      </a>
    </li>
    <li class="tool"><a href="<?php echo $account; ?>"><i class="fa fa-user fa-lg" title="<?php echo $text_account; ?>"></i></a></li>
    <li class="tool">
      <a href="<?php echo $wishlist; ?>" class="wishlist">
        <i class="fa fa-star fa-lg" title="<?php echo $text_wishlist; ?>"></i>
        <span class="wishlist-total"><?php echo $total_wishlist; ?></span>
      </a>
    </li>
    <li class="tool"><a href="<?php echo $home; ?>"><i class="fa fa-home fa-lg" title="<?php echo $text_home; ?>"></i></a></li>
    <li class="tool search" id="menu_search"><a class="icon"><i class="fa fa-search fa-lg"></i></a><i id="search_input_dummy"></i></li>
  </ul>  
</div>
<?php } ?>
<div id="container-holder">
<div id="header-holder">
  <div id="header">
    <?php if ($logo) { ?>
    <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
    <?php } ?>
    <?php echo $language; ?>
    <?php echo $currency; ?>
    <div id="search">
      <div class="icon"><i class="fa fa-search fa-lg"></i><img src="/image/loading-white.gif" class="loading" /></div>
      <input type="text" id="search_input" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" />
    </div>
    <div class="links">
      <a href="<?php echo $home; ?>"><i class="fa fa-home fa-2x" title="<?php echo $text_home; ?>"></i></a>
      <a href="<?php echo $wishlist; ?>" id="wishlist" class="wishlist">
        <i class="fa fa-star fa-2x" title="<?php echo $text_wishlist; ?>"></i>
        <span class="wishlist-total"><?php echo $total_wishlist; ?></span>
      </a>
      <a href="<?php echo $account; ?>"><i class="fa fa-user fa-2x" title="<?php echo $text_account; ?>"></i></a>
      <a class="cart">
        <i class="fa fa-shopping-cart fa-2x" title="<?php echo $text_shopping_cart; ?>"></i>
        <span class="cart-total"><?php echo $total_quantity; ?></span>
      </a>
    </div>
  </div>
</div>
<?php if ($error) { ?>
    
    <div class="warning"><?php echo $error ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
    
<?php } ?>
<div id="notification"></div>
<div id="container" class="clearfix">
<?php if($logged) { ?>
<div id="logged-in">
  <table cellspacing="0" cellpadding="0">
  <tr>
    <td align="right" valign="bottom">
      <p class="loggedin-userdetails">
        <span class="loggedin-username"><?php echo $users_name; ?></span><br/>
        <span class="loggedin-usermail"><?php echo $users_email; ?></span>
      </p>
    </td>
    <td width="40">
      <a href="http://staging.beardgames.se/account/account"><img src="http://www.gravatar.com/avatar/<?php echo $users_email_hash; ?>?s=40&d=http://staging.beardgames.se/catalog/view/theme/beardgames/image/default-gravatar.jpg" title="Mitt konto" /></a>
    </td>
  </tr>
  </table>
</div>
<?php } ?>