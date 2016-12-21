<div>
   <form action="/hotcms/<?php echo $module_url?>/edit/<?php echo $currentItem->id ?>" method="post">
    <div class="input">
     <div class="row">  
      <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name');?>
      <?php echo form_input($form['name_input']); ?>
     </div>
     <div class="row">
      <?php echo form_label(lang( 'hotcms_url' ).' '.lang( 'hotcms__colon' ), 'url');?>
      <?php echo form_input($form['url_input']); ?>         
     </div>
     <div class="row">
      <?php echo form_label('Upload path '.lang( 'hotcms__colon' ), 'url');?>
      <?php echo form_input($form['path_input']); ?>         
     </div>   
     <div class="row">
      <?php echo form_label('Theme '.lang( 'hotcms__colon' ), 'url');?>
      <?php echo form_input($form['theme_input']); ?>         
     </div>             
     <div class="row">
      <?php echo form_label(lang( 'hotcms_primary_site' ).' '.lang( 'hotcms__colon' ), 'primary');?>
      <?php echo form_checkbox($form['primary_input']); ?> 
     </div>
     <div class="row">
      <?php echo form_label(lang( 'hotcms_active' ).' '.lang( 'hotcms__colon' ), 'postal_code');?>
      <?php echo form_checkbox($form['active_input']); ?> 
     </div>       
    </div>
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
      <a href="/hotcms/<?php echo $module_url?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
      
      <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
      
      <?php echo form_hidden('hdnMode', 'edit') ?>
    </div>
  </form>
</div>