<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formImage" name="formImage" class="configform">
  <input name="asset_id" type="hidden" value="<?php echo $asset_id; ?>" />
  <input name="asset_title" type="hidden" value="" />
  <fieldset>
    <legend>Selected Image</legend>
    <table width="700">
      <tr>
        <td><label for="title">Image<span class="red">*</span>:</label></td>
        <td class="preview_area">
          <?php
            if (!empty($image)) {
              printf('<img src="http://%s%s/%s.%s" alt="%s" />',
                $this->session->userdata( 'siteURL' ), $image->thumb, $image->file_name.'_thumb', $image->extension, $image->description);
            }
          ?>
        </td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td class="preview_title">
          <?php
            if (!empty($image)) {
              echo $image->name;
            }
          ?>
        </td>
        <td></td>
      </tr>
      <!-- tr>
        <td><input type="submit" name="Submit" value="Save" class="red_button_smaller" /><input type="button" name="Cancel" value="Cancel" class="cancel_dialog red_button_smaller" /></td>
        <td align="right"></td>
        <td width="100"></td>
      </tr -->
    </table>
    <table>
      <tr><td></td></tr>
    </table>
  </fieldset>
</form>
<script type="text/javascript">
jQuery( document ).ready( function() {
  jQuery("input.cancel_dialog").click(function() {
    if (jQuery(".dialog-config").dialog( "isOpen" )) {
      jQuery(".dialog-config").dialog("close");
    }
  });

  jQuery("body").undelegate("select[name=asset_category_id]", "change")
  .delegate("select[name=asset_category_id]", "change", function(){
    var category_id = jQuery(this).val();
    if (category_id > 0) {
      jQuery.getJSON("/hotcms/media-library/ajax_assets/" + category_id + "/1", function(json) {
        if (json.formatted > '') {
          jQuery(".asset_images").html("<fieldset><legend>Group Images</legend>" + json.formatted + "</fieldset>");
        }
        if (json.messages > '') {
          alert(json.messages);
        }
      }).error(function(){ alert("Sorry but there was an error."); });
      jQuery.get("/hotcms/media-library/ajax_image_upload/" + category_id, null, function(data){
        jQuery(".asset_image_upload").html("<fieldset><legend>Upload Image to Group</legend>" + data + "</fieldset>");
      });
    }
  });
});
</script>
<table>
  <tr>
    <td width="25"></td>
    <td><label for="asset_category_id">Select Image Group:</label></td>
    <td width="10"></td>
    <td><?php echo form_dropdown("asset_category_id", $asset_categories, $asset_category_id);?></td>
    <td></td>
  </tr>
</table>
<div class="asset_images">
<?php
if (!empty($media_library_ui)) {
?>
  <fieldset>
    <legend>Group Images</legend>
    <?php echo $media_library_ui; ?>
  </fieldset>
<?php
}
?>
</div>
<div class="asset_image_upload">
<?php
if (!empty($media_upload_ui)) {
?>
  <fieldset>
    <legend>Upload Image to Group</legend>
    <?php echo $media_upload_ui; ?>
  </fieldset>
<?php
}
?>
</div>