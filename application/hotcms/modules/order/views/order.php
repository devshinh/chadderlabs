<div class="module">
    <div class="row">
        <div class="control_buttons">
            <a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a>
            <!--<a class="red_button" id="filter_button"><?php echo lang('hotcms_filter'); ?></a>-->
        </div>
<!--        <div id="filters-modal" class="ui-dialog" title="Filters">
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
            <i class="red"><?php print $active_filters ?></i>
        </div>-->
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
    <?php if (!empty($orders)) { ?>
        <div class="table">
            <table id="tableCurrent" class="table_sorter">
                <thead>
                    <tr>
                        <th id="sortable_id" class="sortable<?php
    if ($filters['sort_by'] == 'id') {
        echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }
        ?>"><?php echo "Order #" ?></th>
                        
            
                <th id="sortable_status" class="sortable<?php
                        if ($filters['sort_by'] == 'status') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
        ?>"><?php echo lang('hotcms_status') ?></th>
                <th class="filter">
                <div class="filter_icon <?php if ($filters['status'] != '') echo 'active'; ?>" id="status"></div></th> 
            <th><?php echo lang('hotcms_screen_name') ?></th>
            <th><?php echo lang('hotcms_name_first') ?></th>
            <th><?php echo lang('hotcms_name_last') ?></th>            
            <th><?php echo lang('hotcms_email') ?></th>
                <th id="sortable_create_timestamp" class="sortable<?php
                if ($filters['sort_by'] == 'create_timestamp') {
                    echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                }
        ?>"><?php echo lang('hotcms_date_created') ?></th>
                <th class="action"><?php echo lang('hotcms_edit') ?></th>
                <th class="action"><?php echo lang('hotcms_delete') ?></th>
                </tr>
                </thead>
                <tbody><pre>
                    <?php 
                    foreach ($orders as $row) {?>
                        <tr id="trData_<?php echo $row->id ?>">
                            <td><?php echo $row->id; ?></td>

                            <td colspan="2" class="">
                                    <?php
                                    echo (ucfirst($row->order_status));
                                    ?>
                            </td>
                            <td><?php echo $row->user_info->screen_name; ?></td>
                            <td><?php echo $row->user_info->first_name; ?></td>
                            <td><?php echo $row->user_info->last_name; ?></td>
                            <td><?php echo $row->user_info->email; ?></td>
                            <td>
                                <?php echo $row->create_timestamp > 0 ? date('Y-m-d H:i:s', $row->create_timestamp) : '&mdash;'; ?>
                            </td>
                            <td>
                                <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id) ?>"><div class="btn-edit"></div></a>
                            </td>
                            <td class="last">
                                <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id) ?>"><div class="btn-delete"></div></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>    
            <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post" action="<?php echo $module_url?>/index">
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
    } else {
        echo '<p>' . lang('hotcms__message__no_results') . '</p>';
    }
    ?>
</div>
