<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Auction helper
 */

/**
 * Sitemap
 * @return array
 */
if (!function_exists('auction_sitemap'))
{
  function auction_sitemap()
  {
    $CI =& get_instance();
    $CI->load->model('auction/auction_model');
    $link_array = array();
    $auction = $CI->auction_model->get_auction();
    if ($auction && $auction->id > 0) {
      $rows = $CI->auction_model->list_items( $auction->id );
      foreach ($rows as $row) {
        $link_array[] = array(
          'slug' => $row->slug,
          'title' => $row->name,
        );
      }
    }
    return $link_array;
  }
}

/**
 * Validate auction bid
 * TODO: fix this custom form falidation
 */
function validate_bid( $str )
{
  $CI =& get_instance();
  $CI->load->library('session');
  $CI->load->library('form_validation');
  $CI->load->config('auction/auction', TRUE);
  $CI->load->model('model__global', 'model');
  $CI->load->model('auction/auction_model');

  $amount = (float)$str;
  if ($amount == 0) {
    $CI->form_validation->set_message('validate_bid', 'Your bid %s is invalid.');
    return FALSE;
  }
  $item_slug = trim($CI->input->post('item'));
  $valid_status = $CI->auction_model->validate_bid($item_slug, $amount);
  if ($valid_status != 1) {
    switch ($valid_status) {
      case -1:
        $error_msg = 'Your bid %s is invalid. Please enter a new one.';
      case -2:
        $error_msg = 'The auction item was not found.';
        break;
      case -3:
        $error_msg = 'Your bid must be greater than the minimum bid.';
        break;
      case -4:
        $error_msg = 'Your bid must be greater than the previous high bid plus minimum increament.';
        break;
      default:
        $error_msg = 'Your bid %s is invalid. Please enter a new one.';
    }
    $CI->form_validation->set_message('validate_bid', $error_msg);
    return FALSE;
  }
  return TRUE;
}
