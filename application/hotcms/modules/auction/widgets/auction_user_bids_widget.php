<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction_user_bids_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('auction/auction', TRUE);
    $this->load->model('auction/auction_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'auction');
    $data['css'] = $this->config->item('css', 'auction');
    $data['environment'] = $this->config->item('environment');
    $module_titile = 'Auction Item List';

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if ($data['userid'] < 1) {
      return '<p>Please <a href="/login">log in</a> to view your bids.</p>';
    }
    if (!has_permission('access_auction')) {
      return '<p>You do not have permission to access auctions.</p>';
    }

    if (array_key_exists('title', $args)) {
      $data['title'] = $args['title'];
    }

    $auction = $this->auction_model->get_auction();
    if ($auction && $auction->id > 0) {
      $data['auction'] = $auction;
      //$data['categories'] = $CI->auction_model->list_categories(TRUE);
      $data['items'] = $this->auction_model->list_user_bids( $data['userid'], $auction->id);
      // load widget view
      return $this->render('user_bids', $data);
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_titile . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>