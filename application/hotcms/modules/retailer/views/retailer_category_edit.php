<div>
  <form action="/hotcms/<?php echo $module_url?>/category_edit/<?php echo $currentItem->id ?>" method="post">
  <div class="row">
  <?php
    echo form_error('name', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_name' ) . ' ' . lang( 'hotcms__colon' ), 'name');
    echo form_input($form['name_input']);
  ?>
  </div>

  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save_changes' ) ?>" />
    <a href="/hotcms/<?php echo $module_url?>/categories" class="red_button"><?php echo lang('hotcms_back') ?></a>
    <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/category_delete/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang( 'hotcms_delete' ) ?></a>
    <?php echo form_hidden('hdnMode', 'edit') ?>
  </div>
  </form>
    
</div>