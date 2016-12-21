<div class="module">
    <h2><a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
    <div class="row">
        <div id="filters_div">
            <form id="search_form" method="post">
                <?php
                echo form_hidden($form['hidden']);
                ?>
                <div class="search_bar">
                    <?php
                    echo form_label(lang('hotcms_search') . lang('hotcms__colon'), 'keyword');
                    echo form_input($form['keyword_input']);
                    echo form_dropdown('search_options', $form['keyword_column_options'], $filters['keyword_column']);
                    ?>
                    <input id='search_button'type="submit" class="red_button" value="<?php echo lang('hotcms_search'); ?>" />

                </div>
            </form>
        </div>
    </div>    
    <?php if (!empty($aCurrent)) { ?>
        <div class="table">
            <table id="tableCurrent" class="table_sorter">
                <thead>
                    <tr>
                        <th id="sortable_first_name" class="sortable<?php
                        if ($filters['sort_by'] == 'first_name') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_name_first') ?></th>    
                        <th id="sortable_last_name" class="sortable<?php
                        if ($filters['sort_by'] == 'last_name') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_name_last') ?></th>                            
                        <th id="sortable_screen_name" class="sortable<?php
                        if ($filters['sort_by'] == 'screen_name') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_screen_name') ?></th>                       
                        <th id="sortable_email" class="sortable<?php
                        if ($filters['sort_by'] == 'email') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_email_address') ?></th>
                        <th id="sortable_status" class="sortable<?php
                        if ($filters['sort_by'] == 'status') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_status') ?></th>      
                        <th id="sortable_last_login" class="sortable<?php
                        if ($filters['sort_by'] == 'last_login') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_last_login') ?></th>      
                        <th id="sortable_created_on" class="sortable<?php
                        if ($filters['sort_by'] == 'created_on') {
                            echo ($filters['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
                        }
                        ?>"><?php echo lang('hotcms_date_created') ?></th>                         
                        <th class="action"><?php echo lang('hotcms_edit') ?></th>
                        <th class="action"><?php echo lang('hotcms_delete') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aCurrent as $row) { ?>
                        <tr id="trData_<?php echo $row->user_id ?>">
                            <td>
                                <?php echo $row->first_name ?>
                            </td>
                            <td>
                                <?php echo $row->last_name ?>
                            </td>
                            <td>
                                <?php echo $row->screen_name ?>
                            </td>                            
                            <td>
                                <?php if (!empty($row->email)) { ?><?php echo $row->email ?><a href="mailto:<?php echo $row->email ?>"><img class="link" src="asset/images/icon_link-out.png" alt="" /></a><?php } else { ?>&mdash;<?php } ?>
                            </td>
                            <td>
                                <?php
                                if ($row->active == 1) {
                                    printf('<a href="/hotcms/%s/deactivate/%d">%s</a>', $module_url, $row->user_id, lang('hotcms_active'));
                                } else {
                                    printf('<a href="/hotcms/%s/activate/%d/%s">%s</a>', $module_url, $row->user_id, $row->activation_code, lang('hotcms_inactive'));
                                }
                                ?>
                            </td>
                            <td><?php echo $row->last_login > 0 ? date('Y-m-d H:i:s', $row->last_login) : '&mdash;'; ?></td>
                            <td><?php echo $row->created_on > 0 ? date('Y-m-d H:i:s', $row->created_on) : '&mdash;'; ?></td>

                            <td>
                                <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->user_id) ?>"><div class="btn-edit"></div></a>
                            </td>
                            <td class="last">
                                <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->user_id) ?>"><div class="btn-delete"></div></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div id="pagination"><?php echo $pagination; ?></div>
            <div id="pagination_items">
                <form id="pagination_form" method="post" action="<?php echo $module_url ?>/index">
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
    <?php } ?>
</div>
