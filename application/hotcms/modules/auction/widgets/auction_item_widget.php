<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auction_item_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('auction/auction', TRUE);
    $this->load->model('auction/auction_model');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'auction');
    $data['css'] = $this->config->item('css', 'auction');
    $module_titile = 'Auction Item Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return '<p>You do not have permission to access auctions.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $slug = $this->auction_model->get_random_itemslug();
      $args['slug'] = $slug;
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $item_slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');
      $data['message'] = $this->session->userdata('bid_confirm');
      $this->session->set_userdata('bid_confirm', '');

      if ($item_slug > '') {
        $item = $this->auction_model->get_item( $item_slug );
        if ($item) {
          $data['item'] = $item;
          $auction = $this->auction_model->get_auction( $item->auction_id );
          $data['auction'] = $auction;

          // bidding form
          if (has_permission('access_auction')) {
            if (array_key_exists('postback', $args) && is_array($args['postback'])) {
              $posts = $args['postback'];
              // Validation rules
              // TODO: test form validation in widgets
              $this->form_validation->set_rules('bid', 'Bid', 'trim|required|callback_validate_bid|xss_clean');
              $bid = $posts['bid'];
              //$group_id = (int)($posts['group_id']);
              $validated = $this->form_validation->run();
              if ($validated) {
                $result = $this->auction_model->place_bid($item->id, $data['userid'], (float)$bid);
                if ($result && $result > 0) {
                  $this->session->set_userdata('bid_confirm', '<p>Your bid has been placed successfully.</p>');
                  redirect('/gallery/' . $item_slug, 'refresh');
                  return;
                }
                else {
                  switch ($result) {
                    case -2:
                      $error_msg = 'The auction item was not found.';
                      break;
                    case -3:
                      $error_msg = 'Your bid must be greater than the minimum bid. Please try a higher value.';
                      break;
                    case -4:
                      $error_msg = 'Your bid must be greater than the current high bid plus minimum increament.';
                      break;
                    case -5:
                      $error_msg = 'Your bid exceeds the maximum bid amount.';
                      break;
                    case -1:
                    default:
                      $error_msg = 'Invalid bid. Please try again.';
                  }
                  //$this->session->set_flashdata($error_msg);
                  $data['error'] = $error_msg;
                  //redirect('/gallery/' . $item_slug, 'refresh');
                  //return;
                }
              }
              else {
                // return validation errors
                $ve = validation_errors();
                if ($ve > '') {
                  $data['error'] = $ve;
                }
              }
            }

            // build the form
            $data['hidden_fields'] = array('item' => $item->slug);
            $data['bid_field'] = array(
              'name'  => 'bid',
              'id'    => 'bid',
              'type'  => 'text',
              'value' => $this->form_validation->set_value('bid'),
            );
          }

          $assets = $this->auction_model->list_item_assets( $item->id );
          $bids = $this->auction_model->list_item_bids( $item->id );
          $data['assets'] = $assets;
          $data['bids'] = $bids;
          // load widget view
          return $this->render('detail', $data);
        }
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      $result = '<p>Auction item not found.</p>';
      // and list all available items
      $auction = $this->auction_model->get_auction();
      if ($auction && $auction->id > 0) {
        $result .= '<p>Please check out our available items.</p><ul>';
        $items = $this->auction_model->list_items( $auction->id );
        foreach ($items as $item) {
          // TODO: add images here
          //$data['items_images'][$item->id] = $this->auction_model->list_items_images( $item->id );
          $result .= '<li><a href="/gallery/' . $item->slug .'">' . $item->name . '</a></li>';
        }
        $result .= '</ul>';
      }
      return $result;
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_titile . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>