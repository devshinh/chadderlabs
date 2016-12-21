<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction_item_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('auction/auction', TRUE);
    $this->load->model('auction/auction_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    $data['has_permission'] = has_permission('manage_auction');
    if (!$data['has_permission']) {
      return '<p>You do not have permission to manage auctions.</p>';
    }

    // Validation rules
    // TODO: add form validation
    //$this->form_validation->set_rules('auction_id', 'Auction', 'trim|required|xss_clean');

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
    return $this->render('auction_item_admin', $data);
  }

}
?>