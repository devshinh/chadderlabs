<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Customized form validation functions
 */
class MY_Form_validation extends CI_Form_validation {

  /**
   * Check page name/URL to make sure it's unique
   * @param string $name
   * @param int $val
   * @return boolean
   */
  function unique_page($name, $val = 0) {
    $url = format_url($name);
    $val = (int)$val;
    $this->CI->load->model('page_model');
    if ($this->CI->page_model->url_exists($url, $val)) {
      $this->set_message('unique_page', 'A page already exists with this name/URL.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Check post title/slug to make sure it's unique
   * TODO: put this into the module folder
   * @param  string  $title
   * @param  int  $val
   * @return boolean
   */
  function unique_news($title, $val = 0) {
    $slug = format_url($title);
    $val = (int)$val;
    $this->CI->load->model('news_model');
    if ($this->CI->news_model->slug_exists($slug, $val)) {
      $this->set_message('unique_news', 'A post already exists with this title/slug.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Check post title/slug to make sure it's unique
   * TODO: put this into the module folder
   * @param  string  $title
   * @param  int  $val
   * @return boolean
   */
  function unique_product($title, $val = 0) {
    $slug = format_url($title);
    $val = (int)$val;
    $this->CI->load->model('model_product');
    if ($this->CI->model_product->slug_exists($slug, $val)) {
      $this->set_message('unique_product', 'A product already exists with this URL.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Check media file name to make sure it's not already exist
   * TODO: move this function into the module folder
   * @param  string  $new_filename  the new file name
   * @param  string  $field_name
   * @return boolean
   */
  function unique_filename($new_filename, $field_name = '')
  {
    $overwrite = $this->CI->input->post($field_name . '_overwrite');
    $old_filename = $this->CI->input->post($field_name . '_current');
    if ($overwrite != '1' && $new_filename > '') { // && $new_filename != $old_filename) {
      $this->CI->load->config('asset', TRUE);
      $public_path = '/' . $this->CI->config->item('public_path', 'asset') . '/' . $this->CI->session->userdata('sitePath') . '/';
      $abs_upload_path = $_SERVER['DOCUMENT_ROOT'] . '/application/' . $this->CI->config->item('application_path', 'asset') . $public_path;
      $file_name = $abs_upload_path . $new_filename;
      if (file_exists($file_name)) {
        $this->set_message('unique_filename', 'A file named ' . $new_filename . ' already exists on the server.');
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Checking user's chosen month for montly type draw.
   * @param  int     $month user's chosen month
   * @return boolean
   */
  function monthly_type_draw_check($month) {
    $year = $this->CI->input->post("draw_monthly_year");
    $this->CI->load->model("draw/draw_model");
    $this->CI->load->library("form_validation");
    if ($this->CI->draw_model->monthly_type_check($month, $year)) {
      $this->CI->form_validation->set_message("monthly_type_draw_check", "Your chosen month is alreay been used in other draw.");
      return FALSE;
    } elseif ($this->CI->draw_model->get_eligible_entries_for_monthly($month, $year) < 1) {
      $this->set_message("monthly_type_draw_check", "Your chosen month has 0 eligible entries.");
      return FALSE;
    } else {
      return TRUE;
    }
  }

  /**
   * Checking user's chosen draw type.
   * @param  string  $type user's chosen draw type
   * @return boolean
   */
  function life_type_draw_check($type) {
    $this->CI->load->model("draw/draw_model");
    $this->CI->load->library("form_validation");
    if ((strcasecmp($type, "life") === 0) && $this->CI->draw_model->get_eligible_entries_for_life() < 1) {
      $this->set_message("life_type_draw_check", "Current life-type draw has 0 eligible entries from last draw.");
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validate the target date is after the other date
   * @param  string  $target_date
   * @param  string  $after_date
   * @return boolean
   */
  function date_after($target_date, $after_date = '') {
    $target_timestamp = strtotime($target_date);
    $after_timestamp = strtotime($after_date);
    return (($target_timestamp !== FALSE) && ($after_timestamp !== FALSE) && ($target_timestamp > $after_timestamp));
  }

  /**
   * Validate the target date is before the other date
   * @param  string  $target_date
   * @param  string  $before_date
   * @return boolean
   */
  function date_before($target_date, $before_date = '') {
    $target_timestamp = strtotime($target_date);
    $before_timestamp = strtotime($before_date);
    return (($target_timestamp !== FALSE) && ($before_timestamp !== FALSE) && ($target_timestamp < $before_timestamp));
  }

  /**
   * Validate the input string as English time.
   * @param  string  $date
   * @return bollean
   */
  function valid_time($date) {
    return !(strtotime($date) === FALSE);
  }

  /**
   * Validate the $target_number is less than or equal $to_compare_with.
   * @param  numeric $target_number
   * @param  numeric $to_compare_with
   * @return boolean
   */
  function less_than_or_equal($target_number, $to_compare_with = 0) {
    if (( !isset($target_number)) OR ( ! $this->is_numeric($target_number)) OR ( ! $this->is_numeric($to_compare_with))) {
      return; // This function only verify $target_number is less than or equal to $to_compare_with.
    }
    return !($target_number > $to_compare_with);
  }

  /**
   * Validate all fields are either all filled or empty, not only some of them are filled.
   * @param  string $value  ignored in this function
   * @param  string $fields all fields for validation
   * @return bool   TRUE if all filler or empty, or FALSE if only some of them are filled
   */
  function required_all_fields($value, $fields) {
    $fields = explode(".", $fields);
    $filled = $count = count($fields);
    foreach ($fields as $field) {
      $temp = $this->CI->input->post($field);
      if (empty($temp) OR (is_numeric($temp) && (0 == ((double) $temp)))) {
        $filled--;
      }
    }
    return (($filled === $count) OR ($filled === 0));
  }
}
