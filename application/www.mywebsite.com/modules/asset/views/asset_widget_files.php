<form method="post" id="formFiles" name="formFiles" class="configform" onsubmit="return update_files(this);">
  <table class="editinline">
    <tr>
      <th>File</th>
      <th>Name<span class="red">*</span><br /><div id="widget_asset_name_error" class="error"></div></th>
      <th>Description</th>
      <th>Date/Time</th>
      <th>Option</th>
      <th></th>
    </tr>
    <?php
    if (is_array($files) && count($files) > 0) {
      foreach($files as $file) {
        echo '<tr id="assetitem_' . $file->id . '" class="file_row">';
        echo '<td><span class="asset_thumb">' . $file->full_html . '</span></td>';
        //printf('<td><span class="asset_thumb"><a href="http://%s%s" title="%s" target="_blank">%s.%s</a></span></td>',
        //  $this->session->userdata( 'siteURL' ), $file->full_path, $file->name, $file->file_name, $file->extension);
        echo '<td class="title_td">' . form_input('name[' . $file->id . ']', $file->name) . '</td>';
        //echo '<td>' . form_input('description[' . $file->id . ']', $file->description) . '</td>';
        echo '<td>' . form_input( array(
              'name'        => 'description[' . $file->id . ']',
              'id'          => 'description_' . $file->id,
              'value'       => $file->description,
              'maxlength'   => '500',
              'size'        => '45',
            )) . '</td>';
        echo '<td>' . $file->create_date . '</td>';
        echo '<td align="center">' . form_hidden('id[' . $file->id . ']', $file->id);
        //echo form_checkbox('delete[' . $file->id . ']', $file->id, FALSE);
        echo '<input type="button" name="delete[' . $file->id . ']" value="Delete" class="red_button_smaller btn_delete" style="padding: 1px 6px"/>';
        echo '</td>';
        echo '<td class="medium_thumb" style="display:none">' . $file->full_html . '</td>';
        //printf('<a href="http://%s%s" title="%s" target="_blank">%s.%s</a>',
        //  $this->session->userdata( 'siteURL' ), $file->full_path, $file->name, $file->file_name, $file->extension);
        echo "</tr>\n";
      }
    ?>
    <tr>
      <td colspan="3">
        <input type="submit" name="Submit" value="Save" class="red_button_smaller" style="padding: 3px 6px" />
        <!-- input type="button" name="Cancel" value="Cancel"  style="padding: 3px 6px" class="cancel_dialog red_button_smaller" / -->
      </td>
      <td colspan="3" align="right"></td>
    </tr>
    <?php
    }
    else {
      echo '<tr>';
      echo '<td colspan="6">No files were found.</td>';
      echo "</tr>\n";
    }
    ?>
  </table>
  <?php echo form_hidden('asset_category_id', $asset_category_id); ?>
</form>

<script type="text/javascript">
function validate_files_form (obj) {
  return true;
}

function update_files(obj) {
  if (!validate_files_form(obj)) {
    return false;
  }
  var section_id = jQuery("input[name='editing_section']").val();
  var asset_category_id = jQuery("input[name='asset_category_id']", obj).val();
  var dataString = jQuery(obj).serialize();
  jQuery.ajax({
    type: "POST",
    url: "/hotcms/media-library/widgetupdate/" + asset_category_id,
    data: dataString,
    success: function() {
      var page_id = jQuery("input[name='page_id']").val();
      jQuery("input[name='section_updated']").val("1");
      jQuery("#widget-config").load("/hotcms/page/ajax_config_section/" + page_id + "/" + section_id);
      alert('Files updated.');
    }
  });
  return false;
}

jQuery(document).ready(function() {
  jQuery("input.cancel_dialog").click(function() {
    if (jQuery(".dialog-config").length > 0 && jQuery(".dialog-config").dialog( "isOpen" )) {
      jQuery(".dialog-config").dialog("close");
    }
  });
  jQuery(".editinline input").change(function(){
    jQuery(this).addClass("edited");
    jQuery(this).parents(".editinline").find("input[type=submit]").addClass("edited");
  });
  jQuery(".btn_delete").live("click",function(){
    if (confirm("Are you sure you want to delete this file?")) {
      var img_id = jQuery(this).attr("name").substring(7, jQuery(this).attr("name").length-1);
      jQuery.get("/hotcms/media-library/ajax_delete/" + img_id, null, function(data){
        try{
          var JSONobj = JSON.parse(data);
          if (JSONobj['status'] == 'success') {
            if (jQuery("tr#assetitem_" + img_id).length > 0) {
              jQuery("tr#assetitem_" + img_id).remove();
            }
            if (jQuery("select[name=asset_id]").length > 0) {
              jQuery("select[name=asset_id] option[value='"+img_id+"']").remove();
            }
          }
          else if (JSONobj['messages'] > '') {
            alert(JSONobj['messages']);
          }
        }
        catch(e){
          alert("Error: "+e.description);
        }
      });
    }
  });
  jQuery(".asset_thumb img").live("click",function(){
    if (jQuery("input[name=asset_id]").length > 0) {
      var asset_id = jQuery(this).parents("tr.file_row").attr("id").substring(10);
      jQuery("input[name=asset_id]").val(asset_id);
    }
    if (jQuery("#preview_file").length > 0) {
      jQuery("#preview_file").html(jQuery(this).parents("tr.file_row").children(".medium_thumb").html());
    }
    if (jQuery("#file_title").length > 0) {
      jQuery("#file_title").html(jQuery(this).parents("tr.file_row").children("td.title_td").children("input").val());
    }
  });
  jQuery(".asset_thumb a").live("click",function(){
    if (jQuery("input[name=asset_id]").length > 0) {
      var asset_id = jQuery(this).parents("tr.file_row").attr("id").substring(10);
      jQuery("input[name=asset_id]").val(asset_id);
    }
    if (jQuery("#file_preview").length > 0) {
      jQuery("#file_preview").html(jQuery(this).parents("tr.file_row").children(".medium_thumb").html());
    }
    if (jQuery("#file_title").length > 0) {
      jQuery("#file_title").html(jQuery(this).parents("tr.file_row").children("td.title_td").children("input").val());
    }
    return false;
  });
});
</script>