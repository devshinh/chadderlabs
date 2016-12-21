<?php
  switch ($currentItem->type) {
    case 3:
      $label = 'MP4 HD';
      break;
    case 4:
      $label = 'MP3 File';
      break;
    default:
      $label = 'File';
  }
?>
<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    <?php echo form_hidden($form['hidden']); ?>
    <div class="row">
    <?php echo form_error('asset_categories', '<div class="error">', '</div>'); ?>
    <?php echo form_label(lang('hotcms_category').'<span class="red">*</span> ' . lang('hotcms__colon'), 'asset_categories'); ?>
    <?php echo form_dropdown('asset_categories', $asset_categories, $currentItem->asset_category_id); ?>
    </div>
    <div class="row" style="padding-top:5px">
     <?php echo form_error('name', '<div class="error">','</div>');?>
     <?php echo form_label(lang('hotcms_name').'<span class="red">*</span> ' . lang('hotcms__colon'), 'name');?>
     <?php echo form_input($form['name_input']); ?>
    </div>
    <div class="row">
    <?php
      echo form_error('asset_file', '<div class="error" id="asset_file_error">', '</div>');
      echo form_label($label . '<span class="red">*</span> ' . lang('hotcms__colon'), 'asset_file');
      echo form_upload($form['asset_file_input']);
      if (isset($currentItem->file_name) && $currentItem->file_name > '') {
        echo ' &nbsp; ' . $currentItem->file_name . '.' . $currentItem->extension;
      }
    ?>
    </div>
    <div class="row" <?php if ($currentItem->type != 3) { echo 'style="display:none"'; } ?>>
    <?php
      echo form_error('asset_sd', '<div class="error" id="asset_sd_error">', '</div>');
      echo form_label(lang('hotcms_sd') . ' ' . lang('hotcms__colon'), 'asset_sd');
      echo form_upload($form['asset_sd_input']);
      if (isset($currentItem->mp4_sd) && $currentItem->mp4_sd > '') {
        echo ' &nbsp; ' . $currentItem->mp4_sd;
      }
    ?>
    </div>
    <div class="row" <?php if ($currentItem->type != 3) { echo 'style="display:none"'; } ?>>
    <?php
      echo form_error('asset_webmhd', '<div class="error" id="asset_webmhd_error">', '</div>');
      echo form_label(lang('hotcms_webmhd') . ' ' . lang('hotcms__colon'), 'asset_webmhd');
      echo form_upload($form['asset_webmhd_input']);
      if (isset($currentItem->webm_hd) && $currentItem->webm_hd > '') {
        echo ' &nbsp; ' . $currentItem->webm_hd;
      }
    ?>
    </div>
    <div class="row" <?php if ($currentItem->type != 3) { echo 'style="display:none"'; } ?>>
    <?php
      echo form_error('asset_webmsd', '<div class="error" id="asset_webmsd_error">', '</div>');
      echo form_label(lang('hotcms_webmsd') . ' ' . lang('hotcms__colon'), 'asset_webmsd');
      echo form_upload($form['asset_webmsd_input']);
      if (isset($currentItem->webm_sd) && $currentItem->webm_sd > '') {
        echo ' &nbsp; ' . $currentItem->webm_sd;
      }
    ?>
    </div>
    <div class="row" <?php if ($currentItem->type != 3) { echo 'style="display:none"'; } ?>>
    <?php
      echo form_error('asset_poster', '<div class="error" id="asset_poster_error">', '</div>');
      echo form_label(lang('hotcms_poster') . ' ' . lang('hotcms__colon'), 'asset_poster');
      echo form_upload($form['asset_poster_input']);
      if (isset($currentItem->poster) && $currentItem->poster > '') {
        echo ' &nbsp; ' . $currentItem->poster;
      }
    ?>
    </div>
    <div class="row">
     <?php echo form_error('description', '<div class="error">','</div>');?>
     <?php echo form_label(lang('hotcms_description') . ' ' . lang('hotcms__colon'), 'description_code');?>
     <?php echo form_textarea($form['description_input']); ?>
    </div>
    <div class="row">
     <!-- label>Preview :</label -->
     <?php echo $currentItem->full_html; ?>
    </div>
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
</div>

<div id="dialog-modal" title="File Already Exists" style="display:none;">
  <br /><p>There are files on the server with the same name as the one(s) you are trying to upload.</p><br />
  <p>Click <b>Overwrite</b> to ignore this error and try to upload the file(s) with the same name again. Any existing files on the server will be overwritten.</p><br />
  <p>Click <b>Cancel</b> to rename your file(s) and try upload again.</p>
</div>

<script type="text/javascript">
jQuery(function() {
  jQuery("#dialog-modal").dialog({
    autoOpen: false,
    height: 280,
    width: 560,
    modal: true,
    buttons: {
      "Overwrite": function() {
        jQuery("input[name=asset_file_overwrite]").val("1");
        jQuery("input[name=asset_sd_overwrite]").val("1");
        jQuery("input[name=asset_webmhd_overwrite]").val("1");
        jQuery("input[name=asset_webmsd_overwrite]").val("1");
        jQuery("input[name=asset_poster_overwrite]").val("1");
        jQuery(this).dialog("close");
      },
      Cancel: function() {
        jQuery(this).dialog("close");
      }
    }
  });
  if ((jQuery('#asset_file_error').length > 0 && jQuery('input[name=asset_file_overwrite]').length > 0 && jQuery('input[name=asset_file_overwrite]').val != "1")
    || (jQuery('#asset_sd_error').length > 0 && jQuery('input[name=asset_sd_overwrite]').length > 0 && jQuery('input[name=asset_sd_overwrite]').val != "1")
    || (jQuery('#asset_webmhd_error').length > 0 && jQuery('input[name=asset_webmhd_overwrite]').length > 0 && jQuery('input[name=asset_webmhd_overwrite]').val != "1")
    || (jQuery('#asset_webmsd_error').length > 0 && jQuery('input[name=asset_webmsd_overwrite]').length > 0 && jQuery('input[name=asset_webmsd_overwrite]').val != "1")
    || (jQuery('#asset_poster_error').length > 0 && jQuery('input[name=asset_poster_overwrite]').length > 0 && jQuery('input[name=asset_poster_overwrite]').val != "1")
    ) {
    jQuery("#dialog-modal").dialog("open");
  }
});
</script>
