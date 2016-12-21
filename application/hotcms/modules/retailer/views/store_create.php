<div>
    <div class="row">
        <p>Adding new Store for <strong><?php echo $retailer->name?></strong></p>
    </div>
  <form action="/hotcms/<?php echo $module_url ?>/store_create/<?php echo $retailer->id ?>" method="post" accept-charset="UTF-8">
  <div class="row">
  <?php
    echo form_error('store_name', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_location') . ' ' . lang('hotcms__colon'), 'store_name');
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
    echo form_label(lang('hotcms_province_state') . ' ' . lang('hotcms__colon'), 'province');
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
  <?php
    echo form_error('fax', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_fax') . ' ' . lang('hotcms__colon'), 'fax');
    echo form_input($form['fax_input']);
  ?>
  </div>    
  <div class="row">
  <?php
    echo form_error('email', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_email') . ' ' . lang('hotcms__colon'), 'email');
    echo form_input($form['email_input']);
  ?>
  </div>         
  <div class="row">
  <?php
    echo form_error('phone', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_longitude') . ' ' . lang('hotcms__colon'), 'longitude');
    echo form_input($form['longitude_input']);
  ?>
  </div>
  <div class="row">
  <?php
    echo form_error('phone', '<div class="error">', '</div>');
    echo form_label(lang('hotcms_latitude') . ' ' . lang('hotcms__colon'), 'latitude');
    echo form_input($form['latitude_input']);
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
    <a href="/hotcms/<?php echo $module_url ?>/store/<?php echo $retailer->id ?>/<?php echo $index_page_num; ?>" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    <?php echo form_hidden('hdnMode', 'insert') ?>
  </div>
  </form>
</div>
