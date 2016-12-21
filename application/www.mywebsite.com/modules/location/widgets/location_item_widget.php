<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Location_item_widget extends Widget {

  public function run($args = array()) {
    $this->load->config('location/location', TRUE);
    $this->load->model('location/location_model');

    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'location');
    $data['css'] = $this->config->item('css', 'location');
    $module_title = 'Location Detail';

    // check permission
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_locations')) {
      return '<p>You do not have permission to access location.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
      $args['slug'] = CmsNews::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      if ($slug > '') {
        // is this a preview?
        $preview = get_cookie('preview_location');
        if ($preview > '' && strpos($preview, ':') > 0) {
          $ids = explode(':', $preview);
          $location_id = $ids[0];
          $rev_id = $ids[1];
          $rev_slug = $ids[2];
          if ($rev_slug == $slug) {
            if ($rev_id > 0) {
              // preview a revision
              $location = new CmsNews($location_id);
              $item = $location->get_revision($rev_id);
              if ($item) {
                $item->title .= ' [revision: ' . date('Y-m-d H:i:s', $item->create_timestamp) . ']';
              }
            } else {
              // preview a draft
              $location = new CmsNews($location_id);
              $item = $location->draft;
              if ($item) {
                $item->title .= ' [draft preview]';
              }
            }
          } else {
            $item = new CmsNews($slug, TRUE);
            var_dump($item);
          }
        } else {
          // load a published item
          $location = $this->location_model->get_location_by_slug($slug);
          if (!empty($location)) {
            $data['location_users'] = $this->location_model->get_users_for_location($location->id);
            foreach ($data['location_users'] as $user) {
              $data['location_user_avatar'][$user->id] = $this->location_model->get_user_avatar($user->avatar_id);
            }
            $this->load->model('operation_hours/operation_hours_model');
            $data['location_hours'] = $this->operation_hours_model->get_hours_by_connection('location', $location->id);
            $data['location'] = $location;
          }
        }
        if ($location) {
          $data['item'] = $location;
          if ($location->id == 0) {
            // item not found. set 404 status
                  $this->output->set_status_header('404');
                  redirect('page-not-found');
            return '<p>Location not found.</p>';
          }
          // load widget view
          //var_dump($location);
          //die();
          return array(
              'meta_subtitle' => $location->page_location_title,
              'content' => $this->render('location_item', $data),
          );
        }
      }

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      //return $this->render('404', $data);
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>