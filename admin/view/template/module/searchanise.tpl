<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

/* [Searchanise] */
?>

<?php echo $header; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?>
      <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($notifications) { ?>
    <?php foreach ($notifications as $notification) { ?>
      <?php if ($notification['type'] == 'N') { ?>
        <div class="success"><?php echo $notification['text']; ?></div>
      <?php } ?>
      <?php if ($notification['type'] == 'W') { ?>
        <div class="warning"><?php echo $notification['text']; ?></div>
      <?php } ?>
    <?php } ?>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $text_heading_title; ?></h1>
      <div class="buttons">
        <a onclick="$('#searchanise_form').submit();" class="button"><?php echo $text_button_save; ?></a>
        <a href="<?php echo $cancel_button_link; ?>" class="button"><?php echo $text_button_cancel; ?></a>
      </div>
    </div>
    <div class="content">
      <div>
        <form action="<?php echo $action_link; ?>" method="post" enctype="multipart/form-data" id="searchanise_form">
          <table class="form">
            <tr>
              <td>
                <?php echo $text_status; ?>
              </td>
              <td>
                <select name="se_status">
                  <option value='1' <?php if ($se_status) { echo "selected"; } ?> ><?php echo $text_active; ?></option>
                  <option value='0' <?php if (!$se_status) { echo "selected"; } ?> ><?php echo $text_disabled; ?></option>
                </select>
              </td>
            </tr>
            <?php if ($stores) {?>
              <tr>
                <td>
                  <?php echo $text_stores; ?>
                </td>
                <td>
                  <select name="searchanise_module[store]" onchange="changeStore(this.value);">
                    <?php foreach ($stores as $store) { ?>
                      <option value="<?php echo $store['store_id']; ?>" <?php if ($selected_store_id == $store['store_id']) { echo "selected"; } ?> ><?php echo $store['name']; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            <?php } ?>
          </table>
        </form>
      </div>
      <div class="snize" id="snize_container"></div>
      <script type="text/javascript">
        SearchaniseAdmin = {};
        SearchaniseAdmin.host = "<?php echo $searchaniseParams['host']; ?>";

        <?php if (!$se_status) { ?>
          SearchaniseAdmin.AddonStatus = 'disabled';
        <?php } elseif (!$searchaniseParams['is_registered']) {?>
          SearchaniseAdmin.PrivateKey = "";
          SearchaniseAdmin.ConnectLink = "<?php echo $searchaniseParams['connect_link']; ?>";
          SearchaniseAdmin.AddonStatus = 'enabled';
          
        <?php } else { ?>
          SearchaniseAdmin.PrivateKey = "<?php echo $searchaniseParams['private_key']; ?>";
          SearchaniseAdmin.ReSyncLink = "<?php echo $searchaniseParams['re_sync_link']; ?>";
          SearchaniseAdmin.LastRequest = "<?php echo $searchaniseParams['last_request']; ?>";
          SearchaniseAdmin.LastResync = "<?php echo $searchaniseParams['last_resync']; ?>";
          
          SearchaniseAdmin.ConnectLink = "<?php echo $searchaniseParams['connect_link']; ?>";
          SearchaniseAdmin.AddonStatus = 'enabled';

          <?php if (!empty($searchaniseParams['engines_data']) && is_array($searchaniseParams['engines_data'])) { ?>
            SearchaniseAdmin.Engines = [];
            <?php foreach ($searchaniseParams['engines_data'] as $engine_data) { ?>
              SearchaniseAdmin.Engines.push({
                PrivateKey: "<?php echo $engine_data['private_key'] ?>",
                LangCode: "<?php echo $engine_data['lang_code'] ?>",
                Name: "<?php echo $engine_data['language_name'] ?>",
                ExportStatus: "<?php echo $engine_data['import_status'] ?>"
              });
            <?php } ?>
          <?php } ?>
        <?php } ?>
      </script>

      <script type="text/javascript" src="<?php echo $searchaniseParams['host'] . '/js/init.js' ?>"></script>
    </div>
  </div>
</div>

<script type="text/javascript"><!--

function changeStore(store_id) {
    var url = "<?php echo $action_link; ?>" + "&amp;store_id=" + store_id;
    url = url.replace(/&amp;/g, '&');
  
    window.location.replace(url);
}

//--></script> 

<?php echo $footer; ?>