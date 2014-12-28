<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php //echo $column_left; ?><?php //echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" id="edit_form" method="post" enctype="multipart/form-data">
    <div class="content left checkout-box">
    <h2><?php echo $text_your_details; ?></h2>
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
          <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
            <?php if ($error_firstname) { ?>
            <span class="error"><?php echo $error_firstname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
          <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
            <?php if ($error_lastname) { ?>
            <span class="error"><?php echo $error_lastname; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_email; ?></td>
          <td><input type="text" name="email" value="<?php echo $email; ?>" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_telephone; ?><br/>(krav vid sms-avisering)</td>
          <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" /></td>
        </tr>
      </table>
      <div class="buttons">
		<a class="button" onclick="$('#edit_form').submit();"><i class="fa fa-check"></i> Spara</a>
        <a href="<?php echo $back; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a>
        
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>