<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="review-list">  
  <div class="rating">
    <?php for($i=0;$i<$review['rating'];$i++) { ?>
    <i class="fa fa-star"></i>
    <?php } ?>
    <?php for($i=5;$i>$review['rating'];$i--) { ?>
    <i class="fa fa-star-o"></i>
    <?php } ?><br/>
    <span class="date-added"><?php echo $review['date_added']; ?></span>
  </div>
  <div class="text"><?php echo $review['text']; ?></div>
  <div class="author"><b>- <?php echo $review['author']; ?></b></div>
</div>
<?php } ?>
<div class="pagination"><?php echo $pagination; ?></div>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>
