<?php
  if (!empty($title)) {
    echo '<h1>' . $title . "</h1>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/quote/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/quote/js/' . $js . "\"></script>\n";
    }
  }
  if (isset($item->publish_timestamp) && $item->publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->publish_timestamp);
  }
  elseif (isset($item->scheduled_publish_timestamp) && $item->scheduled_publish_timestamp > 0) {
    $date = date('Y-m-d H:i:s', $item->scheduled_publish_timestamp);
  }
  else {
    $date = '(unknown publish date)';
  }
?>
<div class="quote-info">
  <h2>PART 1 OF 2 <span>PERSONAL INFORMATION</span></h2>
  <?php if ( $message>'' ){ ?><div class="message"><?php echo $message;?></div><?php } ?>
  <?php if ( $error>'' ){ ?><div class="error"><?php echo $error;?></div><?php } ?>

  <div class="left-column">
    <div class="row">
      <?php echo form_error('title', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_title') . lang('hotcms__colon'), 'title');?>
      <?php echo form_dropdown("title", $title_array, '', 'class="required"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('firstname', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_name_first') . lang('hotcms__colon'), 'firstname');?><br />
      <?php echo form_input('firstname', '', 'class="required" size="40"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('lastname', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_name_last') . lang('hotcms__colon'), 'lastname');?><br />
      <?php echo form_input('lastname', '', 'class="required" size="40"'); ?>
    </div>
    <?php if($hidden_fields['form_id'] != 2 && $hidden_fields['form_id'] != 6){?>
    <div class="row">
      <?php echo form_error('dateofbirth', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_dob') . lang('hotcms__colon'), 'dateofbirth');?>
      <?php echo form_input('dateofbirth', '', 'class="required"'); ?>
    </div>
    <?php } ?>
    <div class="row">
      <?php echo form_error('phone', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_phone_number') . lang('hotcms__colon'), 'phone');?>
      <?php echo form_input('phone', '', 'class="required"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('email', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_email_address') . lang('hotcms__colon'), 'email');?>
      <?php echo form_input('email', '', 'class="required email" size="30"'); ?>
    </div>
    <?php 
    $required = 'required';
    if($hidden_fields['form_id'] == 2 || $hidden_fields['form_id'] == 6){
      $required = '';
      }
    ?>
    <div class="row">
      <?php echo form_error('near_location', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_near_location') . lang('hotcms__colon'), 'near_location');?>
      <?php echo form_dropdown("near_location", $location_array, '', 'class="'.$required.'"'); ?>
    </div>
  </div>
  <div class="right-column">
    <div class="row">
      <?php echo form_error('address1', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_address1') . lang('hotcms__colon'), 'address1');?><br />
      <?php echo form_input('address1', '', 'class="required" size="40"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('address2', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_address2') . lang('hotcms__colon'), 'address2');?><br />
      <?php echo form_input('address2', '', 'size="40"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('city', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_city') . lang('hotcms__colon'), 'city');?><br />
      <?php echo form_input('city', '', 'class="required" size="40"'); ?>
    </div>
    <div class="row">
      <?php echo form_error('postal', '<div class="error">','</div>');?>
      <?php echo form_label(lang('hotcms_postal_code') . lang('hotcms__colon'), 'postal');?>
      <?php echo form_input('postal', '', 'class="required" size="10"'); ?>
    </div>
  </div>
  <div class="buttons">
    <input type="button" class="btn-continue" name="continue" value="Continue" />
  </div>
</div>
