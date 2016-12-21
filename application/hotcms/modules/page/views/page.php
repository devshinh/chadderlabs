<div class="module">
<h2><a href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<h2><a id="save_menu_layout"><?php echo lang( 'hotcms_save_changes' ) ?></a></h2>
<h2><a onclick="window.location.reload()"><?php echo lang( 'hotcms_reset' ); ?></a></h2>
<?php if (!empty( $aCurrent )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th class="action"><?php echo lang( 'hotcms_actions' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($aCurrent as $row){ ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td>
         <?php echo $row->name ?>
        </td>
        <td class="<?php if (empty( $row->active )){ ?><?php echo lang( 'hotcms_no' ) ?><?php } else { ?><?php echo lang( 'hotcms_yes' ) ?><?php } ?>">
          <?php if (empty( $row->active )){
               echo lang( 'hotcms_inactive' );
             } else{
               echo lang( 'hotcms_active' );
             } ?>
        </td>
        <td>
         <?php echo ($row->update_timestamp > 0 ? date('Y-m-d H:i:s ', $row->update_timestamp) : '&mdash;'); ?>
        </td>
        <td>
         <?php echo ($row->create_timestamp > 0 ? date('Y-m-d H:i:s ', $row->create_timestamp) : '&mdash;'); ?>
        </td>
        <td>
           <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><img src="asset/images/actions/edit.png" alt="Edit" /></a>
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><img src="asset/images/actions/delete.png" alt="Delete" /></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
</div>
