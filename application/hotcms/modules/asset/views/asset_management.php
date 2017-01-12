<div style="float:left; width: 300px">
<form id="widget_upload" target="widget_upload_target" action="/hotcms/media-library/ajax_upload/<?php echo $asset_category_id ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <div id="widget_asset_upload_error" class="error"></div>
  <div class="row">
   <div id="widget_asset_name_error" class="error"></div>
   <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'asset_name');?>
   <?php echo form_input($fields['asset_name_input']); ?>
  </div>
  <div class="row">
   <div id="widget_asset_description_error "class="error"></div>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'asset_description');?>
   <?php echo form_textarea($fields['asset_description_input']); ?>
  </div>
  <div class="row">
   <div class="error">File is required</div>
   <?php echo form_label(lang( 'hotcms_file' ).' '.lang( 'hotcms__colon' ), 'asset_file');?>
   <?php echo form_upload($fields['asset_file_input']); ?>
  </div>
  <div class="submit">
    <input type="submit" class="submit" value="<?php echo lang( 'hotcms_upload' ) ?>" />
    <?php echo form_hidden($fields['hidden_fields']); ?>
  </div>
  <iframe id="widget_upload_target" name="widget_upload_target" src="" style="width:0;height:0;border:0px solid #000;"></iframe>
</form>
</div>
<div style="float:left; width: 300px">
  <?php foreach($images as $image) { ?>
    <div style="clear: both">
    <?php printf('<img src="http://%s%s/%s.%s" alt="%s" />', $this->session->userdata( 'siteURL' ), $image->thumbnail,$image->file_name.'_thumb', $image->extension, $image->file_name); ?><span><?php echo $image->name ?></span>
      <a onclick="widgetDeleteImage(<?php echo $image->id ?>)">delete</a>
    </div>
  <?php } ?>
</div>
<script id="bind_interface" type="text/javascript">
jQuery( document ).ready( function() {

  jQuery('#widget_upload .error').css('display','none');

  jQuery('#widget_upload').submit(function(e) {
      //e.preventDefault();
      var error_flag = false;
      if(jQuery('#widget_upload #asset_name').val() == '')
      {
        jQuery('#widget_upload #widget_asset_name_error').html('Image name is required').show();
        error_flag = true;
      }
      else {
        jQuery('#widget_upload #widget_asset_name_error').hide();
      }
      if(jQuery('#widget_upload #asset_description').val() == '')
      {
        jQuery('#widget_upload #widget_asset_description_error').html('Image description is required').show();
        error_flag = true;
      } else {
        jQuery('#widget_upload #widget_asset_description_error').hide();
      }
      //console.log(error_flag);
      return !error_flag;
   });

  document.getElementById("widget_upload_target").onload = widgetUploadDone;

});

function widgetDeleteImage(id)
{
  var url = "http://<?php echo $this->session->userdata( 'siteURL' ) ?>/hotcms/media-library/ajax_delete/" + id;
  //alert(url);
  jQuery.get(url, function(data){
    jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + jQuery("input[name='editing_section']").val());
  });
}

function widgetUploadDone()
{
  var ret = frames['widget_upload_target'].document.getElementsByTagName("body")[0].innerHTML;
  //alert(ret);
  //console.log(ret);
  var data = eval("("+ret+")"); //Parse JSON
  //console.log(data);
  if(data.status == "success") {
    var page_id = jQuery("input[name='page_id']").val();
    jQuery('#widget_upload input[name=asset_category_id]').val(data.asset_category_id);
    jQuery('#widget_upload #asset_name').val('');
    jQuery('#widget_upload #asset_description').val()
    jQuery('#widget_upload #asset_file').val()
    jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + page_id + "/" + jQuery("input[name='editing_section']").val());
  } else {
    jQuery('#widget_upload #widget_asset_upload_error').html(data.msg).show();
  }
}
</script>