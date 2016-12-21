<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Badge helper
 */

/**
 * Get list of all badges in the system
 */
if (!function_exists('get_all_badges'))
{
  function get_all_badges()
  {
    $CI =& get_instance();
    // check permission

    $CI->load->model('badge/badge_model');
    $result = $CI->badge_model->badge_list(FALSE,100,0);
    
    return $result;
  }
}
  
/**
 * Check user badge
 * 
 * @param int user_id
 * @param string badge name
 * 
 * @return bool
 */
if (!function_exists('check_user_badge'))
{
  function check_user_badge($user_id, $badge_name)
  {
    $CI =& get_instance();
    // check permission

    $CI->load->model('badge/badge_model');
    $result = $CI->badge_model->check_user_badge($user_id, $badge_name);
    return $result;
  }  
  
}
