<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * News helper
 */

/**
 * Cron jobs
 */
if (!function_exists('location_cron'))
{
  function news_cron()
  {
    $CI =& get_instance();
    $CI->load->model('location/location_model');
    $CI->news_model->news_schedule_run();
  }
}

/**
 * Sitemap
 * @return array
 */
if (!function_exists('location_sitemap'))
{
  function news_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('location/location_model');
    $link_array = array();
    $rows = $CI->location_model->get_all_locations(FALSE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'name' => $row->name,
      );
    }
    return $link_array;
  }
}
