<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formImage" name="formImage" class="configform" onsubmit="return config_widget(this);">
  <fieldset>
    <legend>Settings</legend>
    <table width="700">
      <!-- tr>
        <td colspan="4"><span class="red">*</span> indicates mandatory fields</td>
      </tr -->
      <tr>
        <td><label for="title">Content Block Title:</label></td>
        <td><?php echo form_input($title);?></td>
        <td></td>
      </tr>
      <tr>
        <td><label for="asset_category_id">Select Image Group:</label></td>
        <td><?php echo form_dropdown("asset_category_id", $categories, $asset_category_id);?></td>
        <td></td>
      </tr>
      <tr>
        <td><label for="title">Image<span class="red">*</span>:</label></td>
        <td><?php echo form_dropdown("asset_id", $asset_options, $asset_id);?></td>
        <td></td>
      </tr>
      <tr>
        <td><label for="link">Link:</label></td>
        <td><?php echo form_input($link);?></td>
        <td></td>
      </tr>
      <tr>
        <td><label for="link">Link Title:</label></td>
        <td><?php echo form_input($link_title);?></td>
        <td></td>
      </tr>
      <tr>
        <td><input type="submit" name="Submit" value="Save" class="red_button_smaller" /><input type="button" name="Cancel" value="Cancel" class="cancel_dialog red_button_smaller" /></td>
        <td align="right"></td>
        <td width="100"></td>
      </tr>
    </table>
    <table>
      <tr><td><?php
        if (!empty($image)) {
          echo '<span>Image Preview:</span><br />';
          printf('<span class="asset_medium"><img src="http://%s%s/%s.%s" alt="%s" /></span>', $this->session->userdata( 'siteURL' ), $image->thumb,$image->file_name.'_thumb', $image->extension, $image->description);
        }
      ?></td></tr>
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
});
</script>

<?php
if (!empty($media_library_ui)) {
?>
  <fieldset>
    <legend>Group Images</legend>
    <?php echo $media_library_ui; ?>
  </fieldset>
<?php
}
if (!empty($media_upload_ui)) {
?>
  <fieldset>
    <legend>Upload Image to Group</legend>
    <?php echo $media_upload_ui; ?>
  </fieldset>
<?php
}
?>
