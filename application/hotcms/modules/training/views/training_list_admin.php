<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formTrainingList" name="formTrainingList" class="configform" onsubmit="return config_widget(this);">
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
          <td><label for="title">Type of list:<span class="red">*</span></label></td>
          <td><?php echo form_radio($all_labs); ?><label for="all_labs">All labs</label></td>
        </tr>      

        <tr>
          <td></td>
          <td><?php echo form_radio($uncomplete_labs); ?><label for="uncomplete_labs">Uncomplete Labs</label></td>
        </tr>  
        <tr>
          <td></td>
          <td><?php echo form_radio($complete_lab); ?><label for="complete_lab">Complete Labs</label></td>
        </tr>          
                
      
      <tr>
        <td><input type="submit" name="Submit" value="Save" class="button" /></td>
        <td align="right"><input type="button" name="Cancel" value="Cancel" class="cancel_dialog button" /></td>
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
