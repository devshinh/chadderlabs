<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Quote helper
 */

/**
 * Cron jobs
 */
if (!function_exists('quote_cron'))
{
  function quote_cron()
  {
//    $CI =& get_instance();
//    $CI->load->model('quote/quote_model');
//    $CI->quote_model->quote_schedule_run();
  }
}

/**
 * Sitemap
 * @return array
 */
if (!function_exists('quote_sitemap'))
{
  function quote_sitemap()
  {
//    $CI =& get_instance();
//    $CI->load->model('quote/quote_model');
//    $link_array = array();
//    $category_id = 1;
//    $rows = $CI->quote_model->list_all_quote($category_id, TRUE);
//    foreach ($rows as $row) {
//      $link_array[] = array(
//        'slug' => $row->slug,
//        'title' => $row->title,
//      );
//    }
//    return $link_array;
  }
}
