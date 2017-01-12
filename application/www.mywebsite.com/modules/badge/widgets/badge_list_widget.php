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
    $data['js'] = $this->config->item('js', 'brand');
    $data['css'] = $this->config->item('css', 'brand');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Brand List';

    // check permissions
    // unregistered users can view brands, but should not be able to bid on brands
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access brands.</p>');
    }

   if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
       //$screen_name = $args['slug'];

     if (is_array($args)) {
        if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
        if (array_key_exists('slug', $args)) {
          $screen_name = $args['slug'];
        }         
      
      if(!empty($screen_name)){
         $user = account_get_user_by_screen_name($screen_name);
         if(empty($user)){
                   $this->output->set_status_header('404');
                   redirect('page-not-found');
             return array('content' => '<p>User not found.</p>');
         }
         $user_badges = account_get_badges($user->user_id);
         $data['screen_name']=$screen_name;
      }else{
       $user_badges = account_get_badges($data['userid']);
       $user = account_get_user($data['userid']);
       $data['screen_name']= $user->screen_name;
      }
      
      if(!empty($user_badges)){
        $data['user_badges'] = $user_badges;
      }
      
      $badges = get_all_badges();
      //load images for user badges
      foreach($badges as $badge){
            if($badge->icon_image_id != 0){  
              $badge->icon = asset_load_item($badge->icon_image_id);
            }
            if($badge->big_image_id != 0){  
              $badge->hover = asset_load_item($badge->big_image_id);
            }              
      }
      $data['all_badges'] = $badges;
      
      return array('content' => $this->render('widget_badge_list_user', $data), 'meta_subtitle' => ucfirst($user->screen_name));          
      

      // if anything goes wrong, return 404
            $this->output->set_status_header('404');redirect('page-not-found');
      return array('content' => '<p>Page not found.</p>');
    }
   }else{
       //no slug set
      $badges = get_all_badges();
      //load images for user badges
      foreach($badges as $badge){
            if($badge->icon_image_id != 0){  
              $badge->icon = asset_load_item($badge->icon_image_id);
            }
            if($badge->big_image_id != 0){  
              $badge->hover = asset_load_item($badge->big_image_id);
            }              
      }
      $data['all_badges'] = $badges;       

      return array('content' => $this->render('widget_badge_list_user', $data));        
   }
    if ($data['environment'] == 'admin_panel') {
      return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
    }
  }

}
?>