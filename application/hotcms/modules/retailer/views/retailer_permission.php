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
                <div class="filter_header closed">Filter by Country
                        <div class="filter_header_arrow"></div>
                </div>
                <div class="row filter_options closed">
                    <?php
                    $i=0;
                    foreach ($form['country_code_options'] as $code_value => $country_name) {
                        $i++;
                        echo '<div>';
                        if (is_array($filters['country'])) {
                            $checked = in_array($code_value, $filters['country']);
                        } else {
                            $checked = false;
                        }
                        $extra = 'id="country_option_'.$i.'"';
                        echo form_checkbox('country_code[]', $code_value, $checked, $extra);
                        echo '<label for="country_option_'.$i.'">'.$country_name.'</label>';
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
    
<?php if (!empty( $retailers )){ ?>
<div class="table">
  <table id="tableCurrent" class="table_sorter">
    <thead>
      <tr>
        <th id="sortable_name" class="sortable<?php if ($filters['sort_by'] == 'name') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_retailer')?></th>
        <th id="sortable_country" class="sortable<?php if ($filters['sort_by'] == 'country') { echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp'); } ?>"><?php echo lang('hotcms_country')?></th>
        <th id="sortable_stores" class=""><?php echo lang('hotcms_number_of_locations')?></th>
        <th id="sortable_users" class=""><?php echo lang('hotcms_number_of_users')?></th>
        <th class="action">Permissions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($retailers as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->country; ?></td>
        <td><?php echo $row->stores; ?></td>
        <td><?php echo $row->users; ?></td>
        <td class="last">
        <?php
          //echo form_dropdown('access', $form['access_options'], $row->access_id,  'id="' . $row->id . '" class="retailer_access_selector"');
        ?>
            
       <?php 
       //var_dump($row->per);
       //var_dump($row->per_chec);
       if (isset($row->per_chec) && count($row->per_chec) > 0) { ?>
        <div>
          <?php
          foreach ($row->per_chec as $permission){
              
            echo '<div class="checkbox">';
            echo form_checkbox($permission);
            echo form_label($permission["id"], $permission["value"]);
            echo '</div>';
          }
          ?>
        </div>
        <?php } ?>
            
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <div id="pagination"><?php echo $pagination; ?></div>
</div>
<?php
  }
  else {
    echo '<p>' . lang('hotcms__message__no_results') . '</p>';
  }
?>
</div>
