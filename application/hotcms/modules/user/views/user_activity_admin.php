<?php if ($message > '') { ?><div class="message"><?php echo $message; ?></div><?php } ?>
<?php if ($error > '') { ?><div class="error"><?php echo $error; ?></div><?php } ?>

<?php if ($has_permission) { ?>
  <form method="post" id="formUserActivity" name="formUserActivity" class="configform" onsubmit="return config_widget(this);">
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
          <td><label for="limit">Number of Items to Show:</label></td>
          <td><?php echo form_input($limit); ?></td>
        </tr>
        <tr>
          <td><label for="limit">Site restricted</label></td>
          <td><?php echo form_checkbox($site_restricted); ?></td>
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
