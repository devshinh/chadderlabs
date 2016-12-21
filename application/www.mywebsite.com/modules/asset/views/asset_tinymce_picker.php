<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#hotimg_dlg.title}</title>
	<script type="text/javascript" src="/hotcms/asset/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="/hotcms/asset/js/tinymce/plugins/hotimg/js/dialog.js"></script>
	<script type="text/javascript" src="/hotcms/asset/js/jquery-1.6.2.min.js"></script>
  <script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery(".asset_thumb img").css("border", "3px solid #f0f0ee");
    jQuery("select[name=asset_category_id]").change(function(){
      var cid = jQuery(this).val();
      if (cid > 0) {
        jQuery("#divUploader").show();
      }
      else {
        jQuery("#divUploader").hide();
      }
      jQuery("#formUpload").attr("action", "/hotcms/media-library/ajax_upload/" + cid);
      jQuery("#tinymce_image_selector").load("/hotcms/media-library/tinymce_image_list/" + cid + "/" + Math.random()*99999);
    });
    jQuery(".asset_thumb img").live("click", function(){
      var iid = jQuery(this).parents("tr").attr("id").substring(10);
      jQuery("#formHotimg").find("input[name=imageid]").val(iid);
      var full_src = jQuery(this).parents("tr.media_library_item").find("td.fullsrc").html();
      jQuery("#formHotimg").find("input[name=imagesrc]").val(full_src);
      jQuery(".asset_thumb img").css("border", "3px solid #f0f0ee");
      jQuery(this).css("border", "3px solid #2AFF31");
    });
    document.getElementById("upload_target").onload = uploadDone;
  });
  function uploadDone(){
    var ret = frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML;
    var data = eval("("+ret+")"); //Parse JSON
    //console.log(data);
    if(data.status == "success") {
      //tinyMCE.execCommand('mceInsertContent',false,'<img src="'+ data.msg +'"/>');
      jQuery('#formUpload #asset_name').val('');
      jQuery('#formUpload #asset_description').val('');
      jQuery('#formUpload #asset_file').val('');
      jQuery('#formUpload .error').css('display','none');
      // reload image list
      var cid = jQuery("select[name=asset_category_id]").val();
      jQuery("#tinymce_image_selector").load("/hotcms/media-library/tinymce_image_list/" + cid + "/" + Math.random()*99999);
    } else {
      jQuery('#formUpload #asset_upload_error').html(data.msg).show();
    }
  }
  </script>
</head>
<body>

  <label for="asset_category_id">Select Image Group:</label>
  <?php echo form_dropdown("asset_category_id", $categories, $category_id);?>
  <form method="post" id="formHotimg" name="formHotimg" class="configform" onsubmit="HotimgDialog.insert();return false;" action="#">
    <input type="hidden" name="imageid" value="0" />
    <input type="hidden" name="imagesrc" value="" />
    <div id="tinymce_image_selector" class="table">
    <?php echo $image_list; ?>
    </div>
    <div class="mceActionPanel">
      <input type="button" id="insert" name="insert" value="Insert" class="red_button_smaller" style="padding: 3px 6px" onclick="HotimgDialog.insert();" />
      <input type="button" id="cancel" name="cancel" value="Cancel" style="padding: 3px 6px" class="cancel_dialog red_button_smaller" onclick="tinyMCEPopup.close();" />
    </div>
  </form>
  <div class="clear" style="clear:both;height:30px;"></div>
  <div id="divUploader" style="display:none">
    <h3>Upload Image</h3>
    <form id="formUpload" target="upload_target" action="/hotcms/media-library/ajax_upload" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
      <div id="asset_upload_error" class="error"></div>
      <div class="row">
        <div class="error"></div>
        <?php echo form_label(lang( 'hotcms_file' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'asset_file');?>
        <?php echo form_upload($asset_file_input); ?>
      </div>
      <div class="row">
        <div id="asset_name_error" class="error"></div>
        <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'name');?>
        <?php echo form_input($asset_name_input); ?>
      </div>
      <div class="row">
        <div id="asset_description_error" class="error"></div>
        <?php echo form_label(lang( 'hotcms_description' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'description');?>
        <?php echo form_textarea($asset_description_input); ?>
      </div>
      <div class="submit">
        <input type="submit" class="red_button_smaller" value="<?php echo lang( 'hotcms_upload' ) ?>" />
        <?php echo form_hidden('hdnMode', 'insert'); ?>
      </div>
      <iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #000;"></iframe>
    </form>
  </div>

</body>
</html>