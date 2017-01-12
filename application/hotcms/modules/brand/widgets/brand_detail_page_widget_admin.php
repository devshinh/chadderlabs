<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Brand_detail_page_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('brand/brand', TRUE);
    //$this->load->model('brand/brand_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // process form post back
    if (array_key_exists('postback', $args)) {
      $settings = array();

      $settings['title'] = trim($args['title']);
      return $settings;
    }

    // build the form
    $data['title'] = array(
      'name'  => 'title',
      'id'    => 'title',
      'type'  => 'text',
      'value' => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
    );

    // load widget view
    return $this->render('widget_brand_detail_page_admin', $data);
  }

}
?>