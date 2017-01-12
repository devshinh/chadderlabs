<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_forms_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('account/account', TRUE);
    $this->load->model('account/account_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_account');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage account.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['form_type'] = trim($args['form_type']);
      $settings['welcome_text'] = trim($args['welcome_text']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );
    $saved_type = '';
    if (isset($args['form_type'])) $saved_type = $args['form_type'];
    $data['form_type_login'] = array(
      'name'        => 'form_type',
      'id'          => 'form_type_login',
      'value'       => 'login',
      'checked'     => $saved_type =='login' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );    
    $data['form_type_register'] = array(
      'name'        => 'form_type',
      'id'          => 'form_type_register',
      'value'       => 'register',
      'checked'     => $saved_type =='register' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );   
    
    $data['form_type_register_home'] = array(
      'name'        => 'form_type',
      'id'          => 'form_type_register_home',
      'value'       => 'homepage_register',
      'checked'     => $saved_type =='homepage_register' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      ); 
    
    $data['form_type_register_brand'] = array(
      'name'        => 'form_type',
      'id'          => 'form_type_register_brand',
      'value'       => 'brand_register',
      'checked'     => $saved_type =='brand_register' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );      
    
    $data['welcome_text'] = array(
      'name'  => 'welcome_text',
      'id'    => 'welcome_text',
      'type'  => 'text',
      'rows'  => '5',
      'cols'  => '50',
      'value' => array_key_exists('welcome_text', $args) ? set_value( 'welcome_text', $args['welcome_text'] ) : NULL,
    );    


    // load widget view
    return $this->render('account_forms_admin', $data);
  }

}
?>