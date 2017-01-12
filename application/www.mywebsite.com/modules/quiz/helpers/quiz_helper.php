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
/**
 * awardsphero badge for users with 3x 100% on sphero quizzes
 */
if (!function_exists('check_sphero_badge'))
{
    //award sphero badge for users with 3x 100% on sphero quizz
   function check_sphero_badge($user_id){
      $eligible = 0;
      $CI =& get_instance();
      $CI->load->model('quiz/quiz_model');
      
      $sphero_quizzes_ids = array(36,37,44);
      $quizzes = $CI->quiz_model->get_quizzes_history_for_user($user_id);
      foreach($quizzes as $q ){
          if(in_array($q->quiz_id, $sphero_quizzes_ids)){
              if($q->correct_percent == '100'){
                  $eligible++;
              }
          }
      }
      if($eligible >= 3){
          return true;
      }else{
          return false;
      }
  }
}

/**
 * genius badge check badge for users with 3x 100% on sphero quizzes
 */
if (!function_exists('check_genius_badge'))
{
    //award sphero badge for users with 3x 100% on sphero quizz
   function check_genius_badge($user_id){
      $eligible = 0;
      $CI =& get_instance();
      $CI->load->model('quiz/quiz_model');
      

      $quizzes = $CI->quiz_model->get_quizzes_history_for_user($user_id);
      foreach($quizzes as $q ){
            if($q->correct_percent == '100'){
                $eligible++;
            }
        }

      if($eligible >= 10){
          return true;
      }else{
          return false;
      }
  }
}
