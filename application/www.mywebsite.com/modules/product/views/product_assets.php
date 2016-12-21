<div id="mediaLib" class="table">
 <table>
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th><?php echo lang( 'hotcms_name' ) ?></th>
        <th><?php echo lang( 'hotcms_actions' ) ?></th>
      </tr>
    </thead>
    <tbody>  
   <?php foreach ($assets as $row){ ?>
   <tr id="trData_<?php echo $row->id ?>">
   <td>
   <?php    
    printf('<img src="%s%s/%s.%s" alt="%s" />', $this->config->item( 'base_url_front' ) ,'/asset/upload/image/auction_product/thumbnail_50x50/',$row->file_name.'_thumb', $row->extension, $row->file_name); 
   ?>
   </td>
   <td>
    <?php echo $row->name ?>
   </td>
   <td>
    <?php 
      printf('<a href="/hotcms/%s/add_image_asset/%s/%s"><img src="asset/images/actions/add.png" alt="Add image" /></a>',$module_url,$row->id,$current_item->id ); 
    ?>         
   </td>
   </tr>
   <?php } ?>
   </table>
</div>