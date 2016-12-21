<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Produc helper
 */

/**
 * Sitemap
 * @return array
 */
if (!function_exists('product_sitemap'))
{
  function product_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('product/product_model');
    $link_array = array();
    $category_id = 1;
    $rows = $CI->product_model->list_products($category_id, TRUE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'title' => $row->name,
      );
    }
    return $link_array;
  }
}
