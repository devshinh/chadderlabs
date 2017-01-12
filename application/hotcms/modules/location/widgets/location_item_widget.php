<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location_item_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('location/location', TRUE);

    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'location');
    $data['css'] = $this->config->item('css', 'location');
    $module_title = 'Location Detail';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_location')) {
      return '<p>You do not have permission to access location.</p>';
    }

    // in the backend Page Publisher, randomly pick an item for demonstration.
    if ($data['environment'] == 'admin_panel') {
   //   $args['slug'] = CmsNews::random_slug();
    }

    if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
      $slug = $args['slug'];
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      
      if ($slug > '') {
        // is this a preview?

        if ($item) {
          $data['item'] = $item;
          if ($item->id == 0) {
            // item not found. set 404 status
            $this->output->set_status_header('404');
            return '<p>News not found.</p>';
          }
          // load widget view
          return array(
            'meta_title' => $item->title,
            'content' => $this->render('location_item', $data),
          );
        }
      }

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return '<p>News not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>