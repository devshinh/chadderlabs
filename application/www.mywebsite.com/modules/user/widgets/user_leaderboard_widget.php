<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_leaderboard_widget extends Widget {

  public function run($args = array())
  {
    $this->load->config('user/user', TRUE);
    $this->load->model('user/user_model');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'user');
    $data['css'] = $this->config->item('css', 'user');
    $module_title = 'Points Leaderboard';

    // check permission
    $data['userid'] = (int) ($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access this widget.</p>');
    }

    if (is_array($args) && count($args) > 0) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }

      $data['error'] = $this->session->flashdata('error');

        if(isset($args['widget_points_or_entries'])){
            $measurement = $args['widget_points_or_entries'];
        }else {
            $measurement = 'entries';
        }      
        
        $restricted = 0;
        if (is_array($args) && array_key_exists('site_restricted', $args)) {
          $restricted = (int)$args['site_restricted'];
        } 
        //var_dump($restricted);die();        
      
      // load widget view
      if (isset($args['widget_type']) && $args['widget_type'] == 'home') {
        $template_name = 'user_leaderboard_home';
        //load user list with points
          $user_list = $this->user_model->list_top_users(10, 'all', $measurement,$restricted );
       
        
      }
      else {
        $template_name = 'user_leaderboard';
        //load user list with points
        $user_list = $this->user_model->list_top_users(20,'all', $measurement, $restricted);        
      }
      if (!empty($user_list) OR $restricted) {
        $data['users'] = $user_list;
        $data['type'] = $measurement;
        // load a published item
        return array(
          'meta_subtitle' => '',
          'content' => $this->render($template_name, $data),
        );
      }

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return array('content' => '<p>No results were found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }

}

?>
