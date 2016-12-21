<?php
  if (!empty($title)) {
    echo '<h1>' . $title . "</h1>\n";
  }
  if ($environment == 'admin_panel') {
    if (!empty($css)) {
      echo '<link rel="stylesheet" type="text/css" media="all" href="modules/quote/css/' . $css . "\" />\n";
    }
    if (!empty($js)) {
      echo '<script type="text/javascript" src="modules/refer_colleague/js/' . $js . "\"></script>\n";
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
<div id="refer-colleague" class="hero-unit">
    <div class="box-title">Refer a colleague</div>
    
    <div class="row-fluid">
      <?php echo form_label('Colleague\'s '.lang('hotcms_name_first') . lang('hotcms__colon') .'<span class="red">*</span>', 'firstname');?>
      <?php echo form_input('firstname', '', 'class="required" size="40"'); ?>
    </div>
    <div class="row-fluid">
      <?php echo form_label('Colleague\'s '.lang('hotcms_name_last') . lang('hotcms__colon').'<span class="red">*</span>', 'lastname');?>
      <?php echo form_input('lastname', '', 'class="required" size="40"'); ?>
    </div>

    <div class="row-fluid">
      <?php echo form_label('Colleague\'s '.lang('hotcms_email_address') . lang('hotcms__colon').'<span class="red">*</span>', 'email');?>
      <?php echo form_input('email', '', 'class="required email" size="30"'); ?>
    </div>
    <div class="row-fluid">
       
        <?php echo form_hidden($refer_colleague_hidden); ?>
    </div>
    
    
  
    <input type="submit" class="btn btn-primary btn-large" name="refer" value="refer" />
  
</div>

<!-- Modal -->
<div id="refColModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="shopCartModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="myModalLabel">Refer a Colleague</h3>
</div>
<div class="modal-body">
<p><?php echo($refer_modal_content); ?></p>
</div>
<div class="modal-footer">
<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
</div>
</div>
