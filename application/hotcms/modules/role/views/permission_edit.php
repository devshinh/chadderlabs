<div class="">
 <h2>Selected role: <?php echo $roleItem->name?></h2>
</div>
<div>
  <form action="<?php printf('/hotcms/%s/edit_permission/%d/%d', $module_url, $aCurrentItem->id, $role_id); ?>" method="post">
    <div class="input">
  <div class="row">       
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_permission' ).' '.lang( 'hotcms__colon' ), 'name');?>
   <?php echo form_input($form['permission_input']); ?>
  </div>
  <div class="row">       
   <?php echo form_error('main_email', '<div class="error">','</div>');?>
   <?php echo form_label(lang( 'hotcms_description' ).' '.lang( 'hotcms__colon' ), 'main_email');?>
   <?php echo form_input($form['description_permission_input']); ?>
  </div>  
          
    <div class="submit">
      <input type="submit" class="button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="<?php printf('/hotcms/%s/edit/%d/', $module_url, $role_id); ?>" class="button"><?php echo lang( 'hotcms_back' ) ?></a>
      
      <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete_permission/%d/%d', $module_url, $aCurrentItem->id, $role_id); ?>" class="button"><?php echo lang( 'hotcms_delete' ) ?></a>
      
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
  </div>
</div>  
