<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php echo $column_left; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><i class="fa fa-home"></i> <?php echo $heading_title; ?></h1>
  <div class="content left checkout-box adress-list">
    <h2><?php echo $text_address_book; ?></h2>
    <?php foreach ($addresses as $result) { ?>
      <table style="width: 100%;">
        <tr>
          <td><?php echo $result['address']; ?></td>
          <td style="text-align: right; vertical-align: top;"><a href="<?php echo $result['update']; ?>" class="button"><i class="fa fa-pencil"></i></a> &nbsp; <a href="<?php echo $result['delete']; ?>" class="button"><i class="fa fa-times"></i></a></td>
        </tr>
      </table>
    <?php } ?>
    <div class="buttons">
		<a href="<?php echo $insert; ?>" class="button"><i class="fa fa-arrow-circle-right"></i> <?php echo $button_new_address; ?></a>
      <a href="<?php echo $back; ?>" class="button"><i class="fa fa-arrow-circle-left"></i> Mitt konto</a>
    </div>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>