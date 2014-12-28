'use strict';

$('#payson_link').hover(function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/payson.png');
},
function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/payson-gray.png');
});

$('#dhl_link').hover(function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/dhl.png');
},
function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/dhl-gray.png');
});

$('#bitpay_link').hover(function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/bitpay.png');
},
function() {
  $(this).attr('src', '/catalog/view/theme/beardgames/image/bitpay-gray.png');
});

var menu_search = false;
$(window).scroll(function(){
  var scroll_top = $(window).scrollTop();

  // Hide cart
  //$('#cart').removeClass('active');

  // Make menu sticky
  var menu = $('#menu');
  if(scroll_top >= 145) {
    if(menu.css('position') != 'fixed') {
      menu.css({
        position: 'fixed',
        top: '0px'
      });
    }
  } else if(scroll_top < 145) {
    if(menu.css('position') != 'absolute') {
      menu.css({
        position: 'absolute',
        top: '145px'
      });
    }
  }

  // Show menu tools
  var menu_tools = $('li.tool');
  var search_input = $('#search_input');
  var search_input_dummy = $('#search_input_dummy');
  if(scroll_top >= 74) {
    if($('.snize-ac-results').css('position') != 'fixed') {
      $('.snize-ac-results').css({
        position: 'fixed',
        top: '50px'
      });
    }
    if(menu_search === false) {
      menu_search = true;
      search_input_dummy.replaceWith(search_input).appendTo('#search');
      if(search_input.val() === '') {
        search_input.stop();
        search_input.css({
          width: 30,
          display: 'none'
        });
        $('li.tool.search a.icon').show();
      } else {
        search_input.stop();
        search_input.css({
          width: 200,
          display: 'inline'
        });
        $('li.tool.search a.icon').hide();
      }

      menu_tools.fadeIn('fast');
      if(!$('#cart').hasClass('in_menu')) {
        $('#cart').addClass('in_menu');
      }
    }
  } else if(scroll_top < 74) {
      if($('.snize-ac-results').css('position') != 'absolute') {
      $('.snize-ac-results').css({
        position: 'absolute',
        top: '54px'
      });
    }
    if(menu_search === true) {
      menu_search = false;
      search_input.stop();
      search_input_dummy.replaceWith(search_input).appendTo('#menu_search');
      search_input.css({
        width: 262,
        display: 'inline-block'
      });
      repositionLivesearch();
      menu_tools.fadeOut('fast');
      if($('#cart').hasClass('in_menu')) {
        $('#cart').removeClass('in_menu');
      }
    }
  }
  repositionLivesearch();
});

$('li.tool.arrow-up a').click(function(event) {
  $('html, body').animate({
    scrollTop: '0px'
  }, 300);
  event.preventDefault();
});

$('li.tool.search').click(function(event) {
  $('li.tool.search a.icon').hide();
  $('#search_input').css({
    display: 'inline'
  }).animate({
    width: 200
  }, 200).focus();
  event.preventDefault();
});

$('#search_input').blur(function() {
  if(menu_search === true && $(this).val() === '') {
    $('#search_input').animate({
      width: 30
    }, 200, function() {
      $(this).hide();
      $('li.tool.search a.icon').show();
    });
  }
});

function showFilter() {
  $('#filter-icon').removeClass('fa-caret-square-o-down').addClass('fa-caret-square-o-up');
  $('div.filter.box').slideDown(150, 'swing');
}

function hideFilter() {
  $('#filter-icon').removeClass('fa-caret-square-o-up').addClass('fa-caret-square-o-down');
  $('div.filter.box').slideUp(150, 'swing');
}

/* exported toggleFilter */
function toggleFilter() {
  if($('div.filter.box').css('display') == 'block') {
    hideFilter();
  } else {
    showFilter();
  }
}

$('a.cart').live('click', function() {
  if(!$('#cart').hasClass('active')) {
    $('#cart').addClass('active');

    if(menu_search) {
      if(!$('#cart').hasClass('in_menu')) {
        $('#cart').addClass('in_menu');
      }
    } else {
      $('#cart').removeClass('in_menu');
    }
    
    $('#cart .checkout').append("<div class='loading'>Laddar... <img src='/image/loading.gif' /></div>");
    $('#cart .empty').html("Laddar... <img src='/image/loading.gif' />");
    $('#cart').load('index.php?route=module/cart #cart > *');
  } else {
    hideCart();
  }
});

// Hide cart when clicking outside
$(document).mouseup(function (e)
{
    var container = $('#cart');
    var container2 = $('a.cart');
    var search = $('#search_input');
    var search2 = $('#livesearch');

    if (!container.is(e.target) &&
        !container2.is(e.target) &&
        container.has(e.target).length === 0 &&
        container2.has(e.target).length === 0) // ... nor a descendant of the container
    {
        hideCart();
    }
    if (!search.is(e.target) &&
        search.has(e.target).length === 0 && 
        !search2.is(e.target) &&
        search2.has(e.target).length === 0) // ... nor a descendant of the container
    {
        hideSearch();
    }
});
function hideSearch() {
  $('#livesearch').fadeOut(100);
}
function hideCart() {
  $('#cart').removeClass('active');
}

/* global addToCart: true */
addToCart = function(product_id, quantity, element) {
  quantity = typeof(quantity) != 'undefined' ? quantity : 1;
  $('a.cart:visible').addClass('rotate').animate({
    bottom: '5px'
  }, 100, function() {
    $(this).removeClass('rotate').animate({
      bottom: '0px'
    }, 100);
  });
  var cart_total = ($('.cart-total:first').text()*1) + (quantity*1);
  $('.cart-total').text(cart_total).show();

  //var image = $(element).parents('.product').find('img.list_image');
  //var image_copy = image.clone();


  $.ajax({
    url: 'index.php?route=checkout/cart/add',
    type: 'post',
    data: 'product_id=' + product_id + '&quantity=' + quantity,
    dataType: 'json',
    success: function(json) {
      /*var location = '';
      $('.success, .warning, .attention, .information, .error').remove();
      
      if (json.redirect) {
        location = json.redirect;
      }*/
      
      /*if (json.success) {
        $('#notification').html('<div class="success" style="display: none;">' + json.success + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
        
        $('.success').fadeIn('slow');
        
        //$('html, body').animate({ scrollTop: 0 }, 'slow'); 
      }*/
    }
  });
};

/* global addToWishList: true */
addToWishList = function(product_id, insta_feedback, use_id) {
  var insta_feedback = insta_feedback || false;
  var use_id = use_id || false;
  
  $('a.wishlist:visible').addClass('rotate').animate({
    bottom: '5px'
  }, 100, function() {
    $(this).removeClass('rotate').animate({
      bottom: '0px'
    }, 100);
  });

  if(insta_feedback) {
    if(use_id) {
      $('#wishlistbutton_'+product_id).hide();
      $('#wishlistaddedbutton_'+product_id).show();
    } else {
      $('a.button.wishlistbutton').hide();
      $('a.button.wishlistaddedbutton').show();
    }
  }

  //var wishlist_total = ($('.wishlist-total:first').text()*1) + 1;
  //$('.wishlist-total').text(wishlist_total).show();

  $.ajax({
    url: 'index.php?route=account/wishlist/add',
    type: 'post',
    data: 'product_id=' + product_id,
    dataType: 'json',
    success: function(json) {
      //$('.success, .warning, .attention, .information').remove();
            
      if (json.success) {
        /*$('#notification').html('<div class="success" style="display: none;">' + json.success + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
        $('.success').fadeIn('slow');*/
        $('.wishlist-total').text(json.total_count).show();
        $('.wishlist i').prop('title', json.total);
        //$('html, body').animate({ scrollTop: 0 }, 'slow');
      }
    }
  });
};

$(function() {
  $(window).bind("capsOn", function(event) {
    $("#capslocktext").fadeIn();
  });
  $(window).bind("capsOff", function(event) {
    $("#capslocktext").fadeOut();
  });
  $('.cart-total').each(function() {
    if($(this).text()*1 > 0) {
      $(this).show();
    }
  });
  $('.wishlist-total').each(function() {
    if($(this).text()*1 > 0) {
      $(this).show();
    }
  });
  $(window).capslockstate();
});