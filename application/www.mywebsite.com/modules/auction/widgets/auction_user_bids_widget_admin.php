<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction_user_bids_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('auction/auction', TRUE);
    $this->load->model('auction/auction_model');
    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('manage_auction')) {
      return '<p>You do not have permission to manage auctions.</p>';
    }

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
    return $this->render('auction_user_bids_admin', $data);
  }

}
?>