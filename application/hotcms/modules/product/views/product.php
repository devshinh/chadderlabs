<div class="module">
    <h2><a class="red_button" href="<?php printf('/hotcms/%s', $module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
    <?php if (!empty($items_array)) { ?>
        <div class="table">
            <table id="tableCurrent" class="tablesorter groupWrapperProducts">
                <thead>
                    <tr>
                        <th><?php echo lang('hotcms_name') ?></th>
                        <th><?php echo lang('hotcms_status') ?></th>
                        <th><?php echo lang('hotcms_date_updated') ?></th>
                        <th><?php echo lang('hotcms_date_created') ?></th>
                        <th class="action"><?php echo lang('hotcms_edit') ?></th>
                        <th class="action"><?php echo lang('hotcms_delete') ?></th>
                    </tr>
                </thead>
                <tbody id="ui-sortable">
                    <?php foreach ($items_array as $row) { ?>
                        <tr id="<?php echo $row->id ?>" class="groupItem">
                            <td class="itemHeader">
                                <?php echo $row->name ?>
                            </td>
                            <td class="<?php if (empty($row->active)) { ?><?php echo lang('hotcms_no') ?><?php } else { ?><?php echo lang('hotcms_yes') ?><?php } ?>">
                                <?php
                                if (empty($row->active)) {
                                    echo lang('hotcms_inactive');
                                } else {
                                    echo lang('hotcms_active');
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo ($row->update_timestamp > 0 ? date('Y-m-d H:i:s ', $row->update_timestamp) : '&mdash;'); ?>
                            </td>
                            <td>
                                <?php echo ($row->create_timestamp > 0 ? date('Y-m-d H:i:s ', $row->create_timestamp) : '&mdash;'); ?>
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
            <div id="pagination">
                <?php echo $pagination; ?>
            </div>
        </div>
    <?php } ?>
</div>
