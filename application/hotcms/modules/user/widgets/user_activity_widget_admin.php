<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_activity_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('user/user', TRUE);
    $this->load->model('user/user_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_user');
    if (!$data['has_permission']) {
      //return '<p>You do not have permission to manage users.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['limit'] = (int)$args['limit'];
      $settings['site_restricted'] = trim($args['site_restricted']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );
    $data['limit'] = array(
      'name'  => 'limit',
      'id'    => 'limit',
      'type'  => 'text',
      'value' => array_key_exists('limit', $args) ? set_value( 'limit', $args['limit'] ) : NULL,
    );
    $restricted = false;
    if(isset($args['site_restricted']) && $args['site_restricted'] == '1') 
        $restricted = TRUE;
    $data['site_restricted'] = array(
        'name' => 'site_restricted',
        'id' => 'site_restricted',
        'checked' => $restricted,
        'value' => '1'
    );    


    // load widget view
    return $this->render('user_activity_admin', $data);
  }

}
?>