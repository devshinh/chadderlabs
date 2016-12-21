<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <?php echo form_hidden($form['hidden']); ?>
  <div class="row">
   <?php echo form_error('asset_type', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_asset_type' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_type'); ?>
   <?php echo form_dropdown('asset_type', $asset_types); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_categories', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_category' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_categories'); ?>
   <?php echo form_dropdown('asset_categories', $asset_categories, $asset_selected_category); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_name', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_name'); ?>
   <?php echo form_input($form['asset_name_input']); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_file', '<div class="error" id="asset_file_error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_file' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_file', array('id' => 'file_label')); ?>
   <?php echo form_upload($form['asset_file_input']); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_sd', '<div class="error" id="asset_sd_error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_sd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_sd'); ?>
   <?php echo form_upload($form['asset_sd_input']); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_webmhd', '<div class="error" id="asset_webmhd_error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_webmhd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_webmhd'); ?>
   <?php echo form_upload($form['asset_webmhd_input']); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_webmsd', '<div class="error" id="asset_webmsd_error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_webmsd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_webmsd'); ?>
   <?php echo form_upload($form['asset_webmsd_input']); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_poster', '<div class="error" id="asset_poster_error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_poster' ) . ' ' . lang( 'hotcms__colon' ), 'asset_poster'); ?>
   <?php echo form_upload($form['asset_poster_input']); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_description', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_description' ) . ' ' . lang( 'hotcms__colon' ), 'asset_description'); ?>
   <?php echo form_textarea($form['asset_description_input']); ?>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
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

  var asset_type = jQuery("select[name=asset_type]").val();
  if (asset_type == "3") {
    jQuery("label#file_label").html('MP4 HD <span class="red">*</span>:');
    jQuery("input[name=asset_sd]").parent().show();
    jQuery("input[name=asset_webmsd]").parent().show();
    jQuery("input[name=asset_webmhd]").parent().show();
    jQuery("input[name=asset_poster]").parent().show();
  }
  else if (asset_type == "4") {
    jQuery("label#file_label").html('MP3 File <span class="red">*</span>:');
  }
  jQuery("select[name=asset_type]").change(function(){
    var asset_type = jQuery(this).val();
    jQuery("label#file_label").html('File <span class="red">*</span>:');
    jQuery("input[name=asset_sd]").parent().hide();
    jQuery("input[name=asset_webmsd]").parent().hide();
    jQuery("input[name=asset_webmhd]").parent().hide();
    jQuery("input[name=asset_poster]").parent().hide();
    if (asset_type == "3") {
      jQuery("label#file_label").html('MP4 HD <span class="red">*</span>:');
      jQuery("input[name=asset_sd]").parent().show();
      jQuery("input[name=asset_webmsd]").parent().show();
      jQuery("input[name=asset_webmhd]").parent().show();
      jQuery("input[name=asset_poster]").parent().show();
    }
    else if (asset_type == "4") {
      jQuery("label#file_label").html('MP3 File <span class="red">*</span>:');
    }
  });
});
</script>