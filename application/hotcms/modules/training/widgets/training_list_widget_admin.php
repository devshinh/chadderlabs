<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_list_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('training/training', TRUE);
    $this->load->model('training/training_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('manage_training')) {
      return '<p>You do not have permission to manage training.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('training_id', 'Training', 'trim|required|xss_clean');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['list_type'] = trim($args['list_type']);
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
    if (isset($args['list_type']))
      $saved_type = $args['list_type'];
    $data['all_labs'] = array(
        'name' => 'list_type',
        'id' => 'all_labs',
        'value' => 'all_labs',
        'checked' => $saved_type == 'all_labs' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['uncomplete_labs'] = array(
        'name' => 'list_type',
        'id' => 'uncomplete_labs',
        'value' => 'uncomplete_labs',
        'checked' => $saved_type == 'uncomplete_labs' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );
    $data['complete_lab'] = array(
        'name' => 'list_type',
        'id' => 'complete_lab',
        'value' => 'complete_lab',
        'checked' => $saved_type == 'complete_lab' ? TRUE : FALSE,
        'style' => 'margin:10px',
    );    

    // load widget view
    return $this->render('training_list_admin', $data);
  }

}
?>