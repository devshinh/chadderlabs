<div>
   <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $current_item->id ?>" method="post">     
    <div class="input">
        
        <div class="row">  
         <?php echo form_error('name', '<div class="error">','</div>');?>
         <?php echo form_label(lang( 'hotcms_name' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'name');?>
         <?php echo form_input($form['name_input']); ?>
        </div>
        <div class="row">
         <?php echo form_label(lang( 'hotcms_opening_time' ).'<span class="red">*</span> <br />2012-12-12 17:08:12'.lang( 'hotcms__colon' ), 'opening_time');?>
         <?php echo form_input($form['opening_time_input']); ?>         
        </div>     
        <div class="row">
         <?php echo form_label(lang( 'hotcms_closing_time' ).'<span class="red">*</span> '.lang( 'hotcms__colon' ), 'closing_time');?>
         <?php echo form_input($form['closing_time_input']); ?>         
        </div>      
       <div class="row">
         <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'postal_code');?>
         <?php echo form_checkbox($form['active_input']); ?> 
       </div>       
    </div>
     <div class="submit">
       <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
       <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>

       <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $current_item->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>

       <input type="hidden" id="hdnPassword" value="<?php echo set_value( 'hdnPassword', $this->input->post( 'hdnPassword' ) ) ?>" />
     </div>
    </form>
   <div class="clear"></div>
  </div> <!-- .tabs -->
</div>
