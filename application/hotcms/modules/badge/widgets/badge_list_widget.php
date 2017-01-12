<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Badge_list_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('brand/brand', TRUE);
    //$this->load->model('brand/brand_model');
    $this->load->helper('account/account');
    $this->load->helper('badge/badge');
    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset'); 
    $data = array();
    $data['js'] = $this->config->item('js', 'badge');
    $data['css'] = $this->config->item('css', 'badge');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Badges List';

    // check permissions
    // unregistered users can view brands, but should not be able to bid on brands
    $data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access brands.</p>');
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('brand_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      
      $badges = get_all_badges();
      $data['all_badges'] = $badges;
      $user_badges = account_get_badges($data['userid']);
      //load images for badges
      foreach($badges as $badge){
            if($badge->icon_image_id != 0){  
              $badge->icon = asset_load_item($badge->icon_image_id);
            }
            if($badge->big_image_id != 0){  
              $badge->hover = asset_load_item($badge->big_image_id);
            }              
      }
      
      $data['user_badges'] = $user_badges;

      return array('content' => $this->render('widget_badge_list_user', $data));          
      

      // if anything goes wrong, return 404
      $this->output->set_status_header('404');
      return array('content' => '<p>Product not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
    }
  }

}
?>