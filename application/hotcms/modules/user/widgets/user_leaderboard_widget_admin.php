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
      $settings['widget_points_or_entries'] = trim($args['widget_points_or_entries']);
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

    $saved_type = '';
    if (isset($args['widget_type'])) {
      $saved_type = $args['widget_type'];
    }
    $saved_reward = '';
    if (isset($args['widget_points_or_entries'])) {
      $saved_reward = $args['widget_points_or_entries'];
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
    
    $data['widget_points'] = array(
        'name' => 'widget_points_or_entries',
        'id' => 'widget_points',
        'value' => 'points',
        'checked' => $saved_reward == 'points' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['widget_entries'] = array(
        'name' => 'widget_points_or_entries',
        'id' => 'widget_entries',
        'value' => 'entries',
        'checked' => $saved_reward == 'entries' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    
    $restricted = false;
    if(isset($args['site_restricted']) && $args['site_restricted'] == '1') $restricted = TRUE;
    $data['site_restricted'] = array(
        'name' => 'site_restricted',
        'id' => 'site_restricted',
        'checked' => $restricted,
        'value' => '1'
    );       

    
    // load widget view
    return $this->render('user_leaderboard_admin', $data);
  }

}
?>