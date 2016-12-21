<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Training_preview_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('training/training', TRUE);
    $this->load->model('training/training_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_training');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage training.</p>';
    }

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['title'] = trim($args['title']);
      $settings['preview_type'] = trim($args['preview_type']);
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
    if (isset($args['preview_type'])) $saved_type = $args['preview_type'];
    $data['preview_type_featured'] = array(
      'name'        => 'preview_type',
      'id'          => 'preview_type_featured',
      'value'       => 'featured',
      'checked'     => $saved_type =='featured' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );    
    $data['preview_type_new'] = array(
      'name'        => 'preview_type',
      'id'          => 'preview_type_new',
      'value'       => 'new',
      'checked'     => $saved_type =='new' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );   
    $data['preview_type_coming_soon'] = array(
      'name'        => 'preview_type',
      'id'          => 'preview_type_comming',
      'value'       => 'coming_soon',
      'checked'     => $saved_type =='coming_soon' ? TRUE : FALSE,
      'style'       => 'margin:10px',
      );      

    // load widget view
    return $this->render('training_preview_admin', $data);
  }

}
?>