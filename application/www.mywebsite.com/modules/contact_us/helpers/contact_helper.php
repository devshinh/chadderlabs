<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contact Us helper
 *
 * Some functions to share within HotCMS
 */

/**
 * Display and process a Contact Us form
 */
if (!function_exists('contact_us_form'))
{
  function contact_us_form( $args=array() )
  {
    $CI =& get_instance();
    $CI->load->library('session');
    $CI->load->library('form_validation');
    $CI->load->config('contact/contact', TRUE);
    $CI->load->model('model__global', 'model');
    $CI->load->model('contact/model_contact');
    
    $data['message'] = $CI->session->flashdata('message');
    $data['error'] = $CI->session->flashdata('error');
    
    // Validation rules
    $CI->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
    $CI->form_validation->set_rules('email',     'Email Address', 'trim|required|valid_email|xss_clean');
    $CI->form_validation->set_rules('postal',     'Postal Code', 'trim|required|xss_clean');
    
    $firstname = $CI->input->post('firstname');
    $lastname = $CI->input->post('lastname');
    $email = $CI->input->post('email');
    $postal = $CI->input->post('postal');
    $concerns = $CI->input->post('concerns');
    $comment = $CI->input->post('comment');
    $terms = $CI->input->post('terms');
    
    if ($CI->form_validation->run()) {
      $result = $CI->model_contact->contact_request($firstname, $lastname, $email, $postal, $concerns, $comment);
      //var_dump($result);
      //die('its running');
      $CI->session->set_flashdata('postal', $postal);
      $CI->session->set_flashdata('concerns', $concerns);
	  
      if($result) {
        if ($email>'' && $terms==1) {
          // TODO: load other helpers
          /*
          try {
            $CI->load->helper('newsletter/newsletter_signup');
            if (function_exists('newsletter_signup')) {
              $result = newsletter_signup($firstname, $lastname, $email, $postal, 'contact');
            }
          }
          catch (Exception $e) {
            $CI->session->set_flashdata('<p>Failed to subscribe to the e-newsletter.</p>');
          } */
        }
        redirect('/contact-us-confirm', 'refresh');
        return;
      }
      else {
      	$CI->session->set_flashdata('<p>Failed to submit the contact form.</p>');
      }
    }
    else {
      // return validation errors
      $ve = validation_errors();
      if ($ve>'') {
        $data['error'] = $ve;
      }
    }
    // build the form
    $data['firstname'] = array('name' => 'firstname',
                              'id'      => 'firstname',
                              'type'    => 'text',
                              'value'   => $CI->form_validation->set_value('firstname'),
                             );
    $data['lastname'] = array('name' => 'lastname',
                              'id'      => 'lastname',
                              'type'    => 'text',
                              'value'   => $lastname,
                             );
    $data['email'] = array('name' => 'email',
                              'id'      => 'email',
                              'type'    => 'text',
                              'value'   => $CI->form_validation->set_value('email'),
                             );
    $data['postal'] = array('name' => 'postal',
                              'id'      => 'postal',
                              'type'    => 'text',
                              'value'   => $CI->form_validation->set_value('postal'),
                             );
    $data['concerns'] = array('name' => 'concerns[]',
                              'type'    => 'checkbox',
                             );
    $data['concerns_default'] = (is_array($concerns) ? $concerns : array());
    $data['comment'] = array('name' => 'comment',
                              'id'      => 'comment',
                              'type'    => 'textarea',
                              'value'   => $comment,
                              'cols'    => '40',
                              'rows'    => '5',
                             );
    $data['terms'] = array('name' => 'terms',
                              'id'      => 'terms',
                              'type'    => 'checkbox',
                              'value'    => '1',
                              'checked'   => ($terms == '1'),
                             );
    // load module view
    return $CI->load->view('contact/index', $data, TRUE);
  }
  
  /*
   * another way to embed controllers using curl
   *
  function contact_us_form_curl($args=array())
  {
    $CI =& get_instance();
    $CI->load->library('curl'); 
    $output = $CI->curl->_simple_call('get', 'contactus'); 
    return $output;
  }
  */
  
}

/* End of file contact_helper.php */
/* Location: ./helpers/contact_helper.php */