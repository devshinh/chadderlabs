<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('location/location', TRUE);
    $this->load->model('location/location_model');
    $data = array();
    $data['js'] = $this->config->item('js', 'location');
    $data['css'] = $this->config->item('css', 'location');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Location Item List';

    // check permissions
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_locations')) {
      return '<p>You do not have permission to access locations.</p>';
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('news_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
        $data['items'] = $this->location_model->get_all_locations(TRUE);        
        // load widget view
        return $this->render('location_list', $data);
      //}

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