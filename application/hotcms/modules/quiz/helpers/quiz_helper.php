<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Quiz helper
 */

/**
 * Cron jobs
 */
if (!function_exists('quiz_cron'))
{
  function quiz_cron()
  {
    $CI =& get_instance();
    $CI->load->model('quiz/quiz_model');
    $CI->quiz_model->quiz_schedule_run();
  }
}

/**
 * Sitemap
 * @return array
 */
if (!function_exists('quiz_sitemap'))
{
  function quiz_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('quiz/quiz_model');
    $link_array = array();
    $category_id = 1;
    $rows = $CI->quiz_model->list_all_quiz($category_id, TRUE);
    foreach ($rows as $row) {
      $link_array[] = array(
        'slug' => $row->slug,
        'title' => $row->name,
      );
    }
    return $link_array;
  }
}
  
/**
 * Number of quizzes
 * @return int
 */
if (!function_exists('quiz_get_number_of_user_quizzes'))
{
  function quiz_get_number_of_user_quizzes($user_id)
  {
    $CI =& get_instance();
    $CI->load->model('quiz/quiz_model');
    $row = $CI->quiz_model->get_number_of_user_quizzes($user_id);

    if($row->count > 0){
        return $row->count;
    }else{
      return 0;
    }
  }  
}
