<?php if (!empty( $sites )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_url' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_hidden' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th class="action"><?php echo lang('hotcms_edit') ?></th>
        <th class="action"><?php echo lang('hotcms_delete') ?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($sites as $row){ ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td>
         <?php echo $row->name ?>
        </td>
        <td>
         <?php echo $row->domain ?>
        <td class="<?php if (empty( $row->active )){ ?>no<?php } else { ?>yes<?php } ?>">
          <?php if (empty( $row->active )){ ?>INACTIVE<?php }else{ ?>ACTIVE<?php } ?>
        </td>
        <td class="<?php if (empty( $row->hidden )){ ?>no<?php } else { ?>yes<?php } ?>">
          <?php if (empty( $row->hidden )){ ?><?php }else{ ?>HIDDEN<?php } ?>
        </td>        
        <td>
         <?php if (!empty( $row->update_timestamp )){ echo date($this->config->item('timestamp_format'), $row->update_timestamp); }else{ ?>&mdash;<?php } ?>
        </td>   
        <td>
         <?php if (!empty( $row->create_timestamp )){ echo date($this->config->item('timestamp_format'), $row->create_timestamp); }else{ ?>&mdash;<?php } ?>
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
</div>
<?php }else{ ?>
<div>No points history.</div>
<?php } ?>