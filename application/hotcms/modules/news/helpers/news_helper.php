<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * News helper
 */

/**
 * Cron jobs
 */
if (!function_exists('news_cron'))
{
  function news_cron()
  {
    $CI =& get_instance();
    $CI->load->model('news/news_model');
    $CI->news_model->news_schedule_run();
  }
}

/**
 * Sitemap
 * @return array
 */
if (!function_exists('news_sitemap'))
{
  function news_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('news/news_model');
    $link_array = array();
    $category_id = 1;
    $rows = $CI->news_model->list_all_news($category_id, TRUE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'title' => $row->title,
      );
    }
    return $link_array;
  }
}
