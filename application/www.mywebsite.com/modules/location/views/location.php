<div class="module">
 <h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
 <?php
 if (!empty( $current )){ ?>
 <div class="table">   
   <table id="tableCurrent" class="tablesorter">
     <thead>
       <tr>
         <th><?php echo lang( 'hotcms_location_name' ) ?></th>
         <th><?php echo lang( 'hotcms_number_of_users' ) ?></th>
         <th><?php echo lang('hotcms_date_updated') ?></th>
         <th><?php echo lang('hotcms_date_created') ?></th>         
         <th class="action"><?php echo lang('hotcms_edit') ?></th>
         <th class="action"><?php echo lang('hotcms_delete') ?></th>
       </tr>
     </thead>
     <tbody>
   <?php foreach ($current as $row){ ?>
       <tr id="trData_<?php echo $row->id ?>">
         <td><?php echo $row->name ?></td>
         <td><?php echo $row->users ?></td>
         <td><?php echo (empty($row->update_date)?'&mdash;':date($this->config->item('timestamp_format'),$row->update_timestamp)); ?></td>
         <td><?php echo date($this->config->item('timestamp_format'),$row->create_timestamp) ?></td>
         <td>
            <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $row->id)?>"><div class="btn-edit"></div></a>
         </td>
         <td class="last">
            <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s' ,$module_url, $row->id)?>"><div class="btn-delete"></div></a>
         </td>
       </tr>
   <?php } ?>
     </tbody>
   </table>
 </div>
 <?php } ?>
</div>