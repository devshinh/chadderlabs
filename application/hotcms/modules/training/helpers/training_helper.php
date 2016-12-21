<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Training helper
 */

/**
 * Cron jobs
 */
if (!function_exists('training_cron'))
{
  function training_cron()
  {
    $CI =& get_instance();
    $CI->load->model('training/training_model');
    $CI->training_model->training_schedule_run();
  }
}

/**
 * Sitemap
 * @return array
 */
if (!function_exists('training_sitemap'))
{
  function training_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('training/training_model');
    $link_array = array();
    $category_id = 1;
    $rows = $CI->training_model->list_all_training($category_id, TRUE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'title' => $row->title,
      );
    }
    return $link_array;
  }
}

/**
 * Renders a field config user interface (UI)
 * @param  object  form field object
 * @return string
 */
if (!function_exists('field_config_ui'))
{
  function field_config_ui($fld)
  {
    $CI =& get_instance();
    $CI->load->config('training/training');
    $field_types = $CI->config->item('field_types', 'training');
    if (array_key_exists($fld->field_type, $field_types)) {
      $field_type = $field_types[$fld->field_type];
    }
    else {
      $field_type = 'Unknown';
    }
    $result = '<div class="module_header"><div class="variant_type_name">' . $fld->label . '</div>
        <div class="variant_type_actions">
          <a class="edit_variant_type_link" href="' . $fld->id . '">
            <div class="btn-edit"></div>
          </a>
          <a class="delete_variant_type_link" href="' . $fld->id . '">
            <div class="btn-delete"></div>
          </a>
        </div>
      </div>
      <div></div>
      <div class="table"><p><b>' . $field_type . '</b></p>';
    switch ($fld->field_type) {
      case 'input':
        $result .= '<p>Max Length: ';
        $result .= $fld->max_length;
        $result .= '</p>';
        break;

      case 'textarea':
        //$result .= '<p>Rows: ';
        //$result .= $fld->rows;
        //$result .= '</p>';
        break;

      case 'dropdown':
          $result .= '<select>';          
          foreach (explode("\n", $fld->options) as $op) {
            $op = trim($op);
            if ($op > '') {
              $result .= '<option>'. $op.'</option>';
            }
          }
          $result .= '</select>';
          break;
      case 'checkboxes':  
          //$result .= ' field_config_ui ';
      case 'radios':
        $result .= '<p>Options:<br />';
        $result .= $fld->options;
        $result .= '</p>';
        break;

      case 'checkbox':
        $result .= '';
        break;
      case 'date':
        $result .= '';
        break;
      case 'image':
        $result .= '';
        break;
      case 'numberrange':
        $result .= '';
        break;
      case 'password':
        $result .= '';
        break;
    }
    $result .= "</div>\n";
    return $result;
  }
}

/**
 * Config a field
 * @param  object  form field object
 * @return string
 */
if (!function_exists('field_config_form'))
{
  function field_config_form($fld)
  {
    $CI =& get_instance();
    $CI->load->config('training/training');
    $field_types = $CI->config->item('field_types', 'training');
    if (array_key_exists($fld->field_type, $field_types)) {
      $field_type = $field_types[$fld->field_type];
    }
    else {
      $field_type = 'Unknown';
    }
    if ($fld->visible) {
      $div_class = "row";
    }
    else {
      $div_class = "row hidden_field";
    }
    $result = '<form class="field_config_form">';
    $result .= '<div class="' . $div_class . '">';
    $result .= '<b>' . $field_type . '</b>';
    $result .= '</div><div class="' . $div_class . '">';
    $result .= form_error('label', '<div class="error">', '</div>');
    $result .= form_label('Name: ', 'label');
    $result .= form_input('label', $fld->label, 'class="text_field required"');
    switch ($fld->field_type) {
      case 'input':
        $result .= '</div><div class="' . $div_class . '">';
        $result .= form_error('max_length', '<div class="error">', '</div>');
        $result .= form_label('Character Limit: ', 'max_length');
        $result .= form_input('max_length', $fld->max_length, 'class="short_field"');
        break;

      case 'textarea':
        $result .= '</div><div class="' . $div_class . '">';
        $result .= form_error('rows', '<div class="error">', '</div>');
        $result .= form_label('Rows: ', 'rows');
        $result .= form_input('rows', $fld->rows, 'class="short_field"');
        break;

      case 'dropdown':
      case 'checkboxes':
      case 'radios':
        $result .= '</div><div class="' . $div_class . '">';
        $result .= form_error('options', '<div class="error">', '</div>');
        $result .= form_label('Options: ', 'options');
        $textfield = array(
          'name'        => 'options',
          'id'          => 'options',
          'value'       => $fld->options,
          'class'       => 'required',
          'rows'        => '6',
          'cols'        => '33'
        );
        $result .= form_textarea($textfield);
        break;

      case 'checkbox':
        break;
      case 'date':
        break;
      case 'image':
        break;
      case 'numberrange':
        break;
      case 'password':
        break;
    }
    $result .= "</div>\n";
    $result .= "</form>\n";
    return $result;
  }
}

/**
 * Renders a field form
 * @param  object  form field object
 * @return string
 */
if (!function_exists('field_form'))
{
  function field_form($fld, $value = '', $preview = '')
  {
    if (strpos($fld->class, 'hidden') === FALSE) {
      $result = '<div class="row">';
    }
    else {
      $result = '<div class="row hidden_field">';
    }
    switch ($fld->field_type) {
      case 'input':
        $result .= form_error('fld_' . $fld->id, '<div class="error">','</div>');
        if ($fld->label_position != 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
          if ($fld->label_position == 'top') {
            $result .= '<br />';
          }
          else {
            $result .= ' &nbsp; ';
          }
        }
        if ($fld->class > '' || $fld->required) {
          $result .= form_input('fld_' . $fld->id, $value, 'class="' . $fld->class . ($fld->required ? ' required' : '') . '"');
        }
        else {
          $result .= form_input('fld_' . $fld->id, $value);
        }
        if ($fld->label_position == 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
        }
        if ($fld->affix > '') {
          $result .= ' &nbsp; ' . $fld->affix;
        }
        break;

      case 'textarea':
        $result .= form_error('fld_' . $fld->id, '<div class="error">','</div>');
        if ($fld->label_position != 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
          if ($fld->label_position == 'top') {
            $result .= '<br />';
          }
          else {
            $result .= ' &nbsp; ';
          }
        }
        $textfield = array(
          'name'        => 'fld_' . $fld->id,
          'id'          => 'fld_' . $fld->id,
          'rows'        => (int)($fld->rows),
          'class'       => $fld->class . ($fld->required ? ' required' : ''),
          'cols'        => '33'
        );
        $result .= form_textarea($textfield, $value);
        if ($fld->label_position == 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
        }
        if ($fld->affix > '') {
          $result .= ' &nbsp; ' . $fld->affix;
        }
        break;

      case 'dropdown':
        $result .= form_error('fld_' . $fld->id, '<div class="error">','</div>');
        if ($fld->label_position != 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
          if ($fld->label_position == 'top') {
            $result .= '<br />';
          }
          else {
            $result .= ' &nbsp; ';
          }
        }
        $options = array('' => ' -- Select -- ');
        foreach (explode("\n", $fld->options) as $op) {
          $op = trim($op);
          if ($op > '') {
            $options[$op] = $op;
          }
        }
        if ($fld->class > '' || $fld->required) {
          $result .= form_dropdown('fld_' . $fld->id, $options, $value, 'class="' . $fld->class . ($fld->required ? ' required' : '') . '"');
        }
        else {
          $result .= form_dropdown('fld_' . $fld->id, $options, $value);
        }
        if ($fld->label_position == 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
        }
        if ($fld->affix > '') {
          $result .= ' &nbsp; ' . $fld->affix;
        }
        break;

      case 'image':
        $result .= form_error('fld_' . $fld->id, '<div class="error">','</div>');
        if ($fld->label_position != 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
          if ($fld->label_position == 'top') {
            $result .= '<br />';
          }
          else {
            $result .= ' &nbsp; ';
          }
        }
        $value = (int)$value;
        $result .= '<span class="asset_preview" id="fld_preview_' . $fld->id . '">';
        $result .= '<span class="image_preview">';
        if ($value > 0 && $preview > '') {
          $result .= $preview;
        }
        $result .= '</span>';
        $result .= '<a href="' . $value . '" class="variant_image_link">Select Image</a>';
        if ($fld->class > '' || $fld->required) {
          $result .= form_hidden('fld_' . $fld->id, $value, 'class="' . $fld->class . ($fld->required ? ' required' : '') . '"');
        }
        else {
          $result .= form_hidden('fld_' . $fld->id, $value);
        }
        $result .= '</span>';
        if ($fld->label_position == 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
        }
        if ($fld->affix > '') {
          $result .= ' &nbsp; ' . $fld->affix;
        }
        break;

      case 'checkbox':
        $result .= 'checkbox';
        break;
      case 'checkboxes':
         //var_dump($value);
          $checked_values = explode('\n', $value);
         // var_dump($checked_values);

        $result .= form_error('fld_' . $fld->id, '<div class="error">','</div>');
        if ($fld->label_position != 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
          if ($fld->label_position == 'top') {
            $result .= '<br />';
          }
          else {
            $result .= ' &nbsp; ';
          }
        }
        //var_dump($fld);
        $result .= '<div class="checkboxes">';
        foreach (explode("\n", $fld->options) as $op) {
          $checked = in_array($op, $checked_values);
          $result .= '<div>';
          $fld_id = 'fld_' . $fld->id .'_' . url_title($op);

                            $result .= form_checkbox(array(
                                'name' => 'checkboxes_variant_fld_'.$fld->id.'[]',
                                'id' => $fld_id,
                                'value' => $op,
                                //'class' => $fld->class . ($fld->required ? ' required' : ''),
                                'checked' => $checked,
                            ));
                            $result .= ' ';
                            $result .= form_label($op , $fld_id);
                            $result .= ' &nbsp; ';         
        $result .= '</div>';                 
        }
        $result .= '</div>'; 

        if ($fld->label_position == 'right') {
          $result .= form_label($fld->label, 'fld_' . $fld->id);
        }
        if ($fld->affix > '') {
          $result .= ' &nbsp; ' . $fld->affix;
        }          
        break;
      case 'radios':
        $result .= 'radios';
        break;
      case 'password':
        $result .= 'password';
        break;
    }
    $result .= "</div>\n";
    return $result;
  }
}
