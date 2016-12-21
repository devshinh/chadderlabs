<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <div class="row">
   <?php echo form_error('asset_type', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_asset_type' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_type'); ?>
   <?php echo form_dropdown('asset_type', $asset_type); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_name', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_name'); ?>
   <?php echo form_input($asset_name_input); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_description', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_description' ) . ' ' . lang( 'hotcms__colon' ), 'asset_description'); ?>
   <?php echo form_textarea($asset_description_input); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_categories', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_category' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_categories'); ?>
   <?php echo form_dropdown('asset_categories', $asset_categories); ?>
  </div>
  <div class="row">
   <?php echo form_error('asset_file', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_file' ).'<span class="red">*</span> ' . lang( 'hotcms__colon' ), 'asset_file', array('id' => 'file_label')); ?>
   <?php echo form_upload($asset_file_input); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_sd', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_sd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_sd'); ?>
   <?php echo form_upload($asset_sd_input); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_webmhd', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_webmhd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_webmhd'); ?>
   <?php echo form_upload($asset_webmhd_input); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_webmsd', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_webmsd' ) . ' ' . lang( 'hotcms__colon' ), 'asset_webmsd'); ?>
   <?php echo form_upload($asset_webmsd_input); ?>
  </div>
  <div class="row" style="display:none">
   <?php echo form_error('asset_poster', '<div class="error">', '</div>'); ?>
   <?php echo form_label(lang( 'hotcms_poster' ) . ' ' . lang( 'hotcms__colon' ), 'asset_poster'); ?>
   <?php echo form_upload($asset_poster_input); ?>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
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