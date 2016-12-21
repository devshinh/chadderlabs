<form id="media-library-upload" target="upload_target" action="/hotcms/media-library/ajax_upload" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <div id="asset_upload_error" class="error"></div>
  <div class="row">       
   <div id="asset_name_error" class="error"></div>
   <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($asset_name_input); ?>
  </div>
  <div class="row">         
   <div id="asset_description_error"class="error"></div>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'description');?>
   <?php echo form_textarea($asset_description_input); ?>                     
  </div>  
  <div class="row">         
   <div id="asset_type_error"class="error"></div>
   <?php echo form_label(lang( 'hotcms_asset_type' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'asset_type');?>
   <?php echo form_dropdown('asset_type', $asset_type, 1); ?>                     
  </div>     
  <div class="row">         
   <div class="error">File is required</div>
   <?php echo form_label(lang( 'hotcms_file' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'asset_file');?>
   <?php echo form_upload($asset_file_input); ?>                     
  </div>    
  <div class="submit">
    <input type="submit" class="submit" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <input id="upload_cancel" type="button" class="button" value="<?php echo lang( 'hotcms_back' ) ?>" />
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  <iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #000;"></iframe>  
</form>