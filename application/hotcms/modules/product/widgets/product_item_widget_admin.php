<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_item_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('product/product', TRUE);
    $this->load->model('product/product_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_product');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage products.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('product_id', 'Product', 'trim|required|xss_clean');

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
    return $this->render('product_item_admin', $data);
  }

}
?>