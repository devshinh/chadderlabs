<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retailer helper
 */

/**
 * Update retailer permission using Ajax
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_access_add_ajax'))
{
  function retailer_access_add_ajax($retailer_id, $permission_key)
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
    $result = $CI->retailer_model->add_retailer_permission((int)$retailer_id, $permission_key);
    $json['result'] = $result;
    return $json;
  }
}
/**
 * Update retailer permission using Ajax
 * The URL to get here is /ajax/retailer/access/retailer_id/access_code
 */
if (!function_exists('retailer_access_del_ajax'))
{
  function retailer_access_del_ajax($retailer_id, $permission_key)
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
    $result = $CI->retailer_model->delete_retailer_permission((int)$retailer_id, $permission_key);
    $json['result'] = $result;
    return $json;
  }
}

