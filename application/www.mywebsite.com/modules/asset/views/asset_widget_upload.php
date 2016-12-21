<div class="configform" style="clear:both; width: 700px">
<form id="widget_upload" target="widget_upload_target" action="/hotcms/media-library/ajax_upload/<?php echo $asset_category_id . '/' . $asset_type; ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
  <div id="widget_asset_upload_error" class="error"></div>
  <table>
    <!-- tr>
      <td colspan="3"><b>Upload Image:</b></td>
    </tr -->
    <tr>
      <td colspan="3"></td>
    </tr>
    <tr>
      <td>
        <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'asset_name');?>
        <?php echo form_input($fields['asset_name_input']); ?>
      </td>
      <td>
        <?php echo form_label(lang( 'hotcms_file' ).' '.lang( 'hotcms__colon' ), 'asset_file');?>
        <?php echo form_upload($fields['asset_file_input']); ?>                     
      </td>
      <td>
      </td>
    </tr>
    <tr>
     <td colspan="3">
        <input type="submit" value="<?php echo lang( 'hotcms_upload' ) ?>" class="red_button_smaller" />
        <?php echo form_hidden($fields['hidden_fields']); ?>
      </td>
    </tr>
    <tr>
      <td>
        <div id="widget_asset_name_error" class="error"></div>
      </td>
      <td>
        <div class="error">File is required</div>
      </td>
      <td></td>
    </tr>
  </table>
  <!-- <div class="row">         
    <div id="widget_asset_description_error "class="error"></div>
    <?php //echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'asset_description');?>
    <?php //echo form_textarea($fields['asset_description_input']); ?>                     
  </div> -->  
  <iframe id="widget_upload_target" name="widget_upload_target" src="" style="width:0;height:0;display:none;border:none;"></iframe>  
</form>
</div>
<script type="text/javascript">
jQuery( document ).ready( function() {

  document.getElementById("widget_upload_target").onload = parseResult;
  
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
      /*
      if(jQuery('#widget_upload #asset_description').val() == '')
      {
        jQuery('#widget_upload #widget_asset_description_error').html('Image description is required').show();          
        error_flag = true;
      } else {
        jQuery('#widget_upload #widget_asset_description_error').hide();          
      } */
      //console.log(error_flag);
      return !error_flag;
   });

});

function parseResult() {
  var ret = jQuery("#widget_upload_target").contents().find("body").text();
  if (ret > '') {
    var data = eval("("+ret+")");
    if(data.status == "success") {
      jQuery("input[name='section_updated']").val("1");
      if(typeof widgetPostUpload == 'function') {
        widgetPostUpload(data.asset_id);
      }
      else {
        //var page_id = jQuery("input[name='page_id']").val();
        //jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + page_id + "/" + jQuery("input[name='editing_section']").val());
        if (data.asset_type == 1) {
          jQuery.get("/hotcms/media-library/ajax_images/" + data.asset_category_id, null, function(data){
            try{
              var JSONobj = JSON.parse(data);
              if (JSONobj['formatted'] > '') {
                jQuery("#asset_images").html("<fieldset><legend>Group Images</legend>" + JSONobj['formatted'] + "</fieldset>");
              }
              if (JSONobj['raw'] > '') {
                jQuery("select[name='asset_id'] option").remove();
                jQuery("select[name='asset_id']").append('<option value=""> -- select image -- </option>');
                for (idx in JSONobj['raw']) {
                  jQuery("select[name='asset_id']").append('<option value="' + idx + '">' + JSONobj['raw'][idx]['name'] + '</option>');
                }
              }
              if (JSONobj['messages'] > '') {
                alert(JSONobj['messages']);
              }
            }
            catch(e){
              alert("Error: "+e.description);
            }
          });
          jQuery.get("/hotcms/media-library/ajax_image_upload/" + data.asset_category_id, null, function(data){
            jQuery("#asset_image_upload").html("<fieldset><legend>Upload Image to Group</legend>" + data + "</fieldset>");
          });
        }
        else if (data.asset_type == 2) {
          jQuery.get("/hotcms/media-library/ajax_files/" + data.asset_category_id, null, function(data){
            try{
              var JSONobj = JSON.parse(data);
              if (JSONobj['formatted'] > '') {
                jQuery("#asset_files").html("<fieldset><legend>Files</legend>" + JSONobj['formatted'] + "</fieldset>");
              }
              if (JSONobj['messages'] > '') {
                alert(JSONobj['messages']);
              }
            }
            catch(e){
              alert("Error: "+e.description);
            }
          });
          jQuery.get("/hotcms/media-library/ajax_asset_upload/" + data.asset_category_id + "/" + data.asset_type, null, function(data){
            jQuery("#asset_file_upload").html("<fieldset><legend>Upload File to Group</legend>" + data + "</fieldset>");
          });
        }
        //if (jQuery("select[name=asset_category_id]").length == 1 && jQuery("select[name=asset_category_id]").val() > 0) {
        //  var category_id = jQuery("select[name=asset_category_id]").val();
        //}
      }
    }
    else {
      jQuery('#widget_upload #widget_asset_upload_error').html(data.msg).show();
    }
  }
}

/*
function widgetUploadDone()
{
  var ret = frames['widget_upload_target'].document.getElementsByTagName("body")[0].innerHTML;
  //alert(ret);
  //console.log(ret);
  var data = eval("("+ret+")"); //Parse JSON
  //console.log(data);
  if(data.status == "success") {
    jQuery('#widget_upload input[name=asset_category_id]').val(data.asset_category_id);
    jQuery('#widget_upload #asset_name').val('');
    jQuery('#widget_upload #asset_description').val()
    jQuery('#widget_upload #asset_file').val()
    jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + jQuery("input[name='editing_section']").val());
  } else {
    jQuery('#widget_upload #widget_asset_upload_error').html(data.msg).show();
  }
}
*/
</script>