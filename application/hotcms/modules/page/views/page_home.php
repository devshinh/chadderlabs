<h2>Recent Activity</h2>
<div class="module">
  <?php if (!empty($aCurrent)) { ?>
    <div class="table">
      <table id="tableCurrent" class="tablesorter">
        <thead>
          <tr>
            <th><?php echo lang('hotcms_name') ?></th>
            <th><?php echo lang('hotcms_status') ?></th>
            <th><?php echo lang('hotcms_date_updated') ?></th>
            <th><?php echo lang('hotcms_date_created') ?></th>
            <th class="action last"><?php echo lang('hotcms_edit') ?></th>
            <!-- th class="action"><?php echo lang('hotcms_delete') ?></th -->
          </tr>
        </thead>
        <tbody>
          <?php foreach ($aCurrent as $row) { ?>
            <tr id="trData_<?php echo $row->id ?>">
              <td>
<a href="<?php printf('/hotcms/%s/edit/%s/reset', $module_url, $row->id) ?>">
                <?php echo $row->name ?>
</a>
              </td>
              <td class="<?php if (empty($row->status)) { ?><?php echo lang('hotcms_no') ?><?php } else { ?><?php echo lang('hotcms_yes') ?><?php } ?>">
                <?php
                if ($row->status == 1) {
                  echo "Published";
                } elseif ($row->status == 2) {
                  echo "Archived";
                } else {
                  echo "Draft";
                }
                ?>
              </td>
              <td>
                <?php echo ($row->update_timestamp > 0 ? date('Y-m-d H:i:s ', $row->update_timestamp) : '&mdash;'); ?>
              </td>
              <td>
               <?php echo ($row->create_timestamp > 0 ? date('Y-m-d H:i:s ', $row->create_timestamp) : '&mdash;'); ?>
              </td>
              <td class="last">
                <a href="<?php printf('/hotcms/%s/edit/%s/reset', $module_url, $row->id) ?>"><div class="btn-edit"></div></a>
              </td>
              <!-- td class="last">
                <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id) ?>"><div class="btn-delete"></div></a>
              </td -->
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
