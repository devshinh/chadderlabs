<div>
  <form action="/hotcms/<?php echo $module_url ?>/create" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    <div class="row">
      <?php echo form_error('salutation', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_salutation') . ' ' . lang('hotcms__colon'), 'salutation'); ?>
      <?php echo form_dropdown('salutation', $salutation, ''); ?>
    </div>
    <div class="row">
      <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_name_first') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'first_name'); ?>
      <?php echo form_input($first_name_input); ?>
    </div>
    <?php /* div class="row">
      <?php echo form_error('middle_name', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_name_middle') . ' ' . lang('hotcms__colon'), 'middle_name'); ?>
      <?php echo form_input($middle_name_input); ?>
    </div */ ?>
    <div class="row">
      <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_name_last') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'last_name'); ?>
      <?php echo form_input($last_name_input); ?>
    </div>
    <div class="row space">
      <?php echo form_error('username', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_name_user') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'username'); ?>
      <?php echo form_input($username_input); ?>
    </div>
    <div class="row">
      <?php echo form_error('password', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_password') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'password'); ?>
      <?php echo form_password($password_input); ?>
    </div>
    <div class="row">
      <?php echo form_error('password_retype', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_password_retype') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'password_retype'); ?>
      <?php echo form_password($password_retype_input); ?>
    </div>
    <div class="row">
      <?php echo form_error('email', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_email_address') . '<span class="red">*</span> ' . lang('hotcms__colon'), 'email'); ?>
      <?php echo form_input($email_input); ?>
    </div>
    <?php /* div class="row">
      <?php echo form_error('position', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_user_position') . ' ' . lang('hotcms__colon'), 'position'); ?>
      <?php echo form_input($position_input); ?>
    </div */ ?>
    <?php if (isset($roles) && count($roles) > 0) { ?>
    <div class="row">
      <?php echo form_label(lang('hotcms_role') . ' ' . lang('hotcms__colon'), 'roles'); ?>
      <?php echo form_error('roles', '<div class="error">', '</div>'); ?>
      <?php
      foreach ($roles as $role) {
        echo '<div class="checkbox">';
        echo form_checkbox($role);
        echo form_label($role["id"], $role["id"]);
        echo '</div>';
      }
      ?>
    </div>
    <?php } ?>

    <div class="row">         
      <?php echo form_error('asset_file', '<div class="error">', '</div>'); ?>
      <?php echo form_label(lang('hotcms_user_avatar') . ' ' . lang('hotcms__colon'), 'asset_file'); ?>
      <?php echo form_upload($asset_file_input); ?>                     
    </div>      

    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang('hotcms_save') ?>" />
      <?php echo form_hidden('hdnMode', 'insert') ?>
    </div>
  </form>
</div>
