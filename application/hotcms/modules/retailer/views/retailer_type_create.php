<div>
  <form action="/hotcms/<?=$module_url?>/type_create" method="post" accept-charset="UTF-8">
  <div class="row">
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang('hotcms_name').' '.lang('hotcms__colon'), 'name');?>
   <?php echo form_input($form['name_input']); ?>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?=$module_url?>/types/<?=$index_page_num?>" class="red_button"><?=lang( 'hotcms_back' )?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
