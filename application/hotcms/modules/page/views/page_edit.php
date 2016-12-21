<?php
  $draft = $currentItem->draft;
?>
<div>
  <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $draft->id ?>" method="post" id="page-form">
    <?php
      echo form_hidden($form['hidden_fields']);
      echo form_hidden('hdnMode', 'edit');
    ?>
    <div class="tabs">
      <ul>
        <li><a href="#page-general" id="general"><i class="icon-file-alt"></i><span>General</span></a></li>
        <li><a href="#page-settings" id="settings"><i class="icon-cog"></i></span><span>Settings</span></a></li>
        <li><a href="#page-content" id="content"><i class="icon-picture"></i><span>Content</span></a></li>
        <li><a href="#page-permissions" id="permission"><i class="icon-lock"></i><span>Permissions</span></a></li>
        <li><a href="#page-revisions" id="history"><i class="icon-time"></i><span>History</span></a></li>
      </ul>
      <div id="page-general">
        <div class="row">
          <?php echo form_error('name', '<div class="error">','</div>');?>
          <?php echo form_label('Page Name '.lang( 'hotcms__colon' ), 'name');?>
          <?php echo form_input($form['name_input']); ?>
        </div>
        <div class="row">
          <?php echo form_label('Show in Menu '.lang( 'hotcms__colon' ), 'postal_code');?>
          <?php echo form_checkbox($menu_form['menu_visible']); ?>
        </div>
        <div class="row">
          <?php echo form_error('menu_title', '<div class="error">','</div>');?>
          <?php echo form_label('Menu Title '.lang( 'hotcms__colon' ), 'menu_title');?>
          <?php echo form_input($menu_form['menu_title']); ?>
        </div>
        <?php /* <div class="link">
        <?php echo form_error('linktype', '<div class="error">','</div>');?>
        <?php foreach($page_type_options as $option) { ?>
          <label><?php echo $option['value']; ?></label>
          <input id="linktype" name="linktype" type="radio" value="<?php echo $option['key'] ?>" <?php echo $linktype == $option['key'] ? 'checked="checked"' : ''; ?>></input>
        <?php } ?>
        </div>
        <div class="link_external <?echo $linktype!='external' ? 'panel_hidden' : 'panel_visible'; ?>">
            <!--
            <?php echo form_error('link_url','<div class="error">','</div>'); ?>
            <?php echo form_label('External URL '.' '.lang( 'hotcms__colon' ), 'title');?>
            <?php echo form_input($link_external); ?>
            -->
        </div>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'postal_code');?>
          <?php echo form_checkbox($form['active_input']); ?>
        </div>
        */ ?>
        <div class="row">
          <?php echo form_label(lang( 'hotcms_sitemap_exclude' ).' '.lang( 'hotcms__colon' ), 'exclude_sitemap');?>
          <?php echo form_checkbox($form['exclude_sitemap_input']); ?>
        </div>
        <div class="submit">
          <a href="#" class="red_button save_link" style="margin-left:5px;" target="_blank" onclick=""><?php echo lang( 'hotcms_save_changes' ) ?></a>
          <a href="/hotcms/<?php echo $module_url?>" class="red_button" style="float:right;margin-left:5px;" onclick="return confirm_discard();"><?php echo lang( 'hotcms_back' ) ?></a>
          <a onClick="return confirmDelete('page')" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $draft->id ?>" class="red_button" style="float:right;"><?php echo lang( 'hotcms_delete' ) ?> Page</a>
          <!-- a id="page-insert-buttion" href="#" class="button">Insert</a -->
        </div>
      </div>
      <div id="page-content">
        <link href="/themes/<?php echo $front_theme; ?>/css/content.css" media="all" type="text/css" rel="stylesheet" />
        <div class="layouts">
         <div id="templateHeaderWrapper" class="minimazed">
          <div id="templateHeader">Choose Template</div>
          <div id="minmaxWrapper">
            + Maximize
          </div>
         </div>
         <div id="templateWrapper" style="display:none;">
          <?php
          if ($draft->layout_id < 1) {
            $draft->layout_id = $layouts[0]->id;
          }
          foreach ($layouts as $layout) {
            echo '<div class="layout-option">';
            echo form_radio(array(
              'name'        => 'layout_id',
              'id'          => 'layout_id_' . $layout->id,
              'value'       => $layout->id,
              'checked'     => ($draft->layout_id == $layout->id)
            ));
            echo ' ' . form_label($layout->name . '<img src="/hotcms/asset/images/icons/' . $layout->icon . '" />', 'layout_' . $layout->id);
            echo "</div>\n";
          }
          ?>
         </div>
          <div class="clear"></div>
        </div>
        <?php
          if ($draft->style_sheet) {
        ?>
        <link href="/themes/<?php echo $front_theme; ?>/css/page/<?php echo $draft->style_sheet; ?>" media="all" type="text/css" rel="stylesheet" />
        <?php
          }
          $displayed = 0;
          foreach ($zones as $z) {
            echo $name = str_replace('_', ' ', $z);
            echo '<div id="' . $z . '" class="droppable-zone content-zone">';
            foreach ($draft->sections as $section) {
              if ($section->zone != $z) {
                continue;
              }
              echo '<div class="row editable" id="sectionview_' . $section->id . '">';
              echo '<div class="section-buttons">';
              echo '<div class="section-button section-move"><span class="move-icon"></span>' . $section->widget_name . '</div>';
              echo '<div class="section-button ' . ($section->section_type == 0 ? 'section-edit' : 'section-config') . '"><span class="edit-icon"></span></div>';
              echo '<div class="section-button section-delete"><span class="delete-icon"></span></div>';
              echo '</div>';

              echo '<div class="section-text">';
              echo $section->content;
              echo '</div>';
              if ($section->section_type == 1) {
                echo '<div style="display: none;" class="widget-code">' . $section->module_widget . '</div>';
              }
              echo '</div>';
              $displayed++;
            }
            echo "</div>\n";
          }
          if ($displayed < count($draft->sections)) {
            echo '<p><b>Hidden text and widgets:</b></p>';
            // use a hidden zone to hold all the other sections
            echo '<div id="hidden-zone" class="droppable-zone content-zone">';
            foreach ($draft->sections as $section) {
              if (!in_array($section->zone, $zones)) {
                echo '<div class="row editable" id="sectionview_' . $section->id . '">';
                echo '<div class="section-buttons">';
                echo '<div class="section-button section-move"><span class="move-icon"></span>' . $section->widget_name . '</div>';
                echo '<div class="section-button ' . ($section->section_type == 0 ? 'section-edit' : 'section-config') . '"><span class="edit-icon"></div>';
                echo '<div class="section-button section-delete"><span class="delete-icon"></span></div>';
                echo '</div>';
                echo '<div class="section-text">';
                echo $section->content;
                echo '</div></div>';
              }
            }
            echo "</div>\n";
          }
        ?>
        <div class="clear"></div>
        <div id="widget-panel" class="expanded">
          <div class="module_header">
              <span>Widget Library</span>
              <div class="widget_expander expanded"><i class="icon-angle-up"></i></div>
          </div>
          <div id="widget-help-text">
           <p>Drag and drop a widget from the library into a content block to create a new, editable content area.</p>
          </div>
          <div id="new-widgets">
            <div class="row text-widget cloneable">
              <div class="section-buttons">
                <div class="section-button section-move"><i class="awesome-icon icon-font"></i>Text</div>
              </div>
              <div class="section-text"><p>Text</p></div>
              <div class="widget-code" style="display:none">text:text_widget</div>
            </div>
          <?php
          foreach ($widget_array as $k => $v) {
          ?>
            <div class="cloneable section-widget">
              <div class="section-buttons">
               <div class="section-button section-move"><i class="awesome-icon icon-<?php echo strtolower($v->widget_icon_name); ?>"></i><?php echo $v->widget_name; ?></div>
              </div>
              <div class="section-text"><p><?php echo $v->widget_name; ?></p></div>
              <div class="widget-code" style="display:none"><?php echo $k; ?></div>
            </div>
          <?php
          }
          ?>
          </div>
        </div>
        <div class="submit">
          <a href="/hotcms/<?php echo $module_url?>/preview/<?php echo $draft->id; ?>/0" class="red_button" target="_blank">Preview</a>
          <a href="#" class="red_button save_link" target="_blank" onclick=""><?php echo lang( 'hotcms_save_changes' ) ?></a>
          <?php //<input type="submit" class="red_button" value="< ?php echo lang( 'hotcms_save_changes' ) ? >" /> ?>
          <a href="#" class="red_button publish_link">Publish Page</a>
          <a href="/hotcms/<?php echo $module_url?>" class="red_button" style="float:right;margin-left: 5px;" onclick="return confirm_discard();"><?php echo lang( 'hotcms_back' ) ?></a>
          <a href="/hotcms/<?php echo $module_url?>/delete/<?php echo $draft->id ?>" class="red_button" onClick="return confirmDelete('page');" style="float:right;margin-left: 5px;"><?php echo lang( 'hotcms_delete' ) ?> Page</a>
          <a href="#" class="red_button archive_link" style="float:right; <?php echo ($currentItem->status == 1 ? '' : 'display:none;'); ?>">Archive Page</a>
        </div>
        <div class="clear"></div>
      </div>
      <div id="page-settings">
        <div class="row">
          <?php echo form_error('status', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_status' ) . ' '.lang( 'hotcms__colon' ), 'status');?>
          <?php echo form_dropdown("status", $status_array, $currentItem->status); ?>
        </div>
        <div class="row">
          <?php echo form_label('Live Version '.lang( 'hotcms__colon' ));?>
          <p><?php echo $currentItem->update_timestamp > 0 ? 'last updated on ' . date('Y-m-d H:i:s', $currentItem->update_timestamp) : '&mdash;'; ?></p>
        </div>
        <div class="row">
          <?php echo form_label('Working Version '.lang( 'hotcms__colon' ));?>
          <p><?php echo $draft->update_timestamp > 0 ? 'last updated on ' . date('Y-m-d H:i:s', $draft->update_timestamp) : '&mdash;'; ?></p>
        </div>
        <div class="row">
          <?php echo form_error('url', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_url' ).' '.lang( 'hotcms__colon' ), 'url');?>
          <?php echo form_input($form['url_input']); ?>
        </div>
        <div class="row">
          <?php echo form_error('url_parser', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_url_parser' ) . ' '.lang( 'hotcms__colon' ), 'url_parser');?>
          <?php echo form_dropdown("url_parser", $module_array, $draft->url_parser); ?>
          &nbsp; (a module that helps parsing the URL when it contains a wildcard)
        </div>
        <div class="row">&nbsp;</div>
        <div class="row" style="display:none">
          <?php echo form_error('heading', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_heading' ).' '.lang( 'hotcms__colon' ), 'heading');?>
          <?php echo form_input($form['heading_input']); ?> &nbsp; <span class="note">(Headline that appears on top of page content.)</span>
        </div>
        <div class="row">
          <?php echo form_error('meta_title', '<div class="error">','</div>');?>
          <?php echo form_label('Meta ' . lang( 'hotcms_title' ).' '.lang( 'hotcms__colon' ), 'meta_title');?>
          <?php echo form_input($form['meta_title_input']); ?> &nbsp;
        </div>
        <div class="row">
          <?php echo form_error('meta_description', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_meta_description' ).' '.lang( 'hotcms__colon' ), 'meta_description');?>
          <?php echo form_input($form['meta_description_input']); ?>
        </div>
        <div class="row">
          <?php echo form_error('meta_keyword', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_meta_keywords' ).' '.lang( 'hotcms__colon' ), 'meta_keyword');?>
          <?php echo form_input($form['meta_keyword_input']); ?>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
          <?php echo form_error('style_sheet', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_stylesheet' ).lang( 'hotcms__colon' ), 'style_sheet');?>
          <?php echo form_input($form['style_sheet_input']); ?>
        </div>
        <div class="row">
          <?php echo form_error('javascript', '<div class="error">','</div>');?>
          <?php echo form_label(lang( 'hotcms_javascript' ).lang( 'hotcms__colon' ), 'javascript');?>
          <?php echo form_input($form['javascript_input']); ?>
        </div>
        <div class="row">&nbsp;</div>
        <?php
        if ($currentItem->scheduled_publish_timestamp > 0) {
          $sp_hour = date('H', $currentItem->scheduled_publish_timestamp);
          $sp_minute = date('i', $currentItem->scheduled_publish_timestamp);
          $sp_timezone = date('e', $currentItem->scheduled_publish_timestamp);
        }
        else {
          $sp_hour = '00';
          $sp_minute = '00';
          $sp_timezone = date('e');
        }
//echo '<pre>';
//var_dump($sp_timezone);
//$timezone_abbreviations = DateTimeZone::listAbbreviations();
//print_r(array_keys($timezone_abbreviations));
//echo '</pre>';
        if ($currentItem->scheduled_archive_timestamp > 0) {
          $sa_hour = date('H', $currentItem->scheduled_archive_timestamp);
          $sa_minute = date('i', $currentItem->scheduled_archive_timestamp);
          $sa_timezone = date('e', $currentItem->scheduled_archive_timestamp);
        }
        else {
          $sa_hour = '00';
          $sa_minute = '00';
          $sa_timezone = date('e');
        }
        $add='class="auto_width"';
        ?>
        <div class="row">
          <?php echo form_error('scheduled_publish_date', '<div class="error">','</div>');?>
          <?php echo form_label('Scheduled Publish Date' . lang('hotcms__colon'), 'scheduled_publish_date');?>
          <?php echo form_input($form['scheduled_publish_date_input']); ?> &nbsp;
          <?php echo form_dropdown("scheduled_publish_hour", $hour_array, $sp_hour,$add); ?> :
          <?php echo form_dropdown("scheduled_publish_minute", $minute_array, $sp_minute,$add); ?>
          <?php echo form_dropdown("scheduled_publish_timezone", $timezone_array, $sp_timezone); ?>
        </div>
        <div class="row">
          <?php echo form_error('scheduled_archive_date', '<div class="error">','</div>');?>
          <?php echo form_label('Scheduled Archive Date' . lang('hotcms__colon'), 'scheduled_archive_date');?>
          <?php echo form_input($form['scheduled_archive_date_input']); ?> &nbsp;
          <?php echo form_dropdown("scheduled_archive_hour", $hour_array, $sa_hour, $add); ?> :
          <?php echo form_dropdown("scheduled_archive_minute", $minute_array, $sa_minute, $add); ?>
          <?php echo form_dropdown("scheduled_archive_timezone", $timezone_array, $sa_timezone); ?>
        </div>

        <div class="submit">
          <a href="#" class="red_button save_link" style="margin-left:5px;" target="_blank" onclick=""><?php echo lang( 'hotcms_save_changes' ) ?></a>
          <a href="/hotcms/<?php echo $module_url?>" class="red_button" style="float:right;margin-left:5px;" onclick="return confirm_discard();"><?php echo lang( 'hotcms_back' ) ?></a>
          <a onClick="return confirmDelete('page')" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $draft->id ?>" class="red_button" style="float:right;"><?php echo lang( 'hotcms_delete' ) ?> Page</a>
        </div>
      </div>
      <div id="page-permissions">
        <p>Select roles that are allowed to view this page, or leave all un-checked to allow all roles.</p>
        <?php
        foreach ($form['roles'] as $k => $v) {
          echo '<div class="row">';
          echo form_error('javascript', '<div class="error">','</div>');
          echo form_label($v . lang( 'hotcms__colon' ), 'javascript');
          echo form_checkbox($form['permissions'][$k]);
          echo '</div>';
        }
        ?>
        <div class="submit">
          <a href="#" class="red_button save_link" style="margin-left:5px;" target="_blank" onclick=""><?php echo lang( 'hotcms_save_changes' ) ?></a>
          <a href="/hotcms/<?php echo $module_url?>" class="red_button" style="float:right;margin-left:5px;" onclick="return confirm_discard();"><?php echo lang( 'hotcms_back' ) ?></a>
        </div>
      </div>
      <div id="page-revisions" class="table">
        <table class="tablesorter">
          <tr><th>Name</th><th>Author</th><th>Status</th><th>Date &amp; Time</th><th>Preview</th><th>Revert Version</th></tr>
          <?php
          if (is_array($currentItem->revisions) && count($currentItem->revisions) > 0) {
            $status_labels = array('0'=>'Draft', '1'=>'Published', '2'=>'Archived');
            foreach ($currentItem->revisions as $r) {
              echo '<tr><td>' . $r->name . '</td><td>' . $r->username . '</td>';
              echo '<td>' . $status_labels[$r->status] . '</td>';
              echo '<td class="nowrap">';
              echo ($r->update_timestamp > 0 ? date('Y-m-d H:i:s', $r->update_timestamp) : '&mdash;');
              echo ($r->id == $currentItem->revision_id ? ' (current)' : '');
              echo '</td><td>';
              echo '<a class="red_button_smaller" href="/hotcms/page/preview/' . $r->page_id . '/' . $r->id . '" target="_blank">Preview</a></td><td>';
              if ($r->id != $currentItem->revision_id) {
                echo '<a class="revert-revision red_button_smaller" href="/hotcms/page/ajax_revert/' . $r->page_id . '/' . $r->id . '">Revert</a>';
              }
              echo "</td></tr>\n";
            }
          }
          else {
            echo "<tr><td colspan=\"6\">No revisions were found.</td></tr>\n";
          }
          ?>
        </table>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
    </div>

  </form>

  <div class="row editable" id="sectionview_text" style="display:none;">
    <div class="section-buttons">
      <div class="section-button section-move"><span class="move-icon"></span>Text</div>
      <div class="section-button section-edit"><span class="edit-icon"></span></div>
      <div class="section-button section-delete"><span class="delete-icon"></span></div>
    </div>
    <div class="section-text"></div>
  </div>
  <div class="row editable section-widget" id="sectionview_widget" style="display:none;">
    <div class="section-buttons">
      <div class="section-button section-move"><span class="move-icon"></span>Widget</div>
      <div class="section-button section-config"><span class="edit-icon"></span></div>
      <div class="section-button section-delete"><span class="delete-icon"></span></div>
    </div>
  </div>

  <?php /* div class="dialog-insert">
    <div class="submit">
      <p>I would like to insert a</p>
      <input type="radio" name="insert-type" id="insert-type-0" value="0" /> <label for="insert-type-0">text area</label><br />
      <input type="radio" name="insert-type" id="insert-type-1" value="1" /> <label for="insert-type-1">widget: </label>
      <select name="widget" id="widget">
        <option value=""> -- select module / widget -- </option>
        <?php
        foreach ($widget_array as $k => $v) {
          echo '<option value="' . $k . '">' . $v . "</option>\n";
        }
        ?>
      </select>
      <br />
    </div>
  </div */ ?>

  <div class="dialog-config">
    <div id="widget-config">
      <br />
    </div>
  </div>

  <div class="dialog-wysiwyg">
    <div class="divTinyMCE">
      <textarea id="txtTinyMCE" name="txtTinyMCE" class="tinymce" rows="40" cols="82"><?php echo set_value( 'txtTinyMCE' ) ?></textarea>
    </div>
  </div>
</div>