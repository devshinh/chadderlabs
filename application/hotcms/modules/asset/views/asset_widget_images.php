<form method="post" id="formImages" name="formImages" class="configform" onsubmit="return update_images(this);">
  <table class="editinline">
    <tr>
      <th>Image</th>
      <th>Name<span class="red">*</span><br /><div id="widget_asset_name_error" class="error"></div></th>
      <th>Description</th>
      <th>Date/Time</th>
      <th>Option</th>
      <th></th>
    </tr>
    <?php
    if (is_array($images) && count($images) > 0) {
      foreach($images as $image) {
        echo '<tr id="assetitem_' . $image->id . '" class="image_row">';
        echo '<td><span class="asset_thumb">' . $image->thumb_html . '</span></td>';
        //printf('<td><span class="asset_thumb"><img src="http://%s%s/%s.%s" alt="%s" /></span></td>',
        //  $this->session->userdata( 'siteURL' ), $image->thumbnail, $image->file_name.'_thumb', $image->extension, $image->description);
        echo '<td class="title_td">' . form_input('name[' . $image->id . ']', $image->name) . '</td>';
        //echo '<td>' . form_input('description[' . $image->id . ']', $image->description) . '</td>';
        echo '<td>' . form_input( array(
              'name'        => 'description[' . $image->id . ']',
              'id'          => 'description_' . $image->id,
              'value'       => $image->description,
              'maxlength'   => '500',
              'size'        => '45',
            )) . '</td>';
        echo '<td>' . $image->create_date . '</td>';
        echo '<td align="center">' . form_hidden('id[' . $image->id . ']', $image->id);
        //echo form_checkbox('delete[' . $image->id . ']', $image->id, FALSE);
        echo '<input type="button" name="delete[' . $image->id . ']" value="Delete" class="red_button_smaller btn_delete" style="padding: 1px 6px"/>';
        echo '</td>';
        echo '<td class="medium_thumb" style="display:none">';
        echo $image->thumb_html;
        //printf('<img src="http://%s%s/%s.%s" alt="%s" /></span>',
        //  $this->session->userdata( 'siteURL' ), $image->thumb, $image->file_name.'_thumb', $image->extension, $image->description);
        echo '</td>';
        echo "</tr>\n";
      }
    ?>
    <tr>
      <td colspan="3"><input type="submit" name="Submit" value="Save" class="red_button_smaller" style="padding: 3px 6px" />
        <!-- input type="button" name="Cancel" value="Cancel"  style="padding: 3px 6px" class="cancel_dialog red_button_smaller" / --></td>
      <td colspan="3" align="right"></td>
    </tr>
    <?php
    }
    else {
      echo '<tr>';
      echo '<td colspan="6">No images were found.</td>';
      echo "</tr>\n";
    }
    ?>
  </table>
  <?php echo form_hidden('asset_category_id', $asset_category_id); ?>
</form>

<script type="text/javascript">
function validate_images_form (obj) {
  return true;
}

function update_images(obj) {
  if (!validate_images_form(obj)) {
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
      alert('Images updated.');
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
  jQuery("body").delegate(".btn_delete", "click",function(){
    if (confirm("Are you sure you want to delete this image?")) {
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
  jQuery("body").delegate(".asset_selector", "click", function(){
    var asset_id = jQuery(this).parents("tr.image_row").attr("id").substring(10);
    var asset_preview = jQuery(this).parents("tr.image_row").children(".medium_thumb").html();
    var asset_title = jQuery(this).parents("tr.image_row").children("td.title_td").children("input").val();
    jQuery.cookie("asset_id", asset_id);
    jQuery.cookie("asset_preview", asset_preview);
    jQuery.cookie("asset_title", asset_title);
    if (jQuery("input[name=asset_id]").length > 0) {
      jQuery("input[name=asset_id]").val(asset_id);
    }
    if (jQuery(".preview_area").length > 0) {
      jQuery(".preview_area").html(asset_preview);
    }
    if (jQuery(".preview_title").length > 0) {
      jQuery(".preview_title").html(asset_title);
    }
  });
});
</script>