<div style="float:left; width: 300px">
<form id="random_widget_upload" target="random_widget_upload_target" action="/hotcms/randominfo/upload/<?php echo $page_section_id ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <div id="random_widget_asset_upload_error" class="error"></div>
  <div class="row">       
   <div id="random_widget_asset_name_error" class="error"></div>
   <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'asset_name');?>
   <?php echo form_input($asset_name_input); ?>
  </div>
  <div class="row">         
   <div id="random_widget_asset_description_error "class="error"></div>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'asset_description');?>
   <?php echo form_textarea($asset_description_input); ?>                     
  </div>  
  <div class="row">         
   <div id="random_widget_asset_type_error" class="error"></div>
   <?php echo form_label(lang( 'hotcms_asset_type' ).' '.lang( 'hotcms__colon' ), 'asset_type');?>
   <?php echo form_dropdown('asset_type', $asset_type, 1); ?>                     
  </div>     
  <div class="row">         
   <div class="error">File is required</div>
   <?php echo form_label(lang( 'hotcms_file' ).' '.lang( 'hotcms__colon' ), 'asset_file');?>
   <?php echo form_upload($asset_file_input); ?>                     
  </div>    
  <div class="submit">
    <input type="submit" class="submit" value="<?php echo lang( 'hotcms_upload' ) ?>" />
     <?php echo form_hidden("asset_category_id", $asset_category_id); ?>
  </div>
  <iframe id="random_widget_upload_target" name="random_widget_upload_target" src="" style="width:0;height:0;border:0px solid #000;"></iframe>  
</form>
<form method="post" id="formRandomInfo" name="formRandomInfo" class="configform" onsubmit="return config_widget(this);">
  <?php echo form_hidden("asset_category_id", $asset_category_id); ?>
  <!-- <input type="submit" class="triangle" name="Submit" value="Save" /> -->
</form>
</div>
<div style="float:left; width: 300px">
  <?php foreach($images as $image) { ?>
    <div style="clear: both">
    <?php printf('<img src="http://%s%s/%s.%s" alt="%s" />', $this->session->userdata( 'siteURL' ), $image->thumbnail,$image->file_name.'_thumb', $image->extension, $image->file_name); ?><span><?php echo $image->name ?></span>
      <a onclick="randomWidgetDeleteImage(<?php echo $image->id ?>)">delete</a>
    </div>
  <?php } ?>
</div>
<script id="bind_interface" type="text/javascript">
jQuery( document ).ready( function() {

  jQuery('#random_widget_upload .error').css('display','none');    

  jQuery('#random_widget_upload').submit(function(e) {
      //e.preventDefault();   
      var error_flag = false;
      if(jQuery('#random_widget_upload #asset_name').val() == '')
      {
        jQuery('#random_widget_upload #random_widget_asset_name_error').html('Image name is required').show();
        error_flag = true;
      }
      else {
        jQuery('#random_widget_upload #random_widget_asset_name_error').hide(); 
      }
      if(jQuery('#random_widget_upload #asset_description').val() == '')
      {
        jQuery('#random_widget_upload #random_widget_asset_description_error').html('Image description is required').show();          
        error_flag = true;
      } else {
        jQuery('#random_widget_upload #random_widget_asset_description_error').hide();          
      }       
      //console.log(error_flag);
      return !error_flag;
   });

  document.getElementById("random_widget_upload_target").onload = random_uploadDone;        

});

function randomWidgetDeleteImage(id)
{
  var url = "http://<?php echo $this->session->userdata( 'siteURL' ) ?>/hotcms/media-library/ajax_delete/" + id;
  //alert(url);
  jQuery.get(url, function(data){
    jQuery("#widget-config").load("/hotcms/page/configsection/<?php echo $page_section_id ?>");        
    //jQuery('#formRandomInfo').submit();    
  });
}

function random_uploadDone()
{
  var ret = frames['random_widget_upload_target'].document.getElementsByTagName("body")[0].innerHTML;
  //alert(ret);
  //console.log(ret);
  var data = eval("("+ret+")"); //Parse JSON // Read the below explanations before passing judgment on me 
  //console.log(data);
  if(data.status == "success") {
    jQuery('#formRandomInfo input[name=asset_category_id]').val(data.asset_category_id);
    jQuery('#random_widget_upload input[name=asset_category_id]').val(data.asset_category_id);
    jQuery('#random_widget_upload #asset_name').val('');
    jQuery('#random_widget_upload #asset_description').val()
    jQuery('#random_widget_upload #asset_file').val()
    jQuery('#formRandomInfo').submit();
    jQuery("#widget-config").load("/hotcms/page/configsection/<?php echo $page_section_id ?>");        
  } else {
    jQuery('#random_widget_upload #random_widget_asset_upload_error').html(data.msg).show();
  }
}
</script>