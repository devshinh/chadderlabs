<div class="module_header relative"><?php echo $item->name ?>
<a id="back_button" href="/hotcms/<?php echo $module_url ?>" class="red_button_smaller">Back</a>
</div>
<div>
  <form action="/hotcms/<?php echo $module_url ?>/edit/<?php echo $item->id ?>" method="post" id="quiz-form">
    <?php
    echo form_hidden($form['hidden_fields']);
    echo form_hidden('hdnMode', 'edit');
      echo form_hidden("targetOfTrainings", $target_of_trainings);
    ?>
    <div id="general">
      <div class="leftColumn">
        <div class="row">
          <?php echo form_error('quiz_type_id', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_quiz_type') . lang('hotcms__colon'), 'quiz_type_id'); ?>
          <?php $add = 'class="cms_dropdown"' ?>
          <?php echo form_dropdown("quiz_type_id", $quiz_types, set_value('quiz_type_id', $item->quiz_type_id), $add); ?>
        </div>
        <div class="row">
          <?php echo form_error('training_id', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_training') . lang('hotcms__colon'), 'training_id'); ?>
          <?php echo form_dropdown("training_id", $trainings, set_value('training_id', $item->training_id), $add.' id="training"'); ?>
        </div>
        <div class="row">
          <?php echo form_error('target_id', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_target') . lang('hotcms__colon'), 'target_id'); ?>
          <?php echo form_dropdown("target_id", $targets, set_value('target_id', $item->target_id), $add.' id="target"'); ?>
        </div>
        <div class="row">
          <label for="follow">Change As Training</label>
          <input id="follow" type="checkbox" value="follow" checked>
        </div>
      </div>
      <div class="rightColumn">
        <div class="row">
          <?php echo form_error('name', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'name'); ?>
          <?php echo form_input($form['name_input']); ?>
        </div>
        <div class="row">
          <?php echo form_error('status', '<div class="error">', '</div>'); ?>
          <?php echo form_label(lang('hotcms_status') . lang('hotcms__colon'), 'status'); ?>
          <?php echo form_dropdown("status", $status_array, set_value('training_id', $item->status), $add); ?>
        </div>
      </div>
      <div class="clear"></div>
      <div class="submit">
        <a href="#" class="red_button save_link" target="_blank" onclick=""><?php echo lang('hotcms_save_changes') ?></a>
        <a href="/hotcms/<?php echo $module_url ?>/delete/<?php echo $item->id ?>" class="red_button" onClick="return confirmDelete('quiz');" style="float:right;margin-left: 5px;"><?php echo lang('hotcms_delete').' quiz'?></a>
      </div>
    </div>
    <div id="questions">
      <?php
      foreach ($item->type->sections as $section) {
        $sequence = 1;
        echo '<div class="quiz_section"><h3>' . $section_array[$section->section_type] . ' Section</h3>';
        foreach ($item->questions as $q) {
          if ($q->section_id != $section->id) {
            continue;
          }
          echo '<div class="question">';
          //echo '<div class="question_num">Question ' . $sequence . ')</div>';
          echo $q->admin_display;
          echo '</div>';
          $sequence++;
        }
        echo '<a href="' . $module_url . '/ajax_add_question/' . $section->id . '/' . $section->section_type . '" class="red_button add_question_link" target="_blank">Add ' . $section_array[$section->section_type] . ' Question</a>';
        echo '</div>';
      }
      ?>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </form>
</div>