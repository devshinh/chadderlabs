<?php if ($message > '') { ?><div class="message"><?php echo $message; ?></div><?php } ?>
<?php if ($error > '') { ?><div class="error"><?php echo $error; ?></div><?php } ?>

<?php if ($has_permission) { ?>
  <form method="post" id="formNewsPreview" name="formNewsPreview" class="configform" onsubmit="return config_widget(this);">
    <fieldset>
      <legend>Settings</legend>
      <table>
        <tr>
          <td colspan="2"><span class="red">*</span> indicates mandatory fields</td>
        </tr>
        <tr>
          <td><label for="title">Content Block Title:</label></td>
          <td><?php echo form_input($title); ?></td>
        </tr>
        <tr>
          <td><label for="title">Type of preview:<span class="red">*</span></label></td>
          <td><?php echo form_radio($preview_type_homepage); ?><label for="preview_type_homepage">Homepage</label></td>
        </tr>      

        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_training_item); ?><label for="preview_type_training_item">Training item page</label></td>
        </tr>  
        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_archive); ?><label for="preview_type_archive">Archive</label></td>
        </tr>       
        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_latest); ?><label for="preview_type_latest">Latest</label></td>
        </tr>         
        <tr>
          <td><input type="submit" name="Submit" value="Save" class="red_button" /></td>
          <td align="right"><input type="button" name="Cancel" value="Cancel" class="cancel_dialog red_button" /></td>
        </tr>
      </table>
    </fieldset>
  </form>
<?php } ?>
<script type="text/javascript">
  jQuery( document ).ready( function() {
    jQuery("input.cancel_dialog").click(function() {
      if (jQuery(".dialog-config").dialog( "isOpen" )) {
        jQuery(".dialog-config").dialog("close");
      }
    });
  });
</script>
