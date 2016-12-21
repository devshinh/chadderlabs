<div>
   <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $current_item->id ?>" method="post">
    <div class="row">       
     <?php echo form_error('name', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
     <?php echo form_input($form['name_input']); ?>
    </div>
<!--    <div class="row">          
     <?php //echo form_label(lang( 'hotcms_summary' ).' '.lang( 'hotcms__colon' ), 'short_description');?>
     <?php //echo form_textarea($form['short_description_input']); ?>                     
    </div>       
-->
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
     <?php echo form_error('price_input', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_price' ).' '.lang( 'hotcms__colon' ), 'price');?>
     <?php echo form_input($form['price_input']); ?>
    </div>   
    <div class="row">       
     <?php echo form_error('stock_input', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_stock' ).' '.lang( 'hotcms__colon' ), 'stock');?>
     <?php echo form_input($form['stock_input']); ?>
    </div>
    <div class="row">       
     <?php echo form_error('featured_image_id', '<div class="error">','</div>');?>
     <?php echo form_label(lang( 'hotcms_featured_image_id' ).' '.lang( 'hotcms__colon' ), 'featured_image_id');?>
     <?php echo form_input($form['featured_image_id_input']); ?>
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