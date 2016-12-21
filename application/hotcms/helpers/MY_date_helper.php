<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get an array of months with number as key and localize noun as value.
 * @return array list of month
 */
if ( !function_exists("months_list")) {
  function months_list() {
    $CI =& get_instance();
    $CI->load->helper("language");
    return array(1 => lang("hotcms_jan"), 2 => lang("hotcms_feb"), 3 => lang("hotcms_mar"), 4 => lang("hotcms_apr"), 5 => lang("hotcms_may"), 6 => lang("hotcms_jun"), 7 => lang("hotcms_jul"), 8 => lang("hotcms_aug"), 9 => lang("hotcms_sep"), 10 => lang("hotcms_oct"), 11 => lang("hotcms_nov"), 12 => lang("hotcms_dec"));
  }
}

/**
 * Get an array of years with year number is both key and value.
 * @param  int   $min_year minimum year allow for the array
 * @param  int   $max_year maximum year allow for the array
 * @param  int   $number_of_years  number of years in the array
 * @return array list of years
 */
if ( ! function_exists("years_list")) {
  function years_list($min_year = 0, $max_year = 9999, $number_of_years = 20) {
    $years = array();
    $init_year = date("Y") - 10;
    if ($init_year < $min_year) {
      $init_year = $min_year;
    }
    if (($max_year - $init_year) < ($number_of_years - 1)) {
      if (($max_year - $min_year) < ($number_of_years - 1)) {
        $number_of_years = $max_year - $min_year + 1;
      } else {
        $init_year = $max_year - $number_of_years + 1;
      }
    }
    for($i = 0; $i < $number_of_years; $i++) {
      $years[$init_year + $i] = $init_year + $i;
    }
    return $years;
  }
}

/* End of file MY_date_helper.php */
/* Location: ./application/hotcms/helpers/MY_date_helper.php */