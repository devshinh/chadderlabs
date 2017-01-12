<div class="tabs">
    <ul>
        <li><a href="#training-detail" id="detail-tab"><span id="detail_icon"></span><span>Detail</span></a></li>
        <li><a href="#training-history" id="history-tab"><span id="history_icon"></span><span>History</span></a></li>
    </ul>
    <div id="training-detail">
        <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $item->id ?>" method="post" id="training-form">
            <?php
            echo form_hidden($form['hidden_fields']);
            //echo form_hidden('hdnMode', 'edit');
            ?>
            <div class="leftColumn">
                <div id="general">
                    <div class="row">
                        <?php echo form_error('category_id', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('hotcms_category') . lang('hotcms__colon'), 'category_id'); ?>
                        <?php echo form_dropdown("category_id", $categories, $item->category_id); ?>
                    </div>
                    <div class="row">
                        <?php echo form_error('target_id', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('hotcms_target') . lang('hotcms__colon'), 'target_id'); ?>
                        <?php echo form_dropdown("target_id", $targets, $item->target_id); ?>
                    </div>
                    <div class="row">
                        <?php echo form_error('title', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('hotcms_title') . lang('hotcms__colon'), 'title'); ?>
                        <?php echo form_input($form['title']); ?>
                    </div>
                    <div class="row">
                        <?php echo form_error('status', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('hotcms_status') . lang('hotcms__colon'), 'status'); ?>
                        <?php echo form_dropdown("status", $status_array, $item->status); ?>
                    </div>
                    <div class="row">
                        <?php echo form_error('title', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('training_link') . lang('hotcms__colon'), 'link'); ?>
                        <?php echo form_input($form['link']); ?>
                    </div>
                    <div class="row">
                        <?php echo form_error('featured', '<div class="error">', '</div>'); ?>
                        <?php echo form_label(lang('hotcms_featured') . lang('hotcms__colon'), 'featured'); ?>
                        <?php echo form_checkbox($form['featured']); ?>
                    </div>
                </div>
            <div id="featured_image_div">
                <label>Featured Image</label>
                <div class="row">
                <a href="<?php echo $item->featured_image_id; ?>" class="red_button featured_image_link">Choose</a>                 
                <div id="featured_image">
                    <?php
                    if (!empty($item->featured_image)) {
                        //echo $item->featured_image->thumb_html;
                       printf('<img src="%s" alt="%s" title="%s" height="200px"/>', $item->featured_image->full_path, $item->featured_image->name, $item->featured_image->description);                  
                    }
                    ?>
                </div>
                </div>
            </div>
</div>
            <div class="rightColumn">
            <?php
            if (count($tag_types) > 0) {
                ?>
                <div id="tags">
                    <?php
                    foreach ($tag_types as $tag_type) {
                        echo form_label($tag_type->type_name . lang('hotcms__colon'), '', array('style' => 'font-weight:bold'));
                        echo '<div class="tag_type">';
                        foreach ($tags as $tag) {
                            if ($tag->type_id != $tag_type->id) {
                                continue;
                            }
                            echo form_checkbox(array(
                                'name' => 'tags[]',
                                'id' => 'tag_' . $tag->id,
                                'value' => $tag->id,
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
            </div>
            <div class="clear"></div>
            <h2>Assets</h2>
            <div id="assets" class="tabs-assets">
                <ul>
                    <li><a href="#assets-image" id="image-tab"><span id="image_icon"></span><span>Image</span></a></li>
                    <li><a href="#assets-video" id="video-tab"><span id="video_icon"></span><span>Video</span></a></li>
                    <li><a href="#assets-audio" id="audio-tab"><span id="audio_icon"></span><span>Audio</span></a></li>
                </ul>
                <div id="assets-image">
                    <div class="table">
                    <table id="image_table" class="framed tablesorter">
                        <tr>
                            <th class="preview">Preview</th>
                            <th>Title</th>
                            <th class="action">Edit</th>
                            <th class="action last">Delete</th>
                        </tr>
                        <tbody id="ui-sortable">
                        <?php
                        foreach ($item->assets as $k => $v) {
                            if ($v->type == 1) { // image
                                echo '<tr id="'.$k.'" class="groupItem asset_row">';
                                echo '<td class="preview">' . $v->thumb_html . '</td>';
                                echo '<td>' . $v->name . '</td>';
                                echo '<td class="action"><a href="' . $v->id . '" class="edit_asset_link"><div class="btn-edit"></div></a></td>';
                                echo '<td class="action last"><a href="' . $v->id . '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    </div>
                    <a href="<?php echo $item->id; ?>" class="red_button add_asset_image_link">Add Image</a>
                </div>
                <div id="assets-video">
                    <div class="table">
                    <table id="video_table" class="framed">
                        <tr>
                            <th class="preview">Preview</th>
                            <th>Title</th>
                            <th class="action">Edit</th>
                            <th class="action last">Delete</th>
                        </tr>
                        <?php
                        foreach ($item->assets as $v) {
                            if ($v->type == 3) { // video
                                echo '<tr class="asset_row">';
                                echo '<td class="preview">' . $v->thumb_html . '</td>';
                                echo '<td>' . $v->name . '</td>';
                                echo '<td class="action"><a href="' . $v->id . '" class="edit_video_link"><div class="btn-edit"></div></a></td>';
                                echo '<td class="action last"><a href="' . $v->id . '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </table>
                    </div>
                    <a href="<?php echo $item->id; ?>" class="red_button add_asset_video_link">Add Video</a>
                </div>
                <div id="assets-audio">
                    <div class="table">
                    <table id="audio_table" class="framed">
                        <tr>
                            <th class="preview">Preview</th>
                            <th>Title</th>
                            <th class="action">Edit</th>
                            <th class="action last">Delete</th>
                        </tr>
                        <?php
                        foreach ($item->assets as $v) {
                            if ($v->type == 4) { // audio
                                echo '<tr class="asset_row">';
                                echo '<td class="preview">' . $v->thumb_html . '</td>';
                                echo '<td>' . $v->name . '</td>';
                                echo '<td class="action"><a href="' . $v->id . '" class="edit_audio_link"><div class="btn-edit"></div></a></td>';
                                echo '<td class="action last"><a href="' . $v->id . '" class="delete_asset_link"><div class="btn-delete"></div></a></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </table>
                </div>
                    <a href="<?php echo $item->id; ?>" class="red_button add_asset_audio_link">Add Audio</a>
                </div>
            </div>
            <div id="description-div">
                <h2>Description</h2>
                <div class="divTinyMCE">
                    <?php echo form_textarea($form['description']); ?>
                </div>
            </div>
            <div id="features-div">
                <h2>Features</h2>
                <div class="divTinyMCE">
                    <?php echo form_textarea($form['features']); ?>
                </div>
            </div>
            <div id="variants">
                <h2>Variants</h2>
                <div class="table">
                <table id="variant_table" class="framed">
                    <?php
                    echo '<tr>';
                    foreach ($variant_fields as $vf) {
                        echo '<th>';
                        echo $vf->label;
                        echo '</th>';
                    }
                    echo '<th class="action">Edit</th>';
                    echo '<th class="action last">Delete</th>';
                    echo '</tr>';
                    foreach ($item->variants as $v) {
                        echo '<tr class="variant_row">';
                        foreach ($variant_fields as $vf) {
                            echo '<td class="variant-preview">';
                            foreach ($v->details as $vd) {
                                if ($vd->field_id == $vf->id) {
                                    if ($vf->field_type == 'image') {
                                        echo $vd->image->thumb_html;
                                    }elseif($vf->field_type == 'checkboxes') {
                                        foreach (explode('\n',$vd->value) as $value){
                                           echo $value.'<br />';
                                        }                                      
                                    }else {
                                        echo $vd->value;
                                    }
                                    continue;
                                }
                            }
                            echo '</td>';
                        }
                        echo '<td class="action"><a href="' . $v->id . '" class="edit_variant_link"><div class="btn-edit"></div></a></td>';
                        echo '<td class="action last"><a href="' . $v->id . '" class="delete_variant_link"><div class="btn-delete"></div></a></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
                </div>
                <a href="<?php echo $item->id; ?>" class="red_button add_variant_link">Add Variant</a>
            </div>
            <div id="resources">
                <h2>Resources</h2>
                <div class="table">
                <table id="resource_table" class="framed">
                    <tr>
                        <th>File</th>
                        <th>Title</th>
                        <th class="action">Edit</th>
                        <th class="action last">Delete</th>
                    </tr>
                    <?php
                    foreach ($item->resources as $file) {
                        echo '<tr class="resource_row">';
                        echo '<td>' . $file->full_html . '</td>';
                        echo '<td>' . $file->name . '</td>';
                        echo '<td class="action"><a href="' . $file->id . '" class="edit_resource_link"><div class="btn-edit"></div></a></td>';
                        echo '<td class="action last"><a href="' . $file->id . '" class="delete_resource_link"><div class="btn-delete"></div></a></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
                </div>
                <a href="<?php echo $item->id; ?>" class="red_button add_resource_link">Add Resource</a>
            </div>
            <div class="submit">
                <a href="/hotcms/<?php echo $module_url ?>" class="red_button" onclick="return confirm_discard();"><?php echo lang('hotcms_back') ?></a>
                <a href="/hotcms/<?php echo $module_url ?>/preview/<?php echo $item->id; ?>/0" class="red_button" target="_blank">Preview</a>
                <a href="#" class="red_button save_link" target="_blank"><?php echo lang('hotcms_save_changes') ?></a>
                <a href="#" class="red_button publish_link">Publish Training</a>
                <a href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $item->id ?>" class="red_button" onClick="return confirmDelete('training');" style="float:right;margin-left: 5px;"><?php echo lang('hotcms_delete') ?> Training</a>
                <a href="#" class="red_button archive_link" style="float:right; <?php echo ($item->status == 1 ? '' : 'display:none;'); ?>">Archive Training</a>
            </div>
        </form>
    </div>
    <div id="training-history">
        <div class="table">
        <table class="tablesorter">
            <tr><th>Title</th><th>Date &amp; Time</th><th>User</th><th>Status</th><th class="action">Preview</th><th class="action last">Revert</th></tr>
            <?php
            if (is_array($item->revisions) && count($item->revisions) > 0) {
                $status_labels = array('0' => 'Draft', '1' => 'Published', '2' => 'Archived');
                foreach ($item->revisions as $r) {
                    echo '<tr><td>' . $r->title . '</td>';
                    echo '<td class="nowrap">';
                    echo ($r->update_timestamp > 0 ? date('Y-m-d H:i:s', $r->update_timestamp) : '&mdash;');
                    echo ($r->id == $item->revision_id ? ' (current)' : '');
                    echo '<td>' . $r->username . '</td><td>' . $status_labels[$r->status] . '</td>';
                    echo '<td class="action"><a class="red_button_smaller" href="/hotcms/training/preview/' . $r->training_id . '/' . $r->id . '" target="_blank">Preview</a></td><td class="action last">';
                    if ($r->id != $item->revision_id) {
                        echo '<a class="revert-revision red_button_smaller" href="/hotcms/training/ajax_revert/' . $r->training_id . '/' . $r->id . '">Revert</a>';
                    }
                    echo "</td></tr>\n";
                }
            } else {
                echo "<tr><td colspan=\"6\">No revisions were found.</td></tr>\n";
            }
            ?>
        </table>
        </div>
    </div>
</div>

<div id="featured-image-form" title="Featured Image">
</div>
<div id="asset-form" title="Training Assets">
</div>
<div id="variant-form" title="Training Variant">
</div>
<div id="variant-image-form" title="Select Image">
</div>
<div id="resource-form" title="Training Resource">
</div>