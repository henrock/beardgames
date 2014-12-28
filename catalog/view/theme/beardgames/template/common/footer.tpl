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
</body></html>