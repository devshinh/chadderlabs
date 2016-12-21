<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formRandomizer" name="formRandomizer" class="configform" onsubmit="return config_widget(this);">
  <fieldset>
    <legend>Settings</legend>
    <table>
      <!-- tr>
        <td colspan="2"><span class="red">*</span> indicates mandatory fields</td>
      </tr -->
      <tr>
        <td><label for="title">Content Block Title:</label></td>
        <td><?php echo form_input($title);?></td>
      </tr>
      <tr>
        <td><label for="asset_category_id">Select Image Group:</label></td>
        <td><?php echo form_dropdown("asset_category_id", $categories, $asset_category_id);?></td>
      </tr>
      <tr>
        <td>
          <input type="submit" name="Submit" value="Save" class="red_button_smaller" />
          <input type="button" name="Cancel" value="Cancel" class="cancel_dialog red_button_smaller" />
        </td>
        <td align="right"></td>
      </tr>
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