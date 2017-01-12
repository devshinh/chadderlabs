<div>
  <div class="row">

    <form action="/hotcms/<?php echo $module_url ?>/setting_reload" method="post" name="quiz-type-form">
      <?php echo form_error('quiz_type', '<div class="error">', '</div>'); ?>
      <?php      
      $selected_quiz_type = $selected_type;
      $add = 'onChange="document.quiz-type-form.submit()" style="float:left;margin-right: 15px;margin-top:5px;"';
      echo form_dropdown("quiz_type", $quiz_type_dropdown, $selected_quiz_type, $add);
      ?>
    </form>

    <a id="add_new_type" href="/hotcms/<?php echo $module_url ?>/add_quiz_type" class="red_button">Add new quiz type</a>
    <div id="quiz-type-info">
      <?php
      if ($quiz_by_type > 1) {
        printf('This Quiz type has %s quizzes.', $quiz_by_type);
      } elseif ($quiz_by_type == 0) {
        printf('This Quiz type has 0 quiz.', $quiz_by_type);
      } else {
        printf('This Quiz type has %s quiz.', $quiz_by_type);
      }
      ?>
    </div>
  </div>    
  <?php
  foreach ($quiz_types as $item) {
    $id = $item->id;
    if ($id == $selected_quiz_type) {
      // turn on js validation
      ?>
      <div class="quiz_type">
        <form action="/hotcms/<?php echo $module_url ?>/save_setting/<?php echo $id ?>" method="post" id="quiz-setting-form">
          <?php echo form_hidden($form['hidden_fields']); ?>          
          <div id="general">
            <div class="row">
              <?php echo form_error('name_' . $id, '<div class="error">', '</div>'); ?>
              <?php echo form_label(lang('hotcms_name') . ' ' . lang('hotcms__colon'), 'name_' . $id); ?>
              <?php echo form_input($form['name_' . $id]); ?>
            </div>
            <div class="leftColumn">
              <div class="row">
                <?php echo form_error('time_limit_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_time_limit') . ' ' . lang('hotcms__colon'), 'time_limit_' . $id); ?>
                <?php echo form_input($form['time_limit_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('tries_per_day_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_tries_per_day') . ' ' . lang('hotcms__colon'), 'tries_per_day_' . $id); ?>
                <?php echo form_input($form['tries_per_day_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('tries_per_week_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_tries_per_week') . ' ' . lang('hotcms__colon'), 'tries_per_week_' . $id); ?>
                <?php echo form_input($form['tries_per_week_' . $id]); ?>
              </div>
    <div id="featured_image_div">
      <h3>Icon Image</h3>
      <div id="featured_image">
        <?php        
        if ($item->icon_image_id!=0) {
            $icon = $quiz_types_object[$id]->icon_image;
            printf('<img src="%s" alt="%s" title="%s" width="30" height="30" />', $icon->full_path, $icon->name, $icon->description);          
        }
        ?>
      </div>
      <a id="<?php echo $id ?>" href="<?php echo $item->icon_image_id; ?>" class="red_button icon_image_link">Choose</a>
    </div>        

            </div>
            <div class="rightColumn">
              <div class="row">
                <?php echo form_error('expiry_period_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_expiry_period') . ' ' . lang('hotcms__colon'), 'expiry_period_' . $id); ?>
                <?php echo form_input($form['expiry_period_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('points_pre_expiry_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_points_pre_expiry') . ' ' . lang('hotcms__colon'), 'points_pre_expiry_' . $id); ?>
                <?php echo form_input($form['points_pre_expiry_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('points_post_expiry_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_points_post_expiry') . ' ' . lang('hotcms__colon'), 'points_post_expiry_' . $id); ?>
                <?php echo form_input($form['points_post_expiry_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('contest_entries_pre_expiry_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_contest_entries_pre_expiry') . ' ' . lang('hotcms__colon'), 'contest_entries_pre_expiry_' . $id); ?>
                <?php echo form_input($form['contest_entries_pre_expiry_' . $id]); ?>
              </div>
              <div class="row">
                <?php echo form_error('contest_entries_post_expiry_' . $id, '<div class="error">', '</div>'); ?>
                <?php echo form_label(lang('hotcms_contest_entries_post_expiry') . ' ' . lang('hotcms__colon'), 'contest_entries_post_expiry_' . $id); ?>
                <?php echo form_input($form['contest_entries_post_expiry_' . $id]); ?>
              </div>                
            </div>
            <div class="clear"></div>
            <div class="submit">
              <input type="submit" class="red_button" value="<?php echo lang('hotcms_save_changes') ?>" />
              <a href="/hotcms/<?php echo $module_url ?>/delete_type/<?php echo $id ?>" class="red_button" onClick="return confirmDelete('quiz type');" style="float:right;margin-left: 5px;"><?php echo lang('hotcms_delete') ?> quiz type</a>
            </div>
          </div>

          <div class="sections">
            <?php
            $sequence = 0;
            foreach ($item->sections as $section) {
              // turn on js validation
              ?>

              <?php
              $sequence++;
              echo '<div class="quiz_section"><div class="row"><label> Section ' . $sequence . ': </label>';
              $add = 'class="cms_dropdown"';
              echo form_dropdown("section_type_" . $section->id, $section_type_array, $section->section_type, $add);
              echo '</div>';
              echo '<div class="row required">';
              echo form_error('question_pool_' . $section->id, '<div class="error">', '</div>');
              echo form_label(lang('hotcms_question_pool') . lang('hotcms__colon'), 'question_pool_' . $section->id);
              echo form_input($form['question_pool_' . $section->id]);
              echo "</div>\n";
              echo '<div class="row required">';
              echo form_error('questions_per_quiz_' . $section->id, '<div class="error">', '</div>');
              echo form_label(lang('hotcms_questions_per_quiz') . lang('hotcms__colon'), 'questions_per_quiz_' . $section->id);
              echo form_input($form['questions_per_quiz_' . $section->id]);
              echo "</div>\n";
              echo '<a href="/hotcms/' . $module_url . '/delete_section/' . $section->id . '/' . $id . '" class="red_button" onClick="return confirmDelete()">Delete section</a>';
              echo '</div>';
            }
            ?>
        </form>
        <div class="add_section">
          <a href="/hotcms/<?php echo $module_url ?>/add_section/<?php echo $id; ?>" class="red_button">Add Section</a>
        </div>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
    </div> <!-- .quiz-type-->
    <?php
  }
}
?>
</div>

<div id="icon-image-form" title="Icon Image">
</div>