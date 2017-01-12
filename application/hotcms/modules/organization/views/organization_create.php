<div>
 <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label('<span class="red">*</span> '.lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($name_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('email', '<div class="error">','</div>');?>
   <?php echo form_label('<span class="red">*</span> '.lang( 'hotcms_email' ).' '.lang( 'hotcms__colon' ), 'email');?>
   <?php echo form_input($email_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('phone', '<div class="error">','</div>');?>
   <?php echo form_label('<span class="red">*</span> '.lang( 'hotcms_phone' ).' '.lang( 'hotcms__colon' ), 'phone');?>
   <?php echo form_input($phone_input); ?>
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
