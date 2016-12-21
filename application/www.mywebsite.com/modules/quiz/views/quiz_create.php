<div>
  <form action="/hotcms/<?php echo $module_url; ?>/create" method="post" accept-charset="UTF-8" id="new_quiz_form">
    <?php
      echo form_hidden('hdnMode', 'insert');
    ?>
    <div class="row">
      <?php echo form_error('quiz_type_id', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_quiz_type' ) . lang( 'hotcms__colon' ), 'quiz_type_id');?>
      <?php $add = 'class="cms_dropdown" id="quiz_type"';?>
      <?php echo form_dropdown("quiz_type_id", $quiz_types, (!empty($_POST['quiz_type_id'])?set_value('quiz_type_id',$_POST['quiz_type_id']):''),$add); ?>
    </div>
    <div class="row">
      <?php echo form_error('training_id', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_training' ) . lang( 'hotcms__colon' ), 'training_id');?>
      <?php $add = 'class="cms_dropdown" id="trainning"';?>
      <?php echo form_dropdown("training_id", $trainings,(!empty($_POST['training_id'])?set_value('training_id',$_POST['training_id']):''),$add); ?>
    </div>
    <div class="row">
      <?php echo form_error('name', '<div class="error">','</div>');?>
      <?php echo form_label(lang( 'hotcms_name' ) . lang( 'hotcms__colon' ), 'slug');?>
      <?php echo form_input($form['name_input']); ?>
    </div>
    <div class="submit">
      <input type="submit" class="red_button" value="<?php echo lang( 'hotcms_save' ) ?>" />
      <a href="/hotcms/<?php echo $module_url; ?>/" class="red_button"><?php echo lang( 'hotcms_back' ) ?></a>
    </div>
  </form>
</div>
