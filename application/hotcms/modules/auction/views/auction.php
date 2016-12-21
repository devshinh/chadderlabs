<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<?php if (!empty( $items_array )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_status' )?></th>
        <th><?php echo lang( 'hotcms_opening_time' )?></th>        
        <th><?php echo lang( 'hotcms_closing_time' )?></th>        
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th><?php echo lang( 'hotcms_auction_bids' )?></th>
        <th class="action"><?php echo lang( 'hotcms_edit' )?></th>
        <th class="action"><?php echo lang( 'hotcms_delete' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($items_array as $row){ ?>
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
         <?php echo $row->opening_time ?>
        </td>
        <td>
         <?php echo $row->closing_time ?>
        </td>        
        <td>
         <?php if (!empty( $row->create_date )){ echo $row->create_date; }else{ ?>&mdash;<?php } ?>
        </td>          
        <td>
            <a href="<?php printf('/hotcms/%s/bids/%s', $module_url, $row->id)?>">Bids</a>
        </td>
        <td>
           <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
        </td>
        <td class="last">
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $row->id)?>"><div class="btn-delete"></div></a>
        </td>         
      </tr>
  <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
</div>
