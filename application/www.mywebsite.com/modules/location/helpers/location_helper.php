<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Location helper
 */

/**
 * Sitemap
 * @return array
 */
if (!function_exists('location_sitemap'))
{
  function location_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('location/location_model');
    $link_array = array();
    $rows = $CI->news_model->list_all_location(FALSE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'title' => $row->title,
      );
    }
    return $link_array;
  }
}
