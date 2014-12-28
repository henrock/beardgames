<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-question-circle"></i> <?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" id="forgotten-form" method="post" enctype="multipart/form-data">
    <p><?php echo $text_email; ?></p>
    <div class="content left checkout-box">
      <h2><?php echo $text_your_email; ?></h2>
      <div class="content">
        <table class="form">
          <tr>
            <td><?php echo $entry_email; ?></td>
            <td><input type="text" name="email" value="" /></td>
          </tr>
        </table>
      </div>
      <div class="buttons">
        <a class="button" onclick="$('#forgotten-form').submit();"><i class="fa fa-check"></i> Skicka</a>
		<a href="<?php echo $back; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> <?php echo $button_back; ?></a>
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>