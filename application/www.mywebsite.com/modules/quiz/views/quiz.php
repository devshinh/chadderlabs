<div class="tabs">
  <ul>
    <li><a href="#quiz-list" id="general"><span id="g"></span><span>Info</span></a></li>
    <li><a href="#quiz-setting" id="setting"><span id="m"></span><span>Settings</span></a></li>
  </ul>
  <div class="module" id="quiz-list">
    <?php if (!empty($items)) { ?>
      <div class="table">
        <table id="tableCurrent" class="tablesorter">
          <thead>
            <tr>
              <th class="title"><?php echo lang('hotcms_title') ?></th>
              <th><?php echo lang('hotcms_type') ?></th>
              <th><?php echo lang('hotcms_date_created') ?></th>
              <th><?php echo lang('hotcms_date_updated') ?></th>
              <th><?php echo lang('hotcms_author') ?></th>
              <th><?php echo lang('hotcms_status') ?></th>
              <th class="action"><?php echo lang('hotcms_edit') ?></th>
              <th class="action"><?php echo lang('hotcms_delete') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $row) { ?>
              <tr id="trData_<?php echo $row->id ?>">
                <td class="title"><?php echo $row->name ?></td>
                <td><?php echo $row->typename ?></td>
                <td><?php echo $row->create_timestamp > 0 ? date('Y-m-d H:i:s', $row->create_timestamp) : '&mdash;'; ?></td>
                <td><?php echo $row->update_timestamp > 0 ? date('Y-m-d H:i:s', $row->update_timestamp) : '&mdash;'; ?></td>
                <td><?php echo $row->username ?></td>
                <td class="<?php if ($row->status == 1) {
            echo lang('hotcms_yes');
          } else {
            echo lang('hotcms_no');
          } ?>">
                  <?php
                  switch ($row->status) {
                    case 0: echo lang('hotcms_inactive');
                      break;
                    case 1: echo lang('hotcms_active');
                      break;
                  }
                  ?>
                </td>
                <td>
                  <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id) ?>"><div class="btn-edit"></div></a>
                </td>
                <td  class="last">
                  <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id) ?>"><div class="btn-delete"></div></a>
                </td>
              </tr>
      <?php } ?>
          </tbody>
        </table>
      </div>
    <?php
    } else {
      echo 'No items were found.';
    }
    ?>
  </div>
  <div id="quiz-setting">
    <?php print $settings ?>
  </div>
</div>
