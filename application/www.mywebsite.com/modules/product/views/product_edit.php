<div>
   <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $current_item->id ?>" method="post">
    <div class="row">       
     <?php echo form_error('name', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
     <?php echo form_input($form['name_input']); ?>
    </div>
    <div class="row">          
     <?php echo form_label(lang( 'hotcms_summary' ).' '.lang( 'hotcms__colon' ), 'short_description');?>
     <?php echo form_textarea($form['short_description_input']); ?>                     
    </div>       
    <div class="row">          
     <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'description');?>
     <?php echo form_textarea($form['description_input']); ?>                     
    </div>   
    <div class="row">         
     <?php echo form_error('category', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_category' ).' '.lang( 'hotcms__colon' ), 'category');?>
     <?php echo form_dropdown('category', $form['categories'], $current_item->category_id); ?>                     
    </div>   
    <div class="row">       
     <?php echo form_error('minimum_bid', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_minimum_bid' ).' '.lang( 'hotcms__colon' ), 'minimum_bid');?>
     <?php echo form_input($form['minimum_bid_input']); ?>
    </div>
    <div class="row">       
     <?php echo form_error('minimum_increment', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_bid_increment' ).' '.lang( 'hotcms__colon' ), 'minimum_increment');?>
     <?php echo form_input($form['minimum_increment_input']); ?>
    </div>
    <div class="row">       
     <?php echo form_error('opening_time', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_opening_time' ).' (2011-12-16 10:59:43)'.lang( 'hotcms__colon' ), 'opening_time');?>
     <?php echo form_input($form['opening_time_input']); ?>
    </div>
    <div class="row">       
     <?php echo form_error('closing_time', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_closing_time' ).' '.lang( 'hotcms__colon' ), 'closing_time');?>
     <?php echo form_input($form['closing_time_input']); ?>
    </div>      
    <div class="row assets">         
     <a id="addAsset" class="red_button" >Add image</a>
        <?php
     if(!empty($assets)) {
      $data['assets']= $assets;
      $this->load->view('product_assets', $data);
     }?>
     <h3>Images</h3>
     <div class="table">
      <table class="groupWrapper">
       <thead>
         <tr>
           <th>&nbsp;</th>
           <th><?php echo lang( 'hotcms_name' ) ?></th>
           <th><?php echo lang( 'hotcms_actions' ) ?></th>
         </tr>
       </thead>       
       <?php 
       foreach ($product_assets as $asset){
        $delete = sprintf('<a onClick="return confirmDelete()" href="/hotcms/%s/delete_asset/%s/%s"><img src="asset/images/actions/delete.png" alt="Delete" /></a>', $module_url, $asset->a_id, $asset->item_id);
        $img = sprintf('<td class="itemHeader"><img src="%s%s/%s.%s" alt="%s" />', $this->config->item( 'base_url_front' ) ,'/asset/upload/image/auction_product/thumbnail_50x50/',$asset->file_name.'_thumb', $asset->extension, $asset->file_name );
        $name = sprintf('<td>%s</td>', $asset->name); 
        printf('<tr id="%s" class="groupItem">%s %s<td>%s</td></tr>', $asset->a_id, $img, $name, $delete);
       }?>
      </table>
     </div>
    </div>         
    <div class="row">
      <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'active');?>
      <?php echo form_checkbox($form['active_input']); ?> 
    </div>    
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $current_item->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
</div>