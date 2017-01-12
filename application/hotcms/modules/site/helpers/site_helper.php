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
    $CI->load->model('site/site_model');
    $sites_array = array();
    $rows = $CI->site_model->get_all_sites();
    foreach ($rows as $row) {
      $sites_array[$row->id] = $row;
    }
    return $sites_array;
  }
}

