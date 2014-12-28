  </div>
  <div id="footer">
    <div id="footer_container">
      <div id="information">
        <div id="footer_links">
          <?php if ($informations) { ?>
            <?php foreach ($informations as $information) { ?>
            <a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a>
            <?php } ?>
          <?php } ?>
          <a href="/information/contact">Kontakta oss</a>
        </div>
        <div id="powered"><?php echo $powered; ?></div>
      </div>
      <div id="partners">
        <div id="partner_links">
          <a href="http://www.dhl.se" target="_blank"><img src="/catalog/view/theme/beardgames/image/dhl-gray.png" id="dhl_link" /></a>
          <a href="http://www.payson.se" target="_blank"><img src="/catalog/view/theme/beardgames/image/payson-gray.png" id="payson_link" /></a>
          <a href="http://www.bitpay.com" target="_blank"><img src="/catalog/view/theme/beardgames/image/bitpay-gray.png" id="bitpay_link" /></a>
        </div>
        <div id="social_links">
          <a href="http://www.facebook.com/beardgames" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a>
          <a href="http://www.twitter.com/BeardGamesSE" target="_blank"><i class="fa fa-twitter-square fa-3x"></i></a>
          <a href="http://www.instagram.com/beardgames_se" target="_blank"><i class="fa fa-instagram fa-3x"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="//cdn.jsdelivr.net/velocity/1.0.0/velocity.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery.capslockstate.js"></script>
<script type="text/javascript" src="catalog/view/javascript/beardgames.min.js"></script>
<script src="http://instore.biz/in.js" type="text/javascript"></script>

							<script>
								var livesearch_timeout = null;	
								function livesearchShowLoading() {
									$('#search i').hide();
									$('#search .loading').show();
								}							
								function livesearchHideLoading() {
									$('#search i').show();
									$('#search .loading').hide();
								}							
								function repositionLivesearch() { var i = (!!$("#livesearch").length ? $("#livesearch") : $("<ul id='livesearch'></ul>") ), s = $("#search_input"); i.css({ top: (s.offset().top+s.outerHeight()), left:s.offset().left, width: 296 }); }
								$(function(){
									var i = (!!$("#livesearch").length ? $("#livesearch") : $("<ul id='livesearch'></ul>") ), s = $("#search_input");
									$(window).resize(function(){ repositionLivesearch(); });
									s.keyup(function(e){
										switch (e.keyCode) {
											case 13:
												$(".active", i).length && (window.location = $(".active a", i).attr("href"));
												return false;
											break;
											case 40:
												($(".active", i).length ? $(".active", i).removeClass("active").next().addClass("active") : $("li:first", i).addClass("active"))
												return false;
											break;
											case 38:
												($(".active", i).length ? $(".active", i).removeClass("active").prev().addClass("active") : $("li:last", i).addClass("active"))
												return false;
											break;
											default:
												var query = s.val();
												if (query.length > 2) {
													livesearchShowLoading();
													clearTimeout(livesearch_timeout);
													livesearch_timeout = setTimeout(function() {
														$.getJSON(
															"<?php echo HTTP_SERVER; ?>?route=product/search/livesearch&search=" + query,
															function(data) {
																livesearchHideLoading();
																i.empty();
																$.each(data, function( k, v ) { 
																	i.append("<li><a href='"+v.href+"'><img src='"+v.img+"' alt='"+v.name+"'>"+(v.price ? "<i class='fa fa-cubes "+(v.stock ? "available' title='Finns i lager'" : "unavailable' title='Finns ej i lager'")+"></i><span>" : "")+v.name+(v.model ? "<small>"+v.model+"</small>" : '')+"</span><em>"+(v.price ? v.price+' kr' : '')+"</em></a></li>");
																});
																if(data.length == 0) {
																	hideSearch();
																} else {
																	showSearch();
																}
																i.remove(); $("body").prepend(i); repositionLivesearch();
															}
														);
													}, 500);
												} else {
													livesearchHideLoading();
													i.empty();
													hideSearch();
												}
										}
									}).blur(function(){ /*setTimeout(function(){ i.fadeOut(100) },100);*/ }).focus(function(){ repositionLivesearch(); if(i.find('li').length > 0) { i.show(); } });
								});
							</script>
							<style>
								[name=search] {
									outline: none;
								}
								#livesearch, #livesearch * {
									margin: 0;
									padding: 0;
									list-style: none;
								}
								#livesearch {
									position: absolute;
									width: 198px;
									top: 0px;
									background: #d9e6f7;
									z-index: 110;
									
									margin-top:10px;
									
									box-shadow: 2px 2px 2px rgba(51, 51, 51, 0.7);
									-webkit-border-radius: 5px 5px 5px 5px;
									-moz-border-radius: 5px 5px 5px 5px;
									-khtml-border-radius: 5px 5px 5px 5px;
									border-radius: 5px 5px 5px 5px;
								}
								
								
#livesearch {
	background: #d9e6f7;
	border: 1px solid #abb5c2;
}
#livesearch:after, #livesearch:before {
	bottom: 100%;
	left: 5%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}

#livesearch:after {
	border-color: rgba(217, 230, 247, 0);
	border-bottom-color: #d9e6f7;
	border-width: 10px;
	margin-left: -10px;
}
#livesearch:before {
	border-color: rgba(171, 181, 194, 0);
	border-bottom-color: #abb5c2;
	border-width: 11px;
	margin-left: -11px;
}
								
								
								#livesearch li {
									margin:5px;
								}
								#livesearch a {
									display: block;
									clear: both;
									overflow: hidden;
									line-height: 20px;
									padding: 10px;
									text-decoration: none;
									color:#3e4957;
								}
								#livesearch a:hover, #livesearch li.active a {
									background: #ffffff;
									color: #3e4957;
									-webkit-border-radius: 5px 5px 5px 5px;
									-moz-border-radius: 5px 5px 5px 5px;
									-khtml-border-radius: 5px 5px 5px 5px;
									border-radius: 5px 5px 5px 5px;
								}
								#livesearch img {
									float: left;
									width: 50px;
									height: 50px;
									margin-right: 10px;
								}
								#livesearch img[src=''] {
									display: none;
								}
								
								#livesearch i {
									float: right;
									display:block;
									margin: 10px 5px 5px 5px;
									font-size:21px;
								}
								
								.more {
									text-align: center;
									-webkit-border-radius: 0px 0px 5px 5px;
									-moz-border-radius: 0px 0px 5px 5px;
									-khtml-border-radius: 0px 0px 5px 5px;
									border-radius: 0px 0px 5px 5px;
								}
								#livesearch a span {
									display: block;
									color: #3e4957;
									font-style: normal;
									font-size:12px;
									font-weight: 800;
								}
								#livesearch a em {
									display: block;
									color: #3e4957;
									font-style: normal;
									font-size:12px;
									font-weight: 400;
								}
								#livesearch a:hover em, #livesearch li.active a em {
									color: 3e4957;
								}
								#livesearch strike {
									color: #aaaaaa;
								}
								#livesearch a:hover strike {
									color: lightblue;
								}
								#livesearch small {
									display: block;
								}
							</style>
							</body></html>
                        