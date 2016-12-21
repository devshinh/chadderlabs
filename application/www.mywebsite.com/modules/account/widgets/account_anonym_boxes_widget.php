<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account_anonym_boxes_widget extends Widget {

  public function run($args = array())
  {

    $this->load->config('account/account', TRUE);    
    $this->load->library('account/CmsAccount');
    $data = array();
    $data['environment'] = $this->config->item('environment');
    $data['js'] = $this->config->item('js', 'account');
    $data['css'] = $this->config->item('css', 'account');
    $module_title = 'Boxes for anonym user';

    // check permission
    $data['userid'] = (int)($this->session->userdata("user_id"));
    //if (!has_permission('view_account')) {
      //return '<p>You do not have permission to access account.</p>';
    //}

    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      else {
        $data['title'] = '';
      }
      if (array_key_exists('welcome_text', $args)) {
        $data['welcome_text'] = $args['welcome_text'];
      }        
      else {
        $data['welcome_text'] = '';
      }

      $data['error'] = $this->session->flashdata('error');
      
      // load widget view         

          if ($this->ion_auth->logged_in()) {
            return array('content' => '');
          }
        //$data['register'] = CmsAccount::register_brand();

        return array('content' => $this->render('widget_anonym_boxes', $data));
            

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return '<p>Widget not found.</p>';
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
    
  }

}

?>
