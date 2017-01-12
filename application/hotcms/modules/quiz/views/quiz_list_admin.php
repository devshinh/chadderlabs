<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formQuizList" name="formQuizList" class="configform" onsubmit="return config_widget(this);">
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
        <td><label for="title">Quiz types:</label></td>
        <td>
          <table>
          <?php
          foreach ($form['quiz_types'] as $k => $v) {
            echo '<tr id="trData_' . $k . '"><td>';
            echo form_checkbox($v);
            echo form_label($v['id'], $v['id']);
            echo form_error('quiz_types[' . $k . ']', '<div class="error">', '</div>');
            echo '</td></tr>';
          }
          ?>     
          </table>
        </td>
      </tr>      
      <tr>
        <td><input type="submit" name="Submit" value="Save" class="red_button_smaller" /></td>
        <td align="right"><input type="button" name="Cancel" value="Cancel" class="red_button_smaller" /></td>
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
