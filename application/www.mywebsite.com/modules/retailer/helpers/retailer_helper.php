<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retailer helper
 */

/**
 * Update retailer permission using Ajax
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_access_ajax'))
{
  function retailer_access_ajax($retailer_id, $access_code)
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
    );
    $CI =& get_instance();
    // check permission
    if (!($CI->ion_auth->logged_in()) || !has_permission('manage_retailer_permission')) {
      $json['messages'] = 'No permission.';
      return $json;
    }
    $CI->load->model('retailer/retailer_model');
    $result = $CI->retailer_model->update_retailer_permission((int)$retailer_id, $access_code);
    $json['result'] = $result;
    return $json;
  }
}
  
/**
 * Get retailer for user
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_user'))
{
  function retailer_user($user_id)
  {

    $CI =& get_instance();
    // check permission
    //if (!($CI->ion_auth->logged_in())) {
    //  $json['messages'] = 'No permission.';
    //  return $json;
    //}
    $CI->load->model('retailer/retailer_model');
    $result = $CI->retailer_model->retailer_user_load($user_id);
    return $result;
  }  
}

/**
 * Get store detail
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_store_details'))
{
  function retailer_store_details($store_id)
  {

    $CI =& get_instance();
    // check permission
    if (!($CI->ion_auth->logged_in())) {
      $json['messages'] = 'No permission.';
      return $json;
    }
    $CI->load->model('retailer/retailer_model');
    $result = $CI->retailer_model->store_load($store_id, TRUE);
    return $result;
  }  
}

/**
 * Get all 
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_organization_type'))
{
  function retailer_organization_type()
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
    );
    $CI =& get_instance();
    // check permission
    $CI->load->model('retailer/retailer_model');
    $result = $CI->retailer_model->organization_type_load_all();
    $json['result'] = $result;
    return $json;
  }
  
/**
 * List of retailers
 */
if (!function_exists('retailer_by_category'))
{
  function retailer_by_category($cat_id = '')
  {
 /*   static $cat_id;
    static $retailers;
    if (isset($retailers) && isset($cat_id) && $country == $country_code) {
      return $retailers;
    }
  */
    $CI =& get_instance();
    $CI->load->model('retailer/retailer_model');
    $retailers = $CI->retailer_model->list_retailers_by_category($cat_id);
    return $retailers;
  }
}  
}

