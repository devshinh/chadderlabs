<?php $moduleConfig = $this->config->item('location');?>
<h2><a href="/hotcms/location/create">Create <?php echo $moduleConfig['module_url']?></a></h2>
<?php
if (!empty( $aCurrent )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' ) ?></th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($aCurrent as $row){ ?>
      <tr id="trData_<?php echo $row->nLocationID ?>">
        <td><?php echo $row->sName ?></td>
        <td>
           <a href="<?php printf('location/edit/%s',$row->nLocationID)?>"><img src="asset/images/actions/edit.png" alt="Edit" /></a>
           <a onClick="return confirmDelete()" href="<?php printf('location/delete/%s',$row->nLocationID)?>"><img src="asset/images/actions/delete.png" alt="Delete" /></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
