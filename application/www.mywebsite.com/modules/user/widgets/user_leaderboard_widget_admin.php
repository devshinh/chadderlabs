<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_leaderboard_widget_admin extends Widget {

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
      return '<p>You do not have permission to manage users.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['widget_type'] = trim($args['widget_type']);
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
    if (isset($args['widget_type'])) {
      $saved_type = $args['widget_type'];
    }
    $data['widget_type_main'] = array(
        'name' => 'widget_type',
        'id' => 'widget_type_main',
        'value' => 'main',
        'checked' => $saved_type == 'main' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['widget_type_home'] = array(
        'name' => 'widget_type',
        'id' => 'widget_type_home',
        'value' => 'home',
        'checked' => $saved_type == 'home' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );

    // load widget view
    return $this->render('user_leaderboard_admin', $data);
  }

}
?>