<?php echo $header; ?><?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <?php echo $text_message; ?>
  <?php echo $content_bottom; ?>
  <?php echo (isset($prisjakt_image)) ? $prisjakt_image : ''; ?></div>
<?php echo $footer; ?>