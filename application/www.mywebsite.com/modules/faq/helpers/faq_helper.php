<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FAQ helper
 */

/**
 * Displays FAQs
 */
if (!function_exists('faq_list'))
{
  function faq_list( $args=array() )
  {
    $CI =& get_instance();
    $CI->load->library('session');
    $CI->load->config('faq/faq', TRUE);
    $CI->load->model('model__global', 'model');
    $CI->load->model('faq/model_faq');
    
    $data['message'] = $CI->session->flashdata('message');
    $data['error'] = $CI->session->flashdata('error');

    $data['faqs'] = $CI->model_faq->list_faqs();
    $data['faq_groups'] = $CI->model_faq->list_faq_groups();
    
    // load module view
    return $CI->load->view('faq/index', $data, TRUE);
  }
  
}