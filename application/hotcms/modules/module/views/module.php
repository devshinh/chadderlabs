<div class="module">
<h2><a href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<?php if (!empty( $aCurrent )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th><?php echo lang( 'hotcms_actions' )?></th>
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
         <?php if (!empty( $row->update_date )){ echo $row->update_date; }else{ ?>&mdash;<?php } ?>
        </td>   
        <td>
         <?php if (!empty( $row->create_date )){ echo $row->create_date; }else{ ?>&mdash;<?php } ?>
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
