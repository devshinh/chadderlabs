<div>
  <form action="/hotcms/<?=$module_url?>/type_edit/<?=$currentItem->id?>" method="post">
  <div class="row">
  <?php
    echo form_error('name', '<div class="error">', '</div>');
    echo form_label(lang( 'hotcms_name' ) . " " . lang( 'hotcms__colon' ), 'name');
    echo form_input($form['name_input']);
  ?>
  </div>

  <div class="submit">
    <input type="submit" class="red_button" value="<?=lang( 'hotcms_save_changes' )?>" />
    <a href="/hotcms/<?=$module_url?>/types" class="red_button"><?=lang('hotcms_back')?></a>
    <a onClick="return confirmDelete()" href="/hotcms/<?=$module_url?>/type_delete/<?=$currentItem->id?>" class="red_button"><?=lang( 'hotcms_delete' )?></a>
    <?php echo form_hidden('hdnMode', 'edit') ?>
  </div>
  </form>
    
</div>