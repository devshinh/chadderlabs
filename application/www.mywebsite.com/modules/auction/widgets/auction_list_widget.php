<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('auction/auction', TRUE);
    $this->load->model('auction/auction_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'auction');
    $data['css'] = $this->config->item('css', 'auction');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Auction Item List';

    // check permissions
    // unregistered users can view auctions, but should not be able to bid on auctions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return '<p>You do not have permission to access auctions.</p>';
    }

    // for now we only show one auction at a time. load the latest one.
    //if (is_array($args) && count($args) > 0 && array_key_exists('auction_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      $auction = $this->auction_model->get_auction();
      if ($auction && $auction->id > 0) {
        $data['auction'] = $auction;
        //$data['categories'] = $this->auction_model->list_categories(TRUE);
        $data['items'] = $this->auction_model->list_items( $auction->id );
        foreach ($data['items'] as $item){
          $data['items_images'][$item->id] = $this->auction_model->list_items_images( $item->id );
        }
        // load widget view
        return $this->render('gallery', $data);
      }

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return '<p>Auction not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>