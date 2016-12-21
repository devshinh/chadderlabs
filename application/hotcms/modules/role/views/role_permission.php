<?php

if (!empty( $currentItemPermissions )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_description' ) ?></th>
        <th><?php echo lang( 'hotcms_actions' ) ?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($currentItemPermissions as $row){?>
      <tr id="trData_<?php echo $row->id ?>">
        <td><?php echo $row->description ?></td>
        <td>
           <a href="<?php printf('role/edit_permission/%s/%s', $row->id, $role_id)?>"><img src="asset/images/actions/edit.png" alt="Edit" /></a>
           <a onClick="return confirmDelete()" href="<?php printf('role/delete_permission/%s/%s',$row->id, $role_id)?>"><img src="asset/images/actions/delete.png" alt="Delete" /></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
