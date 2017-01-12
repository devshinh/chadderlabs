<div>
  <form action="/hotcms/<?php echo $module_url?>/create" method="post" accept-charset="UTF-8">
  <div class="row">
   <?php echo form_error('name', '<div class="error">','</div>');?>
   <?php echo form_label(lang('hotcms_name').' '.lang('hotcms__colon'), 'name');?>
   <?php echo form_input($form['name_input']); ?>
  </div>
  <div class="row">
  <?php
    echo form_error('country_code', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_country') . ' ' . lang('hotcms__colon'), 'country_code');
    echo form_dropdown('country_code', $form['country_code_options'], $selected_country);
  ?>
  </div>
  <div class="row">
    <?php echo form_label(lang('hotcms_status') . ' ' . lang('hotcms__colon')); ?>
    <?php echo form_radio($form['status_pending']); ?>
    <label for="status_pending" style="display:inline-block;margin-left:5px">Pending</label>
    <?php echo form_radio($form['status_confirmed']); ?>
    <label for="status_confirmed" style="display:inline-block;margin-left:5px">Confirmed</label>
    <?php echo form_radio($form['status_closed']); ?>
    <label for="status_closed" style="display:inline-block;margin-left:5px">Closed</label>
  </div>
  <div class="submit">
    <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
    <a href="/hotcms/<?php echo $module_url; ?>/index/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang( 'hotcms_back' ); ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
