<div class="module">
<div>
  <div class="row">
  <?php
    echo form_label(lang( "hotcms_organization" ) . " " . lang( "hotcms_name" ) . lang( "hotcms__colon" ));
    echo $retailer->name;
  ?>
  </div>
  <div class="row">
  <?php
    echo form_label(lang('hotcms_country')  . lang('hotcms__colon'));
    echo $retailer->country;
  ?>
  </div>
  <div class="row">
    <?php
      echo form_label(lang('hotcms_status')  . lang('hotcms__colon'));
      switch ($retailer->status) {
        case 1:
          echo lang('hotcms_confirmed');
          break;
        case 2:
          echo lang('hotcms_closed');
          break;
        default:
          echo lang('hotcms_pending');
      }
    ?>
  </div>
</div>
    <div class="row">
        <div class="control_buttons">
<a class="red_button" href="<?php printf('/hotcms/%s/store_create/%s', $module_url, $retailer->id) ?>"><?php printf('%s', $add_new_text) ?></a>
<a href="/hotcms/<?php echo $module_url; ?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang('hotcms_back') . " to ".lang("hotcms_organization")." List" ?></a>
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
                <div class="filter_header closed">Filter by Status
                    <div class="filter_header_arrow"></div>
                </div>
                <div class="row filter_options closed">
                    <?php
                    $i=0;
                    foreach ($form['status_options'] as $status_value => $status_name) {
                        $i++;
                        echo '<div>';
                        if (is_array($filters['status'])) {
                            $checked = in_array($status_value, $filters['status']);
                        } else {
                            $checked = false;
                        }
                        $extra = 'id="status_option_'.$i.'"';
                        echo form_checkbox('status[]', $status_value, $checked, $extra);
                        echo '<label for="status_option_'.$i.'">'.$status_name.'</label>';
                        echo '</div>';
                    }
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
            <i class="red"><?php print $active_filters; ?></i>
        </div>        
        <div id="filters_div">
          <form id="search_form" method="post">
          <?php
            echo form_hidden($form['hidden']);
          ?>
          <div class="search_bar">
            <div class="col">
            <?php
              echo form_label(lang('hotcms_search') . lang('hotcms__colon'), 'keyword');
              echo form_input($form['keyword_input']);
            ?>
            </div>
              <!--
            <div class="col">
            <?php
              echo form_label(lang('hotcms_status') . lang('hotcms__colon'), 'status');
              echo form_dropdown('status', $form['status_options'], $filters['status']);
            ?>
            </div>
            <div class="col">
            <?php
              echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page']);
            ?>
            </div>
              -->
            <div class="col">
              <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_search' ); ?>" />
            </div>
          </div>
          </form>
        </div>
    </div>
<div class="clear"></div>
<?php if (!empty( $stores )) { ?>
<div class="table">
  <table id="tableCurrent" class="table_sorter">
    <thead>
      <tr>
        <th id="sortable_store_name" class="sortable<?php if ($filters['sort_by'] == 'store_name') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_location')?></th>
        <th id="sortable_store_num" class="sortable<?php if ($filters['sort_by'] == 'store_num') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_location_num')?></th>
        <th id="sortable_province_name" class="sortable<?php if ($filters['sort_by'] == 'province_name') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_province_state')?></th>
        <th id="sortable_city" class="sortable<?php if ($filters['sort_by'] == 'city') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_city')?></th>
        <th id="sortable_status" class="sortable<?php if ($filters['sort_by'] == 'status') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_status')?></th>
        <th id="sortable_create_timestamp" class="sortable<?php if ($filters['sort_by'] == 'create_timestamp') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_date_created')?></th>
        <th class="action"><?php echo lang('hotcms_edit')?></th>
        <th class="action"><?php echo lang('hotcms_delete')?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($stores as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->store_name; ?></td>
        <td><?php echo $row->store_num; ?></td>
        <td><?php echo $row->province_name; ?></td>
        <td><?php echo $row->city; ?></td>
        <td class="<?php if (empty( $row->status )){ echo lang('hotcms_no'); } else { echo lang('hotcms_yes'); } ?>">
          <?php
          switch ($row->status) {
            case 1:
              echo lang('hotcms_confirmed');
              break;
            case 2:
              echo lang('hotcms_closed');
              break;
            default:
              echo lang('hotcms_pending');
          }
          ?>
        </td>
        <td><?php echo $row->create_timestamp > 0 ? date('Y-m-d H:i:s', $row->create_timestamp) : '&mdash;'; ?></td>
        <td>
          <a href="<?php printf('/hotcms/%s/store_edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td class="last">
          <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/store_delete/%s/%s', $module_url, $row->retailer_id, $row->id)?>"><div class="btn-delete"></div></a>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post">
                    <?php
                    echo form_hidden($form['hidden']);
                    ?>        
                    <?php
                    $id = 'id="per_page_select"';
                    echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page'], $id);
                    ?>
                </form>
            </div>  
</div>
<?php
  }
  else {
    echo '<p>' . lang('hotcms__message__no_results') . '</p>';
  }
?>

</div>
