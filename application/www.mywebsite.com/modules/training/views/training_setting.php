<div>
  <form action="/hotcms/<?php echo $module_url?>/setting" method="post" id="training-setting-form">
  <?php
    foreach ($training_types as $item) {
      $id = $item->id;
  ?>
    <div id="general">
      <div class="row">
        <?php echo form_error('name_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_name' ).' '.lang( 'hotcms__colon' ), 'name_' . $id);?>
        <?php echo form_input($form['name_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('time_limit_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_time_limit' ).' '.lang( 'hotcms__colon' ), 'time_limit_' . $id);?>
        <?php echo form_input($form['time_limit_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('expiry_period_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_expiry_period' ).' '.lang( 'hotcms__colon' ), 'expiry_period_' . $id);?>
        <?php echo form_input($form['expiry_period_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('tries_per_day_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_tries_per_day' ).' '.lang( 'hotcms__colon' ), 'tries_per_day_' . $id);?>
        <?php echo form_input($form['tries_per_day_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('tries_per_week_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_tries_per_week' ).' '.lang( 'hotcms__colon' ), 'tries_per_week_' . $id);?>
        <?php echo form_input($form['tries_per_week_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('points_pre_expiry_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_points_pre_expiry' ).' '.lang( 'hotcms__colon' ), 'points_pre_expiry_' . $id);?>
        <?php echo form_input($form['points_pre_expiry_' . $id]); ?>
      </div>
      <div class="row">
        <?php echo form_error('points_post_expiry_' . $id, '<div class="error">','</div>');?>
        <?php echo form_label(lang( 'hotcms_points_post_expiry' ).' '.lang( 'hotcms__colon' ), 'points_post_expiry_' . $id);?>
        <?php echo form_input($form['points_post_expiry_' . $id]); ?>
      </div>
      <div class="submit">
        <a href="<?php echo $id ?>" class="red_button save_link" target="_blank" onclick=""><?php echo lang( 'hotcms_save_changes' ) ?></a>
        <a href="/hotcms/<?php echo $module_url?>/type_delete/<?php echo $id ?>" class="red_button" onClick="return confirmDelete('training type');" style="float:right;margin-left: 5px;"><?php echo lang( 'hotcms_delete' ) ?></a>
      </div>
    </div>
    <div class="sections">
      <?php
      $sequence = 0;
      foreach ($item->sections as $section) {
        $sequence++;
        echo '<div class="training_section"><h3>Section ' . $sequence . ': ';
        echo form_dropdown("section_type_" . $section->id, $section_type_array, $section->section_type);
        echo '</h3>';
        echo '<div class="row">';
        echo form_error('question_pool_' . $section->id, '<div class="error">','</div>');
        echo form_label(lang('hotcms_question_pool') . lang('hotcms__colon'), 'question_pool_' . $section->id);
        echo form_input($form['question_pool_' . $section->id]);
        echo "</div>\n";
        echo '<div class="row">';
        echo form_error('questions_per_training_' . $section->id, '<div class="error">','</div>');
        echo form_label(lang('hotcms_questions_per_training') . lang('hotcms__colon'), 'questions_per_training_' . $section->id);
        echo form_input($form['questions_per_training_' . $section->id]);
        echo "</div>\n";
        echo '<a name="save[' . $id . ']" href="' . $section->id . '" class="red_button save_section_link" target="_blank">Save</a>';
        echo '</div>';
      }
      ?>
      <a href="<?php echo $id; ?>" class="red_button add_section_link" target="_blank">Add Section</a>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  <?php
    }
  ?>
  </form>
</div>