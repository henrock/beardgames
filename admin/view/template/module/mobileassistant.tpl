<?php if ($is_ver20) { ?>

    <?php echo $header; ?><?php echo $column_left; ?>
    <div id="content">
        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <button type="button" id="button_save_continue" form="form_mobassist" data-toggle="tooltip" title="<?php echo $button_save_continue; ?>" class="btn btn-success"><i class="fa fa-check"></i></button>
                    <button type="button" id="button_save_not_continue" form="form_mobassist" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
                <h1><?php echo $heading_title; ?></h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>

            <?php if ($saving_success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $saving_success; ?>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>
            <?php } ?>

            <?php if ($message_info) { ?>
                <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $message_info; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                </div>
                <div class="panel-body">
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_mobassist" class="form-horizontal">
                        <input type="hidden" name="save_continue" id="save_continue" value="0">
                        <div class="form-group">
                            <div style="margin-right: 18px; margin-bottom: 15px; float: right">
                                <span style="margin-right: 25px">
                                    <?php echo $module_version; ?> <b><?php echo $connector_version; ?></b>
                                </span>
                                <?php echo $useful_links; ?>
                                <a href="http://www.opencart.com/index.php?route=extension/extension/info&extension_id=19950" class="link" target="_blank"><?php echo $check_new_version; ?></a> |
                                <a href="https://support.emagicone.com/submit_ticket" class="link" target="_blank"><?php echo $submit_ticket; ?></a> |
                                <a href="http://mobile-store-assistant-help.emagicone.com/4-opencart-mobile-assistant-installation-instructions" class="link" target="_blank"><?php echo $documentation; ?></a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                            <div class="col-sm-10">
                                <select name="mobassist_status" id="input-status" class="form-control">
                                    <?php if ($settings['mobassist_status']) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="mobassist_login"><span data-toggle="tooltip" title="<?php echo $help_login; ?>"><?php echo $entry_login; ?></span></label>
                            <div class="col-sm-10">
                                <input type="text" id="mobassist_login" name="mobassist_login" value="<?php echo $settings['mobassist_login']; ?>" placeholder="<?php echo $entry_login; ?>" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="mobassist_pass"><span data-toggle="tooltip" title="<?php echo $help_pass; ?>"><?php echo $entry_pass; ?></span></label>
                            <div class="col-sm-10">
                                <input type="password" id="mobassist_pass" name="mobassist_pass" value="<?php echo $settings['mobassist_pass']; ?>" placeholder="<?php echo $entry_pass; ?>" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="mobassist_qr"><span data-toggle="tooltip" title="<?php echo $help_qr; ?>"><?php echo $entry_qr; ?></span></label>
                            <div class="col-sm-10">
                                <?php if (isset($qrcode_url)) { ?>
                                <div style="position: relative; width: 100%">
                                    <img id="mobassist_qr_code" src="<?php echo $qrcode_url ?>"/>
                                    <div id="mobassist_qr_code_changed" style="display: none; z-index: 1000; text-align: center; position: absolute; top: 0; left: 0; height: 100%;">
                                        <div style="position: relative; width: 100%; height: 100%;">
                                            <div style="background: #fff; opacity: 0.9; position: absolute; height: 100%; width: 100%">&nbsp;</div>
                                            <div style="font-size: 16px; color: #DF0101; width: 100%; text-align: center; padding-top: 45px; position: absolute; font-weight: bold;"><?php echo $error_login_details_changed ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                    <b>Error:</b> Directory "/admin/model/mobileassistant/phpqrcode/img" is not writable for generate QR-code images. Set the permission of this folder to 777.
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <?php echo $header; ?>
    <div id="content">
        <div class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php } ?>
        </div>
        <?php if ($error_warning) { ?>
            <div class="warning"><?php echo $error_warning; ?></div>
        <?php } ?>
        <?php if ($saving_success) { ?>
            <div class="success"><?php echo $saving_success; ?></div>
        <?php } ?>

        <?php if ($message_info) { ?>
            <div class="warning"><?php echo $message_info; ?></div>
        <?php } ?>

        <div class="box">
            <div class="heading">
                <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
                <div class="buttons"><a id="button_save_continue" class="button"><?php echo $button_save_continue; ?></a> <a id="button_save_not_continue" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
            </div>
            <div class="content">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_mobassist">
                    <input type="hidden" name="save_continue" id="save_continue" value="0">
                    <table class="form">
                        <tr>
                            <td colspan="2">
                                <div style="margin-bottom: 15px">
                                    <span style="margin-right: 25px">
                                        <?php echo $module_version; ?> <b><?php echo $connector_version; ?></b>
                                    </span>
                                    <?php echo $useful_links; ?>
                                    <a href="http://www.opencart.com/index.php?route=extension/extension/info&extension_id=19950" class="link" target="_blank"><?php echo $check_new_version; ?></a> |
                                    <a href="https://support.emagicone.com/submit_ticket" class="link" target="_blank"><?php echo $submit_ticket; ?></a> |
                                    <a href="http://mobile-store-assistant-help.emagicone.com/4-opencart-mobile-assistant-installation-instructions" class="link" target="_blank"><?php echo $documentation; ?></a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td><?php echo $entry_status; ?></td>
                            <td>
                                <select name="mobassist_status" style="float: left">
                                    <?php if ($settings['mobassist_status']) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_login; ?><br /><span class="help">(<?php echo $help_login; ?>)</span></td>
                            <td><input type="text" id="mobassist_login" name="mobassist_login" value="<?php echo $settings['mobassist_login']; ?>" /></td>
                        </tr>

                        <tr>
                            <td><?php echo $entry_pass; ?><br /><span class="help">(<?php echo $help_pass; ?>)</span></td>
                            <td><input type="password" id="mobassist_pass" name="mobassist_pass" value="<?php echo $settings['mobassist_pass']; ?>" /></td>
                        </tr>

                        <tr>
                            <td><?php echo $entry_qr; ?><br /><span class="help">(<?php echo $help_qr; ?>)</span></td>
                            <td>
                                <?php if ($qrcode_url) { ?>
                                <div style="position: relative; width: 100%">
                                    <img id="mobassist_qr_code" src="<?php echo $qrcode_url ?>"/>
                                    <div id="mobassist_qr_code_changed" style="display: none; z-index: 1000; text-align: center; position: absolute; top: 0; left: 0; height: 100%;">
                                        <div style="position: relative; width: 100%; height: 100%;">
                                            <div style="background: #fff; opacity: 0.9; position: absolute; height: 100%; width: 100%">&nbsp;</div>
                                            <div style="font-size: 16px; color: #DF0101; width: 100%; text-align: center; padding-top: 45px; position: absolute; font-weight: bold;"><?php echo $error_login_details_changed ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                    <b>Directory "/admin/model/mobileassistant/phpqrcode/img" is not writable for create QR-code images.</b>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>

                </form>
            </div>
        </div>
    </div>

<?php } ?>

<script type="text/javascript"><!--
    $(document).ready(function() {
        setTimeout(
                function() {
                    $('.success').hide('fast');
                    $('.alert-success').hide('fast');
                },
                3000
        );

        $('#button_save_continue').on('click', function () {
            $('#save_continue').val('1');
            $('#form_mobassist').submit();
        });
        $('#button_save_not_continue').on('click', function () {
            $('#save_continue').val('0');
            $('#form_mobassist').submit();
        });

        var mobassist_login = $("#mobassist_login");
        var mobassist_pass = $("#mobassist_pass");
        var _old_login = $(mobassist_login).val();
        var _old_pass = $(mobassist_pass).val();

        var onCredetChange = function() {
            var mobassist_qr_code_changed = $('#mobassist_qr_code_changed');

            if(_old_login != $(mobassist_login).val() || _old_pass != $(mobassist_pass).val()) {
                var qr = $("#mobassist_qr_code");
                if($(qr).width() > 0 && $(qr).attr('src') != '') {
                    $(mobassist_qr_code_changed).width($(qr).width()).show('fast');
                } else {
                    $(mobassist_qr_code_changed).hide('fast');
                }
            } else {
                $(mobassist_qr_code_changed).hide('fast');
            }
        };

        $(mobassist_login).on('keyup', function () {
            onCredetChange();
        });

        $(mobassist_pass).on('keyup', function () {
            onCredetChange();
        });
    });
    //--></script>

<?php echo $footer; ?> 