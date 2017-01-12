<div>
 <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($name_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('description', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'title');?>
   <?php echo form_input($description_input); ?>
  </div> 
  <div class="row">
    <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'active');?>
    <?php echo form_checkbox($active_input); ?> 
  </div>    
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
