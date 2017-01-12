<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Order helper
 */

/**
 * Update order permission using Ajax
 * The URL to get here is /ajax/order/access/order_id/access_code
 */
if (!function_exists('order_access_ajax'))
{
  function order_access_ajax($order_id, $access_code)
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
    );
    $CI =& get_instance();
    // check permission
    if (!($CI->ion_auth->logged_in()) || !has_permission('manage_order_permission')) {
      $json['messages'] = 'No permission.';
      return $json;
    }
    $CI->load->model('order/order_model');
    $result = $CI->order_model->update_order_permission((int)$order_id, $access_code);
    $json['result'] = $result;
    return $json;
  }
}

