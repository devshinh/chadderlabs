<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quote_form_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('quote/quote', TRUE);
    $this->lang->load('hotcms');
    $this->load->model('quote/quote_model');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'quote');
    $data['css'] = $this->config->item('css', 'quote');
    $data['dropdown_hint'] = $this->config->item('dropdown_hint', 'quote');
    $data['lines_per_column'] = $this->config->item('lines_per_column', 'quote');
    $module_title = 'Quote';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('access_quote')) {
      return '<p>You do not have permission to access quote.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
//    if ($data['environment'] == 'admin_panel') {
//      $args['slug'] = $this->quote_model->get_random_slug();
//    }

    if (is_array($args) && count($args) > 0 && array_key_exists('form_id', $args)) {
      $form_id = (int)($args['form_id']);
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');
      $data['message'] = $this->session->userdata('bid_confirm');
      $this->session->set_userdata('bid_confirm', '');

      if ($form_id > 0) {
        $item = $this->quote_model->quote_load($form_id, NULL, TRUE);
        if ($item) {
          $data['item'] = $item;
          if ($item->id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<p>Contact not found.</p>';
          }

          // process postback
          if (array_key_exists('postback', $args)) {
            try {
              if ($args['postback']['near_location'] > 0) {
                $this->load->model('location/location_model');
                $location = $this->location_model->get_location_by_id((int)($args['postback']['near_location']));
                $email = $location->main_email;
                $this->quote_model->email_request($email, $item, $args['postback']);
              }
            }
            catch (Exception $e) {
              //$messages = 'There was an error when trying to send out notice email: ' . $e->getMessage();
            }
            $result = $this->quote_model->process_request($item, $args['postback']);
            $content = '<div id="quoteform" class="overlay">';
            if ($result) {
              $content .= '<p>Your request has been sent. Thank you for your submission!</p>';
            }
            else {
              $content .= '<p>Sorry but there was an error processing your request.</p>';
            }
            $content .= "</form></div>\n";
            return $content;
          }

          // build the genreal information form
          $data['hidden_fields'] = array();
          $data['hidden_fields']['form_id'] = $item->id;
          $data['title_array'] = array(
            '' => 'Select One',
            'Mr'  => 'Mr',
            'Mrs' => 'Mrs',
          );
          $data['location_array'] = array('' => 'Select One');
          $this->load->model('location/location_model');
          $all_locations = $this->location_model->get_all_locations();
          foreach ($all_locations as $loc) {
            $data['location_array'][$loc->id] = $loc->name;
          }

          // load widget view
          $content = '<div id="quoteform" class="overlay"><form method="post" id="quote_form">';
          $content .= $this->render('quote_info', $data);
          $content .= $this->render('quote_form', $data);
          $content .= "</form></div>\n";
          return array(
            'meta_title' => $item->name,
            'content' => $content,
          );
        }
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>Form not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>