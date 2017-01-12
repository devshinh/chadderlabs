<?php
$draft = $currentItem->draft;
?>
<div>
    <?php
    echo form_hidden($form['hidden_fields']);
    echo form_hidden('hdnMode', 'edit');
    ?>
    <div class="tabs">
      <ul>
        <li><a href="#page-general" id="general"><span id="general_icon"></span><span>General</span></a></li>
        <li><a href="#page-content" id="content"><span id="content_icon"></span><span>Content</span></a></li>
        <li><a href="#page-items" id="content"><span id=""></span><span>Training Items</span></a></li>
        <li><a href="#page-revisions" id="history"><span id="history_icon"></span><span>History</span></a></li>
      </ul>
      <div id="page-general">
        <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $currentItem->id ?>" method="post" id="news-form">
        <div class="row">
          <?php echo form_error('title', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_title') . lang('hotcms__colon'), 'title'); ?>
          <?php echo form_input($form['title_input']); ?> &nbsp;
        </div>
        <div class="row">
          <?php echo form_error('snippet', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_snippet') . lang('hotcms__colon'), 'snippet'); ?>
          <?php echo form_textarea($form['snippet_input']); ?>
        </div>
        <div class="row">
          <?php echo form_error('status', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_status') . lang('hotcms__colon'), 'status'); ?>
          <?php echo form_dropdown("status", $status_array, $currentItem->status); ?>
        </div>
        <div class="row">
          <?php echo form_label('Working Version' . lang('hotcms__colon')); ?>
          <p><?php echo $draft->update_timestamp > 0 ? 'last updated on ' . date('Y-m-d H:i:s', $draft->update_timestamp) : '&mdash;'; ?></p>
        </div>
        <div class="row">
          <?php echo form_label('Live Version' . lang('hotcms__colon')); ?>
          <p><?php echo $currentItem->update_timestamp > 0 ? 'last updated on ' . date('Y-m-d H:i:s', $currentItem->update_timestamp) : '&mdash;'; ?></p>
        </div>
        <div class="row">
          <?php echo form_error('scheduled_publish_date', '<div class="error">', '</div>'); ?>
          <?php echo form_label('Scheduled Publish Date' . lang('hotcms__colon'), 'scheduled_publish_date'); ?>
          <?php echo form_input($form['scheduled_publish_date_input']); ?> &nbsp;
        </div>
        <div class="row">
          <?php echo form_error('scheduled_archive_date', '<div class="error">', '</div>'); ?>
          <?php echo form_label('Scheduled Archive Date' . lang('hotcms__colon'), 'scheduled_archive_date'); ?>
          <?php echo form_input($form['scheduled_archive_date_input']); ?> &nbsp;
        </div>
        <div class="row">&nbsp;</div>
        <div class="submit">
          <a href="/hotcms/<?php echo $module_url ?>/preview/<?php echo $currentItem->id; ?>/0" class="red_button" target="_blank">Preview</a>
          <a href="#" class="red_button save_link" target="_blank" onclick=""><?php echo lang('hotcms_save_changes') ?></a>
          <a href="#" class="red_button publish_link">Publish</a>
          <a href="/hotcms/<?php echo $module_url ?>" class="red_button" style="float:right;margin-left: 5px;" onclick="return confirm_discard();"><?php echo lang('hotcms_back') ?></a>
          <a href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $currentItem->id ?>" class="red_button" onClick="return confirmDelete('page');" style="float:right;margin-left: 5px;"><?php echo lang('hotcms_delete') ?></a>
          <a href="#" class="red_button archive_link" style="float:right; <?php echo ($currentItem->status == 1 ? '' : 'display:none;'); ?>">Archive</a>
        </div>
        
      </div>
      <div id="page-content">
        
        <link href="/themes/<?php echo $front_theme; ?>/css/content.css" media="all" type="text/css" rel="stylesheet" />
    <div id="news_image_div" style="padding: 10px;">
      <label>News Image</label>
      <div id="news_image">
        <?php
        if (!empty($currentItem->featured_image_id)) {
          echo $currentItem->featured_image->full_html;
        }
        ?>
      </div>
      <a href="<?php echo $currentItem->featured_image_id; ?>" class="red_button news_image_link" data-id="<?php echo $currentItem->id ?>">Choose</a>
      <input type="hidden" name="featured_image_id" id="featured_image_id" value="<?php echo $currentItem->featured_image_id; ?>" />
    </div>        
        <?php
        echo '<div class="row editable" id="sectionview_body">';
        echo '<div class="section-buttons">';
        echo '<div class="section-button">Body Text</div>';
        echo '<div class="section-button section-edit"><span class="edit-icon"></span></div>';
        //echo '<div class="section-button section-delete"><span class="delete-icon"></span></div>';
        echo '</div>';
        echo '<div class="section-text">';
        echo $draft->body;
        echo '</div>';
        echo '</div>';
        ?>
        <div class="clear"></div>
        <div class="submit">
          <a href="/hotcms/<?php echo $module_url ?>/preview/<?php echo $currentItem->id; ?>/0" class="red_button" target="_blank">Preview</a>
          <a href="#" class="red_button save_link" target="_blank" onclick=""><?php echo lang('hotcms_save_changes') ?></a>
          <?php //<input type="submit" class="red_button" value="< ?php echo lang( 'hotcms_save_changes' ) ? >" /> ?>
          <a href="#" class="red_button publish_link">Publish</a>
          <a href="/hotcms/<?php echo $module_url ?>" class="red_button" style="float:right;margin-left: 5px;" onclick="return confirm_discard();"><?php echo lang('hotcms_back') ?></a>
          <a href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $currentItem->id ?>" class="red_button" onClick="return confirmDelete('page');" style="float:right;margin-left: 5px;"><?php echo lang('hotcms_delete') ?></a>
          <a href="#" class="red_button archive_link" style="float:right; <?php echo ($currentItem->status == 1 ? '' : 'display:none;'); ?>">Archive</a>
        </div>
        <div class="clear"></div>
      </form>
      </div>
      <div id="page-items">
        <div class="row">  
          <div class="table">   
            <table id="tableCurrent" class="tablesorter">
              <thead>
                <tr>
                  <th><?php echo lang('hotcms_title') ?></th>
                  <th class="action"><?php echo lang('hotcms_edit') ?></th>
                  <th class="action"><?php echo lang('hotcms_delete') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (isset($items)) {
                  foreach ($items as $row) {
                    ?>
                    <tr id="trData_<?php echo $row->id ?>">
                      <td>
                        <?php echo $row->title; ?>
                      </td>
                      <td>
                        <a href="<?php printf('/hotcms/%s/edit/%s', 'training', $row->id) ?>"><div class="btn-edit"></div></a>
                      </td>
                      <td class="last">
                        <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_item/%s/%s', $module_url, $row->id, $currentItem->id) ?>"><div class="btn-delete"></div></a>
                      </td>              
                    </tr>
                    <?php
                  }
                }
                ?>
              </tbody>
            </table>
          <?php print ('<div class="row">'.$items_msg.'</div>');?>
          </div>
        </div>
        <form action="/hotcms/news/edit_items/<?php echo $currentItem->id ?>#page-items" method="post">
          <div class="row">  
            <?php echo $items_select; ?>
          </div>        
          <input type="hidden" value="3" name="currentTabIndex" id="currentTabIndex">
          <div class="submit">
            <input type="submit" class="input red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
            <a style="float:right;margin-left: 5px;" href="/hotcms/<?php echo $module_url ?>/" class="red_button"><?php echo lang('hotcms_back') ?></a>
          </div>    
        </form>       
      </div>
        
        
      <div id="page-revisions" class="table">
        <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $currentItem->id ?>" method="post" id="news-form">
        <table class="tablesorter">
          <tr><th>Title</th><th>Author</th><th>Status</th><th>Date &amp; Time</th><th>Preview</th><th>Revert Version</th></tr>
          <?php
          if (is_array($currentItem->revisions) && count($currentItem->revisions) > 0) {
            $status_labels = array('0' => 'Draft', '1' => 'Published', '2' => 'Archived');
            foreach ($currentItem->revisions as $r) {
              echo '<tr><td>' . $r->title . '</td><td>' . $r->username . '</td>';
              echo '<td>' . $status_labels[$r->status] . '</td>';
              echo '<td class="nowrap">';
              echo $r->update_timestamp > 0 ? date('Y-m-d H:i:s', $r->update_timestamp) : '&mdash;';
              echo '</td><td>';
              echo '<a class="red_button_smaller" href="/hotcms/' . $module_url . '/preview/' . $r->news_id . '/' . $r->id . '" target="_blank">Preview</a></td><td class="last">';
              if ($r->id == $currentItem->revision_id) {
                echo '(current)';
              } else {
                echo '<a class="revert-revision red_button_smaller" href="/hotcms/' . $module_url . '/ajax_revert/' . $r->news_id . '/' . $r->id . '">Revert</a>';
              }
              echo "</td></tr>\n";
            }
          } else {
            echo "<tr><td colspan=\"6\">No revisions were found.</td></tr>\n";
          }
          ?>
        </table>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
      </form>
    </div>

  <div class="row editable" id="sectionview_text" style="display:none;">
    <div class="section-buttons">
      <div class="section-button section-move"><span class="move-icon"></span>Text</div>
      <div class="section-button section-edit"><span class="edit-icon"></span></div>
      <div class="section-button section-delete"><span class="delete-icon"></span></div>
    </div>
    <div class="section-text"></div>
  </div>
  <div class="row editable section-widget" id="sectionview_widget sectionview_image" style="display:none;">
    <div class="section-buttons">
      <div class="section-button section-move"><span class="move-icon"></span>Widget</div>
      <div class="section-button section-config"><span class="edit-icon"></span></div>
      <div class="section-button section-delete"><span class="delete-icon"></span></div>
    </div>
  </div>

  <div class="dialog-config">
    <div id="widget-config">
      <br />
    </div>
  </div>

  <div class="dialog-wysiwyg">
    <div class="divTinyMCE">
      <textarea id="txtTinyMCE" name="txtTinyMCE" class="tinymce" rows="40" cols="82"><?php echo set_value('txtTinyMCE') ?></textarea>
    </div>
  </div>
  
  <div id="news-image-form" title="News Image">
  </div>  

</div>