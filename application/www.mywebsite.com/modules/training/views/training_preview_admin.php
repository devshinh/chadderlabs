<?php if ($message > '') { ?><div class="message"><?php echo $message; ?></div><?php } ?>
<?php if ($error > '') { ?><div class="error"><?php echo $error; ?></div><?php } ?>

<?php if ($has_permission) { ?>
  <form method="post" id="formTrainingItem" name="formTrainingItemPreview" class="configform" onsubmit="return config_widget(this);">
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
          <td><?php echo form_radio($preview_type_featured); ?><label for="preview_type_featured">Featured game</label></td>
        </tr>      

        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_new); ?><label for="preview_type_new">New games</label></td>
        </tr>  
        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_coming_soon); ?><label for="preview_type_coming_soon">Coming soon</label></td>
        </tr>          
        <tr>
          <td></td>
          <td><?php echo form_radio($preview_type_overview_carousel); ?><label for="preview_type_overview_carousel">Overview carousel</label></td>
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
