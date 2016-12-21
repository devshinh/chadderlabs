<div>
<?php if (!empty( $items )) { ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th class="title"><?php echo lang( 'hotcms_title' )?></th>
        
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_author' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_edit' )?></th>
        <th><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($items as $row) { ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td class="title"><?php echo $row->title ?></td>
        
        <td><?php echo $row->create_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->create_timestamp) : '&mdash;'; ?></td>
        <td><?php echo $row->update_timestamp > 0 ? date($this->config->item('timestamp_format'), $row->update_timestamp) : '&mdash;'; ?></td>
        <td><?php echo $row->username ?></td>
        <td class="<?php if ($row->status == 1) { echo lang( 'hotcms_yes' ); } else { echo lang( 'hotcms_no' ); } ?>">
        <?php
        switch ($row->status) {
          case 0: echo lang( 'hotcms_inactive' );
            break;
          case 1: echo lang( 'hotcms_active' );
            break;
        }
        ?>
        </td>
        <td>
          <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td  class="last">
          <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php }
  else {
    echo 'No items were found.';
  }
?>
</div>
