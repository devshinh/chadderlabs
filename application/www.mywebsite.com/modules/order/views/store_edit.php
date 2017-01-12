<div>
  <form action="/hotcms/<?php echo $module_url?>/store_edit/<?php echo $retailer->id ?>/<?php echo $currentItem->id ?>" method="post">
  <div class="row">
  <?php
    echo form_error('store_name', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'store_name');
    echo form_input($form['store_name_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('store_num', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_store_num') . ' ' . lang('hotcms__colon'), 'store_num');
    echo form_input($form['store_num_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('street_1', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_address1') . ' ' . lang('hotcms__colon'), 'street_1');
    echo form_input($form['street_1_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('street_2', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_address2') . ' ' . lang('hotcms__colon'), 'street_2');
    echo form_input($form['street_2_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('city', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_city') . ' ' . lang('hotcms__colon'), 'city');
    echo form_input($form['city_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('country_code', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_country') . ' ' . lang('hotcms__colon'), 'country_code');
    echo form_dropdown('country_code', $form['country_code_options'], $selected_country);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('province', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_province') . ' ' . lang('hotcms__colon'), 'province');
    echo form_dropdown('province', $form['province_options'], $selected_province);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('postal_code', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_postal_code') . ' ' . lang('hotcms__colon'), 'postal_code');
    echo form_input($form['postal_code_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('phone', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_phone') . ' ' . lang('hotcms__colon'), 'phone');
    echo form_input($form['phone_input']);
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
    <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
    <a href="/hotcms/<?php echo $module_url ?>/store/<?php echo $retailer->id ?>/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang('hotcms_back') ?></a>
    <a onClick="return confirmDelete()" href="/hotcms/<?php echo $module_url?>/store_delete/<?php echo $retailer->id ?>/<?php echo $currentItem->id ?>" class="red_button"><?php echo lang('hotcms_delete') ?></a>
    <?php echo form_hidden('hdnMode', 'edit') ?>
  </div>
  </form>
</div>