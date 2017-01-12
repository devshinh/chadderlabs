<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Account helper
 */

/**
 * Extend current period.
 * The URL to get here is /ajax/account/session/$esfp_key.$period_id/$last_n_seconds
 */
if (!function_exists('account_session_ajax'))
{
  function account_session_ajax($keys, $last_n_seconds)
  {
    $CI =& get_instance();
    $result = $CI->account_model->extend_session_for_period($keys, $last_n_seconds);
    switch ($result) {
      case 1:
        return "too short"; // keys length is not long enough
      case 2:
        return "not found"; // no row found by keys
      case 3:
        return "op error"; // database update error
      case 0:
        return $last_n_seconds;
      default :
        return $keys;
    }
  }
}

/**
 * Get user information
 */
if (!function_exists('account_get_user'))
{
  function account_get_user($user_id)
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->get_user($user_id);
  }
}

/**
 * Get user information by screenname
 */
if (!function_exists('account_get_user_by_screen_name'))
{
  function account_get_user_by_screen_name($screen_name)
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->get_user_by_screename($screen_name);
  }
}
/**
 * Get points from a user account
 */
if (!function_exists('account_get_points'))
{
  function account_get_points($user_id, $type = 'current')
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->get_user_points($user_id, $type);
  }
}

/**
 * Add points to a user account
 */
if (!function_exists('account_add_points'))
{
  function account_add_points($user_id, $points, $type, $ref_table = '', $ref_id = 0, $description = '')
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->add_user_points($user_id, $points, $type, $ref_table, $ref_id, $description);
  }
}

/**
 * Refund points to a user account
 */
if (!function_exists('account_refund_points'))
{
  function account_refund_points($user_id, $ref_table, $ref_id, $points)
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->refund_user_points($user_id, $ref_table, $ref_id, $points);
  }
}

/**
 * Add contest entries to a user account
 */
if (!function_exists('account_add_contest_entries'))
{
  function account_add_contest_entries($user_id, $ce, $type, $ref_table = '', $ref_id = 0, $description = '')
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    return $CI->account_model->add_user_ce($user_id, $ce, $type, $ref_table, $ref_id, $description);
  }
}


/**
 * List of retailers
 */
if (!function_exists('account_retailers'))
{
  function account_retailers($country_code = '')
  {
    static $country;
    static $retailers;
    if (isset($retailers) && isset($country) && $country == $country_code) {
      return $retailers;
    }
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $country = $country_code;
    $retailers = $CI->account_model->list_retailers($country);
    return $retailers;
  }
}

/**
 * List of provinces
 */
if (!function_exists('account_provinces'))
{
  function account_provinces($country_code = '')
  {
    static $country;
    static $provinces;
    if (isset($provinces) && isset($country) && $country == $country_code) {
      return $provinces;
    }
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $country = $country_code;
    $provinces = $CI->account_model->list_provinces($country);
    return $provinces;
  }
}

/**
 * List of stores
 */
if (!function_exists('account_stores'))
{
  function account_stores($retailer_id = 0, $province ='')
  {
    static $retailer;
    static $stores;
    if (isset($stores) && isset($retailer) && $retailer == $retailer_id) {
      return $stores;
    }
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $retailer = $retailer_id;
    $stores = $CI->account_model->list_stores($retailer,$province);
    return $stores;
  }
}

/**
 * List of job titles
 */
if (!function_exists('account_job_titles'))
{
  function account_job_titles()
  {
    $job_titles = array(
      'Sales Associate' => 'Sales Associate',
      'Customer Service' => 'Customer Service',
      'HR' => 'HR',
      'Manager' => 'Manager',
      'Owner' => 'Owner',
      'Brand Representative' => 'Brand Representative',
      'Marketing' => 'Marketing',
      'Other' => 'Other',
    );
    return $job_titles;
  }
}

/**
 * List of employments
 */
if (!function_exists('account_employments'))
{
  function account_employments()
  {
    $list = array(
      'Full-Time' => 'Full-Time',
      'Part-Time' => 'Part-Time',
      'Contract' => 'Contract',
      'Other' => 'Other',
    );
    return $list;
  }
}

/**
 * List of retailers using Ajax
 * The URL to get here is /ajax/account/retailers/country_code
 */
if (!function_exists('account_retailers_ajax'))
{
  function account_retailers_ajax($country_code)
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
      'content' => '',    // optional output parameter, include when needed
      'retailers' => '',  // dynamic output parameter, include when needed
    );
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $results = $CI->account_model->list_retailers($country_code);
    $json['result'] = TRUE;
    //$json['retailers'] = array('' => '');
    foreach ($results as $v) {
      $json['retailers'][$v->name] = array('name' => $v->name, 'id' => $v->id);
    }
    $json['retailers']['Other'] = array('name' => 'Other', 'id' => '99999');
    return $json;
  }
}

/**
 * List of stores using Ajax
 * The URL to get here is /ajax/account/stores/retailer_id
 */
if (!function_exists('account_stores_ajax'))
{
  function account_stores_ajax($retailer_id, $province_code)
  {
    $json = array(
      'result' => TRUE,   // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
      'content' => '',    // optional output parameter, include when needed
      'stores' => '',     // dynamic output parameter, include when needed
    );
    if ($retailer_id == '99999') {
      $json['stores']['Other'] = array('name' => 'Other', 'id' => '99999');
      return $json;
    }
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $results = $CI->account_model->list_stores($retailer_id, $province_code);
    //$json['stores'] = array('' => '');
    foreach ($results as $v) {
        if(empty($v->store_num)){
          $json['stores'][$v->store_name] = array('name' => $v->store_name, 'id' => $v->id);            
        }else{
          $json['stores'][$v->store_name] = array('name' => $v->store_name . ' (' . $v->store_num . ')', 'id' => $v->id);
        }
    }
    $json['stores']['Other'] = array('name' => 'Other', 'id' => '99999');
    return $json;
  } 
}

/**
 * List of provinces using Ajax
 * The URL to get here is /ajax/account/provinces/country_code
 */
if (!function_exists('account_provinces_ajax'))
{
  function account_provinces_ajax($country_code)
  {
    $json = array(
      'result' => TRUE,   // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
      'content' => '',    // optional output parameter, include when needed
      'stores' => '',     // dynamic output parameter, include when needed
    );
    if ($country_code == '99999') {
      $json['provinces'] = array('99999' => 'Other');
      return $json;
    }
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $results = $CI->account_model->list_provinces($country_code);
    /*
    if($country_code == 'US'){
      $json['provinces'] = array('-1' => 'Please select your state');
    }else{
      $json['provinces'] = array('-1' => 'Please select your province');
    }
*/
    foreach ($results as $v) {
      $json['provinces'][$v->province_code] = $v->province_name;
    }
    //$json['provinces'][99999] = 'Other';
    return $json;
  }  
}

/**
 * Add badge to user account
 * 
 * @param  int  user_id
 * @param  string badge name
 * 
 * @return bool
 */

if (!function_exists('account_add_badge')) {

    function account_add_badge($id_user, $badge_name, $extra_info = '') {
        
        $CI =& get_instance();
        $CI->load->config('user/user', TRUE);
        $CI->load->model('user/user_model');
        
        $CI->user_model->add_badge($id_user, $badge_name, $extra_info);
    }
}
/**
 * Get badges for user account
 * 
 * @param  int  user_id
 * 
 * @return badges
 */
if (!function_exists('account_get_badges'))
{
  function account_get_badges($user_id)
  {
    $CI =& get_instance();
    $CI->load->model('account/account_model');
    $CI->load->model('badge/badge_model');
    $badges_id = $CI->account_model->get_account_badges($user_id);
    $badges = '';
    foreach ($badges_id as $b_id){
         $badge = $CI->badge_model->badge_load($b_id->ref_id);
            if($badge->icon_image_id != 0){  
              $badge->icon = asset_load_item($badge->icon_image_id);
            }
            if($badge->big_image_id != 0){  
              $badge->hover = asset_load_item($badge->big_image_id);
            }    
             
            $badges[$b_id->ref_id] = $badge;
    }    
    return $badges;
    
  }
}

/**
 * Get registered sites for user account
 * 
 * @param  int   $user_id
 * @param  bool  $public_profile determine it is for public profile.
 * 
 * @return sites
 */
if (!function_exists('account_get_sites'))
{
  function account_get_sites($user_id, $public_profile)
  {
    $CI =& get_instance();
    $CI->load->model('site/site_model');
    
    $sites = $CI->site_model->get_all_sites_for_account($user_id, $public_profile);
    
    foreach ($sites as $site){
            if($site->site_image_id != 0){  
              $site->image = asset_load_item($site->site_image_id);
            }
    }    
    return $sites;
    
  }
}
  

