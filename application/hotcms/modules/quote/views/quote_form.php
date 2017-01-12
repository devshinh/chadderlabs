<div class="questionnaire" id="questionnaire-<?php print($item->id); ?>">
  <h2>PART 2 OF 2 <span>INSURANCE INFORMATION</span></h2>
  <div class="left-column">
  <?php
  $line_counter = 0;
  $column_shifted = FALSE;
  foreach ($item->questions as $q) {
    if (strpos($q->class, 'hidden') === FALSE) {
      echo '<div class="row">';
    }
    else {
      echo '<div class="row hidden_field">';
    }
    switch ($q->field_type) {
      case 'input':
        $line_counter += 1;
        echo form_error('fld_' . $q->id, '<div class="error">','</div>');
        if ($q->label_position != 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
          if ($q->label_position == 'top') {
            $line_counter += 1;
            echo '<br />';
          }
          else {
            echo ' &nbsp; ';
          }
        }
        if ($q->class > '' || $q->required) {
          echo form_input('fld_' . $q->id, '', 'class="' . $q->class . ($q->required ? ' required' : '') . '"');
        }
        else {
          echo form_input('fld_' . $q->id);
        }
        if ($q->label_position == 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
        }
        if ($q->affix > '') {
          echo ' &nbsp; ' . $q->affix;
        }
        break;

      case 'textarea':
        $line_counter += ($q->rows > 0 ? $q->rows : 3);
        echo form_error('fld_' . $q->id, '<div class="error">','</div>');
        if ($q->label_position != 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
          if ($q->label_position == 'top') {
            $line_counter += 1;
            echo '<br />';
          }
          else {
            echo ' &nbsp; ';
          }
        }
        $textfield = array(
          'name'        => 'fld_' . $q->id,
          'id'          => 'fld_' . $q->id,
          'rows'        => (int)($q->rows),
          'class'       => $q->class . ($q->required ? ' required' : ''),
          'cols'        => '33'
        );
        echo form_textarea($textfield);
        if ($q->label_position == 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
        }
        if ($q->affix > '') {
          echo ' &nbsp; ' . $q->affix;
        }
        break;

      case 'dropdown':
        $line_counter += 1;
        echo form_error('fld_' . $q->id, '<div class="error">','</div>');
        if ($q->label_position != 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
          if ($q->label_position == 'top') {
            $line_counter += 1;
            echo '<br />';
          }
          else {
            echo ' &nbsp; ';
          }
        }
        $options = array('' => $dropdown_hint);
        foreach (explode("\n", $q->options) as $op) {
          $op = trim($op);
          if ($op > '') {
            $options[$op] = $op;
          }
        }
        if ($q->class > '' || $q->required) {
          echo form_dropdown('fld_' . $q->id, $options, '', 'class="' . $q->class . ($q->required ? ' required' : '') . '"');
        }
        else {
          echo form_dropdown('fld_' . $q->id, $options);
        }
        if ($q->label_position == 'right') {
          echo form_label($q->label, 'fld_' . $q->id);
        }
        if ($q->affix > '') {
          echo ' &nbsp; ' . $q->affix;
        }
        break;

      case 'checkbox':
        echo '';
        break;
      case 'checkboxes':
        echo '';
        break;
      case 'radios':
        echo '';
        break;
      case 'password':
        echo '';
        break;
    }
    echo "</div>\n";
    if ($line_counter >= $lines_per_column && !$column_shifted) {
      echo '</div><div class="right-column">';
      $column_shifted = TRUE;
    }
  }
  ?>
  </div>
  <div class="buttons">
    <!-- <input type="button" class="btn-back" name="back" value="Go Back" /> &nbsp; -->
    <input type="submit" class="btn-quote" name="quote" value="Get a Quote" />
  </div>
</div>
<div class="clear"></div>
