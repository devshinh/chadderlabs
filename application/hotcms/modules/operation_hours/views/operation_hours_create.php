<div>
 <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label('<span class="red">*</span> '.lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($name_input); ?>
  </div>
  <div class="row">         
   <?php echo form_error('short_description', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_summary' ).' '.lang( 'hotcms__colon' ), 'short_description');?>
   <?php echo form_textarea($short_description_input); ?>                     
  </div>   
  <div class="row">         
   <?php echo form_error('description', '<div class="error">','</div>');?>
   <?php echo form_label('<span class="red">*</span> '.lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'description');?>
   <?php echo form_textarea($description_input); ?>                     
  </div> 
  <div class="row">       
   <?php echo form_error('minimum_bid', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_minimum_bid' ).' '.lang( 'hotcms__colon' ), 'minimum_bid');?>
   <?php echo form_input($minimum_bid_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('minimum_increment', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_bid_increment' ).' '.lang( 'hotcms__colon' ), 'minimum_increment');?>
   <?php echo form_input($minimum_increment_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('opening_time', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_opening_time' ).' (2011-12-16 10:59:43)'.lang( 'hotcms__colon' ), 'opening_time');?>
   <?php echo form_input($opening_time_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('closing_time', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_closing_time' ).' '.lang( 'hotcms__colon' ), 'closing_time');?>
   <?php echo form_input($closing_time_input); ?>
  </div>  
  <div class="row">         
   <?php echo form_error('category', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_category' ).' '.lang( 'hotcms__colon' ), 'category');?>
   <?php echo form_dropdown('category', $categories, 1); ?>                     
  </div>   
  <div class="row">
    <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'active');?>
    <?php echo form_checkbox($active_input); ?> 
  </div>    
  <div class="submit">
    <input type="submit" class="input red_button"  value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
