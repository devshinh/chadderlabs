<div id="editHours_<?php echo $form_day_hours['row_id'] ?>" class="hours_row">
  <div class="row">
    <div class="day"><?php echo $form_day_hours['day']; ?></div>
    <div class="input closed"> 
      <?php echo form_label('Closed' . lang('hotcms__colon'), 'closed'); ?>
      <?php echo form_hidden($form_day_hours['closed_hidden']); ?> 
      <?php echo form_checkbox($form_day_hours['closed']); ?> 
    </div>    
  </div>          
  <div class="row">  
    <div class="input"> 
      <?php echo form_label('From' . lang('hotcms__colon'), 'from1'); ?>
      <?php echo form_input($form_day_hours['from1']); ?> 
    </div>         
    <div class="input"> 
      <?php echo form_label('To' . lang('hotcms__colon'), 'to1'); ?>
      <?php echo form_input($form_day_hours['to1']); ?> 
    </div> 
    <div class="extra_fields">
      <div class="input"> 
        <?php echo form_label('From' . lang('hotcms__colon'), 'from2'); ?>
        <?php echo form_input($form_day_hours['from2']); ?> 
      </div>         
      <div class="input"> 
        <?php echo form_label('To' . lang('hotcms__colon'), 'to2'); ?>
        <?php echo form_input($form_day_hours['to2']); ?> 
      </div>         
    </div>               
  </div>
</div>