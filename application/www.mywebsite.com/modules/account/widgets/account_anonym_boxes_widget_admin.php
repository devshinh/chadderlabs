<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_anonym_boxes_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');

    $this->load->config('account/account', TRUE);

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();

      $settings['title'] = trim($args['title']);
      return $settings;
    }

//    // build the form
//    $data['title'] = array(
//      'name'  => 'title',
//      'id'    => 'title',
//      'type'  => 'text',
//      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
//    );

    // load widget view
    return $this->render('widget_anonym_boxes_admin', $data);
  }

}
?>