<div class="">
 <h2>Selected role: <?php echo $roleItem->name?></h2>
</div>
<div>
 <form action="/hotcms/<?php echo $module_url?>/add_role_permission/<?php echo $roleItem->id?>" method="post" accept-charset="UTF-8">
  <div class="row">       
   <?php echo form_error('permission', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_permission' ).' '.lang( 'hotcms__colon' ), 'permission');?>
   <?php echo form_input($permission_input); ?>
  </div>
  <div class="row">       
   <?php echo form_error('description_permission', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'description_permission');?>
   <?php echo form_input($description_permission_input); ?>
  </div>   
  <div class="submit">
    <input type="submit" class="submit" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/edit/<?php echo $roleItem->id?>" class="button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
