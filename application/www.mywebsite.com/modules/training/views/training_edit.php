<div class="tabs">
  <ul>
    <li><a href="#training-detail" id="detail-tab"><span id="detail_icon"></span><span>Detail</span></a></li>
    <li><a href="#training-history" id="history-tab"><span id="history_icon"></span><span>History</span></a></li>
  </ul>
  <div id="training-detail">
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $item->id ?>" method="post" id="training-form">
    <?php
      echo form_hidden($form['hidden_fields']);
      //echo form_hidden('hdnMode', 'edit');
    ?>
    <div id="general">
      <div class="row">
        <?php echo form_error('category_id', '<div class="error">', '</div>');?>
        <?php echo form_label(lang( 'hotcms_category' ) . lang( 'hotcms__colon' ), 'category_id');?>
        <?php echo form_dropdown("category_id", $categories, $item->category_id); ?>
      </div>
      <div class="row">
        <?php echo form_error('title', '<div class="error">', '</div>');?>
        <?php echo form_label(lang( 'hotcms_title' ) . lang( 'hotcms__colon' ), 'title');?>
        <?php echo form_input($form['title']); ?>
      </div>
      <div class="row">
        <?php echo form_error('status', '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_status' ) . lang( 'hotcms__colon' ), 'status');?>
        <?php echo form_dropdown("status", $status_array, $item->status); ?>
      </div>
      <div class="row">
        <?php echo form_error('title', '<div class="error">', '</div>');?>
        <?php echo form_label(lang( 'training_link' ) . lang( 'hotcms__colon' ), 'link');?>
        <?php echo form_input($form['link']); ?>
      </div>      
      <div class="row">
        <?php echo form_error('featured', '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_featured' ) . lang( 'hotcms__colon' ), 'featured');?>
        <?php echo form_checkbox($form['featured']); ?>
      </div>
    </div>
    <div id="featured_image_div">
      <h3>Featured Image</h3>
      <div id="featured_image">
        <?php
        if (!empty($item->featured_image)) {
          printf('<img src="http://%s%s/%s.%s" alt="%s" />',
            $this->session->userdata( 'siteURL' ), $item->featured_image->thumb, $item->featured_image->file_name.'_thumb',
            $item->featured_image->extension, $item->featured_image->description);
        }
        ?>
      </div>
      <a href="<?php echo $item->featured_image_id; ?>" class="red_button featured_image_link">Choose</a>
    </div>
    <?php
    if (count($tag_types) > 0) {
    ?>
    <div id="tags">
      <?php
      foreach ($tag_types as $tag_type) {
        echo form_label($tag_type->type_name . lang( 'hotcms__colon' ), '', array('style' => 'font-weight:bold'));
        echo '<div class="tag_type">';
        foreach ($tags as $tag) {
          if ($tag->type_id != $tag_type->id) {
            continue;
          }
          echo form_checkbox(array(
            'name'    => 'tags[]',
            'id'      => 'tag_' . $tag->id,
            'value'   => $tag->id,
            'checked' => array_key_exists($tag->id, $item->tags),
          ));
          echo ' ';
          echo form_label($tag->name, 'tag_' . $tag->id);
          echo ' &nbsp; ';
        }
        echo '</div>';
        echo '<div class="clear"></div>';
      }
      ?>
    </div>
    <div class="clear"></div>
    <?php
    }
    ?>
    <div id="assets" class="tabs">
      <ul>
        <li><a href="#assets-image" id="image-tab"><span id="image_icon"></span><span>Image</span></a></li>
        <li><a href="#assets-video" id="video-tab"><span id="video_icon"></span><span>Video</span></a></li>
        <li><a href="#assets-audio" id="audio-tab"><span id="audio_icon"></span><span>Audio</span></a></li>
      </ul>
      <div id="assets-image">
        <table id="image_table" class="framed">
          <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        <?php
        foreach ($item->assets as $v) {
          if ($v->type == 1) { // image
            echo '<tr class="asset_row">';
            printf('<td><img src="http://%s%s/%s.%s" alt="%s" /></td>',
              $this->session->userdata( 'siteURL' ), $v->thumb, $v->file_name.'_thumb', $v->extension, $v->description);
            echo '<td>' . $v->name . '</td>';
            echo '<td><a href="' . $v->id . '" class="edit_asset_link">edit</a></td>';
            echo '<td><a href="' . $v->id . '" class="delete_asset_link">delete</a></td>';
            echo '</tr>';
          }
        }
        ?>
        </table>
        <a href="<?php echo $item->id; ?>" class="red_button add_asset_image_link">Add Image</a>
      </div>
      <div id="assets-video">
        <table id="video_table" class="framed">
          <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        <?php
        foreach ($item->assets as $v) {
          if ($v->type == 3) { // video
            echo '<tr class="asset_row">';
            echo '<td>' . $v->thumbnail . '</td>';
            echo '<td>' . $v->name . '</td>';
            echo '<td>edit</td>';
            echo '<td>delete</td>';
            echo '</tr>';
          }
        }
        ?>
        </table>
        <a href="<?php echo $item->id; ?>" class="red_button add_asset_video_link">Add Video</a>
      </div>
      <div id="assets-audio">
        <table id="audio_table" class="framed">
          <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        <?php
        foreach ($item->assets as $v) {
          if ($v->type == 4) { // audio
            echo '<tr class="asset_row">';
            echo '<td>' . $v->thumbnail . '</td>';
            echo '<td>' . $v->name . '</td>';
            echo '<td>edit</td>';
            echo '<td>delete</td>';
            echo '</tr>';
          }
        }
        ?>
        </table>
        <a href="<?php echo $item->id; ?>" class="red_button add_asset_audio_link">Add Audio</a>
      </div>
    </div>
    <div id="description-div">
      <h3>Description</h3>
      <div class="divTinyMCE">
        <?php echo form_textarea($form['description']); ?>
      </div>
    </div>
    <div id="features-div">
      <h3>Features</h3>
      <div class="divTinyMCE">
        <?php echo form_textarea($form['features']); ?>
      </div>
    </div>
    <div id="variants">
      <h3>Variants</h3>
      <table id="variant_table" class="framed">
      <?php
      echo '<tr>';
      foreach ($variant_fields as $vf) {
        echo '<th>';
        echo $vf->label;
        echo '</th>';
      }
      echo '<th>Edit</th>';
      echo '<th>Delete</th>';
      echo '</tr>';
      foreach ($item->variants as $v) {
        echo '<tr class="variant_row">';
        foreach ($variant_fields as $vf) {
          echo '<td>';
          foreach ($v->details as $vd) {
            if ($vd->field_id == $vf->id) {
              echo $vd->value;
              continue;
            }
          }
          echo '</td>';
        }
        echo '<td><a href="' . $v->id . '" class="edit_variant_link">edit</a></td>';
        echo '<td><a href="' . $v->id . '" class="delete_variant_link">delete</a></td>';
        echo '</tr>';
      }
      ?>
      </table>
      <a href="<?php echo $item->id; ?>" class="red_button add_variant_link">Add Variant</a>
    </div>
    <div id="resources">
      <h3>Resources</h3>
      <table id="resource_table" class="framed">
        <tr>
          <th>Type</th>
          <th>Title</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      <?php
      foreach ($item->resources as $v) {
        echo '<tr class="resource_row">';
        echo '<td>' . $v->type . '</td>';
        echo '<td>' . $v->title . '</td>';
        echo '<td><a href="' . $v->id . '" class="edit_resource_link">edit</a></td>';
        echo '<td><a href="' . $v->id . '" class="delete_resource_link">delete</a></td>';
        echo '</tr>';
      }
      ?>
      </table>
      <a href="<?php echo $item->id; ?>" class="red_button add_resource_link">Add Resource</a>
    </div>
    <div class="submit">
      <a href="/hotcms/<?php echo $module_url?>" class="red_button" onclick="return confirm_discard();"><?php echo lang( 'hotcms_back' ) ?></a>
      <a href="/hotcms/<?php echo $module_url?>/preview/<?php echo $item->id; ?>/0" class="red_button" target="_blank">Preview</a>
      <a href="#" class="red_button save_link" target="_blank"><?php echo lang( 'hotcms_save_changes' ) ?></a>
      <a href="#" class="red_button publish_link">Publish Training</a>
      <a href="/hotcms/<?php echo $module_url?>/delete/<?php echo $item->id ?>" class="red_button" onClick="return confirmDelete('training');" style="float:right;margin-left: 5px;"><?php echo lang( 'hotcms_delete' ) ?> Training</a>
      <a href="#" class="red_button archive_link" style="float:right; <?php echo ($item->status == 1 ? '' : 'display:none;'); ?>">Archive Training</a>
    </div>
  </form>
  </div>
  <div id="training-history">

  </div>
</div>

<div id="featured-image-form" title="Featured Image">
</div>
<div id="asset-form" title="Training Assets">
</div>
<div id="variant-form" title="Training Variant">
</div>
<div id="resource-form" title="Training Resource">
</div>