<table class="editinline">
  <tr>
    <th>Image</th>
    <th>Name</div></th>
    <th>Description</th>
    <th>Date/Time</th>
    <th style="display:none;"></th>
  </tr>
  <?php
  if (is_array($images) && count($images) > 0) {
    foreach($images as $image) {
      echo '<tr id="assetitem_' . $image->id . '" class="media_library_item">';
      echo form_hidden('id[' . $image->id . ']', $image->id);
      //printf('<img src="http://%s%s/%s.%s" alt="%s" /></span></td>', $this->session->userdata( 'siteURL' ), $image->thumbnail, $image->file_name.'_thumb', $image->extension, $image->description);
      echo '<td class="imageids"><span class="asset_thumb">' . $image->thumb_html . '</span></td>';
      echo '<td>' . $image->name . '</td>';
      echo '<td>' . $image->description . '</td>';
//        echo '<td>' . form_input('name[' . $image->id . ']', $image->name) . '</td>';
//        //echo '<td>' . form_input('description[' . $image->id . ']', $image->description) . '</td>';
//        echo '<td>' . form_input( array(
//              'name'        => 'description[' . $image->id . ']',
//              'id'          => 'description_' . $image->id,
//              'value'       => $image->description,
//              'maxlength'   => '500',
//              'size'        => '45',
//            )) . '</td>';
      echo '<td class="last">' . $image->create_date . '</td>';
//        echo '<td align="center">' . form_checkbox('delete[' . $image->id . ']', $image->id, FALSE) . '</td>';
      echo '<td class="fullsrc" style="display:none;">';
      //printf('<img src="http://%s%s" alt="%s" />', $this->session->userdata( 'siteURL' ), $image->full_path, $image->description);
      printf('<img src="%s" alt="%s" />', $image->full_path, $image->description);
      echo '</td>';
      echo "</tr>\n";
    }
  }
  else {
    echo '<tr>';
    echo '<td colspan="5" class="last">No images were found.</td>';
    echo "</tr>\n";
  }
  ?>
</table>
