<div class="module">
    <?php if (!empty($targets)) { ?>
        <div class="table">
            <table id="tableCurrent" class="table_sorter">
                <thead>
                    <tr>
                      <th id="sortable_name" class="sortable<?php
                        if (strcasecmp($filters['sort_by'], 'name') === 0) {
                            echo ((strcasecmp($filters['sort_direction'], 'desc') === 0) ? ' headerSortDown' : ' headerSortUp');
                        }
                      ?>"><?=lang("hotcms_name")?></th>
                      <th id="sortable_organization" class="sortable<?php
                        if (strcasecmp($filters['sort_by'], 'site') === 0) {
                            echo ((strcasecmp($filters['sort_direction'], 'desc') === 0) ? ' headerSortDown' : ' headerSortUp');
                        }
                      ?>"><?=lang("hotcms_site")?></th>
                      <th id="sortable_description" class="sortable<?php
                        if (strcasecmp($filters['sort_by'], "description") === 0) {
                            echo ((strcasecmp($filters['sort_direction'], 'desc') === 0) ? ' headerSortDown' : ' headerSortUp');
                        }
                      ?>"><?=lang("hotcms_description")?></th>
                      <th id="sortable_update_timestamp" class="sortable<?php
                        if (strcasecmp($filters['sort_by'], 'update_timestamp') === 0) {
                            echo ((strcasecmp($filters['sort_direction'], 'desc') === 0) ? ' headerSortDown' : ' headerSortUp');
                        }
                      ?>"><?=lang('hotcms_date_updated')?></th>
                    <th class="action"><?=lang('hotcms_edit')?></th>
                    <th class="action"><?=lang('hotcms_copy')?></th>
                    <th class="action"><?=lang('hotcms_delete')?></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($targets as $row) {?>
                        <tr id="trData_<?=$row->id?>">
                            <td><?=$row->name?></td>
                            <td><?=$row->site?></td>
                            <td><?=$row->description?></td>
                            <td>
                                <?php echo $row->update_timestamp > 0 ? date('Y-m-d H:i:s', $row->update_timestamp) : '&mdash;'; ?>
                            </td>
                            <td>
                                <a href="<?="/hotcms/".$module_url."/edit/".$row->id?>"><div class="btn-edit"></div></a>
                            </td>
                            <td>
                              <a class="red_button" href="<?="/hotcms/".$module_url."/duplicate/".$row->id?>"><?=lang("hotcms_copy")?></a>
                            </td>
                            <td class="last">
                                <a onClick="return confirmDelete()" href="<?="/hotcms/".$module_url."/delete/".$row->id?>"><div class="btn-delete"></div></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>    
            <div id="pagination"><?=$pagination?></div>
            <div id="pagination_items">
              <?php 
                echo form_open($module_url."/index", array("id" => "pagination_form"));
/*                <form id="pagination_form" method="post" action="<?=$module_url?>/index">*/
                echo form_hidden($form['hidden']);
                $id = 'id="per_page_select"';
                echo form_dropdown('per_page', $form['per_page_options'], $filters['per_page'], $id);
                echo form_close();
              ?>
            </div>
        </div>
        <?php
    } else {
        echo "<p>" . lang('hotcms__message__no_results') . "</p>";
    }
    ?>
</div>
