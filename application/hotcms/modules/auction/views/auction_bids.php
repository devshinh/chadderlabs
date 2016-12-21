<div class="module">
   <h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>"><?php printf('%s', 'Back') ?></a></h2>
   <h2><?php echo $module_header; ?></h2>
<?php if (!empty( $items_array )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_user' )?></th>
        <th><?php echo lang( 'hotcms_amount' )?></th>        
        <th><?php echo lang( 'hotcms_highest' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
        
      </tr>
    </thead>
    <tbody>
  <?php foreach ($items_array as $row){ ?>
      <tr id="trData_<?php echo $row->id ?>">
        <td>
         <?php echo $row->name ?>
        </td>
        <td>
            <?php echo $row->first_name .' '.$row->last_name ?>
        </td>
        <td>
         <?php echo $row->amount ?>
        </td>
        <td style="text-align:center">
         <?php
          echo ($row->highest==1?'<div class="yes">Yes</div>':'<div class="no">No</div>'); 
        // echo ($row->highest==1?'Yes':'No'); 
         ?>
        </td>        
        <td>
         <?php echo $row->create_date ?>
        </td>        
        <td class="last">
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_bid/%s/%s', $module_url, $row->id,$row->auction_id)?>"><div class="btn-delete"></div></a>
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
