<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="clearfix"><?php echo $content_top; ?>
  <h1><i class="fa fa-lock"></i> <?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" id="password_form" method="post" enctype="multipart/form-data">
    <div class="content left checkout-box">
      <h2><?php echo $text_password; ?></h2>
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="password" name="password" value="<?php echo $password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_confirm; ?></td>
          <td><input type="password" name="confirm" value="<?php echo $confirm; ?>" />
            <?php if ($error_confirm) { ?>
            <span class="error"><?php echo $error_confirm; ?></span>
            <?php } ?></td>
        </tr>
      </table>
      <div class="buttons">
		<a class="button" onclick="$('#password_form').submit();"><i class="fa fa-check"></i> Spara</a>
        <a href="<?php echo $back; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a>
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>