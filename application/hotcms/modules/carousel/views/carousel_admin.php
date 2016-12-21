<?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
<?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

<form method="post" id="formCarousel" name="formCarousel" class="configform" onsubmit="return config_widget(this);">
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
        <td><label for="group_id">Select Image Group:</label></td>
        <td><?php echo form_dropdown("group_id", $groups, $group_id);?></td>
      </tr>
      <tr>
        <td><input type="submit" name="Submit" value="Save" class="button" /></td>
        <td align="right"><input type="button" name="Cancel" value="Cancel" class="cancel_dialog button" /></td>
      </tr>
    </table>
  </fieldset>
  <fieldset>
    <legend>Group Images</legend>
    <table class="editinline">
      <tr>
        <th>Order</th>
        <th>Image</th>
        <th>Title</th>
        <th>Link Image To</th>
        <th>Delete</th>
        <th></th>
      </tr>
      <?php
      if (is_array($items) && count($items) > 0) {
        foreach($items as $item) {
          echo '<tr id="' . $item->id . '">';
          echo '<td>' . form_input( array(
                'name'        => 'sequence[' . $item->id . ']',
                'id'          => 'sequence_' . $item->id,
                'value'       => $item->sequence,
                'maxlength'   => '10',
                'size'        => '2',
              )) . '</td>';
          if (empty($item->image)) {
            echo '<td><span class="asset_thumb"></span></td>';
          }
          else {
            printf('<td><span class="asset_thumb"><img src="http://%s%s/%s.%s" alt="%s" /></span></td>', $this->session->userdata( 'siteURL' ), $item->image->thumbnail,$item->image->file_name.'_thumb', $item->image->extension, $item->image->description);
          }
          echo '<td>' . form_input('link_title[' . $item->id . ']', $item->link_title) . '</td>';
          echo '<td>http://' . $_SERVER['HTTP_HOST'] . '/ ' . form_input('link[' . $item->id . ']', $item->link) . '</td>';
          //echo '<td>' . form_input('sequence[' . $item->id . ']', $item->sequence) . '</td>';
          echo '<td align="center">' . form_hidden('id[' . $item->id . ']', $item->id) . form_hidden('asset_id[' . $item->id . ']', $item->asset_id);
          echo form_checkbox('delete[' . $item->id . ']', $item->id, FALSE) . '</td>';
          echo '<td></td>';
          echo "</tr>\n";
        }
      }
      else {
        echo '<tr>';
        echo '<td colspan="6">No images were found.</td>';
        echo "</tr>\n";
      }
      ?>
      <!-- tr id="0">
        <td><span class="asset_thumb" id="new_thumb"></span></td>
        <td><?php //echo form_input('link[0]', ''); ?></td>
        <td><?php //echo form_input('link_title[0]', ''); ?></td>
        <td>
          <?php //echo form_hidden('id[0]', '0'); ?>
          <?php //echo form_hidden('asset_id[0]', '0'); ?>
          <?php //echo form_checkbox('delete[0]', '0', FALSE); ?>
          <?php //echo form_hidden('new_asset_id', '0'); ?>
        </td>
        <td><a href="#">add</a></td>
      </tr -->
      <tr>
        <td colspan="3"><input type="submit" name="Submit" value="Save" class="button" /></td>
        <td colspan="3" align="right"><input type="button" name="Cancel" value="Cancel" class="cancel_dialog button" /></td>
      </tr>
    </table>
  </fieldset>
</form>
<script type="text/javascript">
/*
function widgetPostUpload (asset_id) {
  jQuery("input[name='new_asset_id']").val(asset_id);
  //jQuery('#formCarousel').submit();
  config_widget(document.formCarousel);
  jQuery("input[name='new_asset_id']").val('0');
  jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + jQuery("input[name='editing_section']").val());
}
*/

jQuery( document ).ready( function() {
  jQuery("input.cancel_dialog").click(function() {
    if (jQuery(".dialog-config").dialog( "isOpen" )) {
      jQuery(".dialog-config").dialog("close");
    }
  });
  jQuery(".editinline input").change(function(){
    jQuery(this).addClass("edited");
    jQuery(this).parents(".editinline").find("input[type=submit]").addClass("edited");
  });
});
</script>

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
