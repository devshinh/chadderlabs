<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_communication_preferences_widget_admin extends Widget {

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

    $data['welcome_text'] = array(
      'name'  => 'welcome_text',
      'id'    => 'welcome_text',
      'type'  => 'text',
      'rows'  => '5',
      'cols'  => '50',
      'value' => array_key_exists('welcome_text', $args) ? set_value( 'welcome_text', $args['welcome_text'] ) : NULL,
    );    

    // load widget view
    return $this->render('widget_account_communication_preferences_admin', $data);
  }

}
?>