<div class="module">
<div class="row">
        <div class="control_buttons">
            <a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a>
            <a class="red_button" id="filter_button"><?php echo lang('hotcms_filter'); ?></a>
        </div>
        <div id="filters-modal" class="ui-dialog" title="Filters">
            <form class="filter_form" method="post">
                <?php
                echo form_hidden($form['hidden_modal']);
                ?>      

                <div class="row selected_filters">
                    <b>Selected filters:</b>
                    <i class="red"><?php print $active_filters ?></i>                    
                </div>                
                <div class="filter_header closed">Filter by Category
                    <div class="filter_header_arrow"></div>
                </div>
                <div class="row filter_options closed">
                    <?php
                    $i=0;
                    foreach ($form['categories'] as $cat_id => $cat_name) {
                        $i++;
                        echo '<div class="checkbox_option">';
                        if (is_array($filters['category'])) {
                            $checked = in_array($cat_name, $filters['category']);
                        } else {
                            $checked = false;
                        }
                        $extra = 'id="category_option_'.$i.'"';
                        echo form_checkbox('category[]', $cat_name, $checked, $extra);
                        echo '<label for="category_option_'.$i.'">'.$cat_name.'</label>';
                        echo '</div>';
                    }
                    ?>                    
                </div>
                <div class="filter_header closed">Filter by Type
                        <div class="filter_header_arrow"></div>
                </div>
                <div class="row filter_options closed">
                    <?php
                    $i=0;
                    foreach ($form['type_options'] as $type_id => $type_name) {
                        $i++;
                        echo '<div class="checkbox_option">';
                        if (is_array($filters['type'])) {
                            $checked = in_array($type_id, $filters['type']);
                        } else {
                            $checked = false;
                        }
                        $extra = 'id="type_option_'.$i.'"';
                        echo form_checkbox('type[]', $type_id, $checked, $extra);
                        echo '<label for="type_option_'.$i.'">'.$type_name.'</label>';
                        echo '</div>';
                    }
                    //echo form_dropdown('country_code', $form['country_code_options'], $filters['country']);
                    ?>
                </div>   
                <div class="row submit">
                  <input type="submit" class="red_button" value="<?php echo lang('hotcms_filter'); ?>" />
                  <a class="red_button" id="remove_all_filters">Remove all</a>
                </div>
            </form>
        </div>
        <div class="active_filters_wrapper">
            <b>Active filters:</b>
            <i class="red"><?php print $active_filters ?></i>
        </div>
        <div id="filters_div">
            <form id="search_form" method="post">
                <?php
                echo form_hidden($form['hidden']);
                ?>
                <div class="search_bar">

                    <?php
                    echo form_label(lang('hotcms_search') . lang('hotcms__colon'), 'keyword');
                    echo form_input($form['keyword_input']);
                    ?>


                    <input type="submit" class="red_button" value="<?php echo lang('hotcms_search'); ?>" />

                </div>
            </form>
        </div>
    </div>    
  <?php if (!empty($aCurrent)) { ?>
    <div class="table">
      <table id="tableCurrent" class="table_sorter">
        <thead>
          <tr>
            <th></th>
            <th id="sortable_name" class="sortable 
            <?php if ($filters['sort_by'] == 'name') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang('hotcms_name'); ?></th>
            <th id="sortable_type" class="sortable 
            <?php if ($filters['sort_by'] == 'type') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang('hotcms_type') ?></th>
            <th class="filter">
                <div class="filter_icon <?php if ($filters['type'] != '') echo 'active'; ?>" id="type"></div></th>  
            <th id="sortable_update_date" class="sortable 
            <?php if ($filters['sort_by'] == 'update_date') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang('hotcms_date_updated') ?></th>
            <th id="sortable_create_date" class="sortable 
            <?php if ($filters['sort_by'] == 'create_date') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang('hotcms_date_created') ?></th>
            <th id="sortable_asset_category_id" class="sortable 
            <?php if ($filters['sort_by'] == 'asset_category_id') {
              echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
            }?>"><?php echo lang('hotcms_category') ?></th>
            <th class="filter">
                <div class="filter_icon <?php if ($filters['category'] != '') echo 'active'; ?>" id="category"></div></th>             
            <th class="action"><?php echo lang('hotcms_edit') ?></th>
            <th class="action"><?php echo lang('hotcms_delete') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($aCurrent as $asset) { ?>
            <tr id="trData_<?php echo $asset->id ?>">
              <td>
                <?php echo $asset->thumb_html; ?>
              </td>
              <td>
                <?php echo $asset->name ?>
              </td>
              <td colspan="2">
                <?php
                switch ($asset->type) {
                  case 1: $type = 'Image';
                    break;
                  case 2: $type = 'Document';
                    break;
                  case 3: $type = 'Video';
                    break;
                  case 4: $type = 'Audio';
                    break;
                  default: $type = 'Unknown';
                }
                echo $type;
                ?>

              </td>
              <td>
                <?php
                if (!empty($asset->update_date)) {
                  echo $asset->update_date;
                } else {
                  ?>&mdash;<?php } ?>
              </td>
              <td>
                <?php
                if (!empty($asset->create_date)) {
                  echo $asset->create_date;
                } else {
                  ?>&mdash;<?php } ?>
              </td>
              <td colspan="2">
    <?php
    if (!empty($asset->category_name)) {
      echo $asset->category_name;
    }
    else {
      ?>&mdash;<?php } ?>
              </td>
              <td class="centered">
                <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $asset->id) ?>"><div class="btn-edit"></div></a>
              </td>
              <td class="centered">
                <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $asset->id) ?>"><div class="btn-delete"></div></a>
              </td>
            </tr>
    <?php } ?>
        </tbody>
      </table>
      <div id="pagination">
  <?php echo $pagination; ?>
      </div>
        <div id="pagination_items">
            <form id="pagination_form" method="post" action="<?php echo $module_url?>/index">
                <?php
                echo form_hidden($form['hidden']);
                ?>        
                <input type="hidden" value="true" name="per_page_change">
                <?php
                $id = 'id="per_page_select"';
                echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page'], $id);
                ?>
            </form>
        </div>        
    </div>
<?php } ?>
</div>
