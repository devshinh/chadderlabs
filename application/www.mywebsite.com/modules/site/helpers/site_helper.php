<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Site helper
 */

/**
 * Get all sites defined
 */
if (!function_exists('get_sites_array'))
{
  function get_sites_array()
  {
    $CI =& get_instance();
    $CI->load->model('site/model_site');
    $sites_array = array();
    $rows = $CI->model_site->get_all_sites();
    foreach ($rows as $row) {
      $sites_array[$row->id] = $row;
    }
    return $sites_array;
  }
}

/**
 * Get real time point balance for a site.
 * @param  int $site_id row id in site table
 * @return int point balance for a site.
 */
if (!function_exists('get_realtime_balance'))
{
  function get_realtime_balance($site_id)
  {
    $CI =& get_instance();
    $CI->load->model('site/site_model');
    return $CI->site_model->get_point_balance($site_id);
  }
}
