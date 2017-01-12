<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retailer_user_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('retailer/retailer', TRUE);
    
    $this->load->helper('retailer/retailer', TRUE);
    
    $this->load->helper('asset/asset');
    
    $data = array();
    $data['js'] = $this->config->item('js', 'retailer');
    $data['css'] = $this->config->item('css', 'retailer');
    $data['environment'] = $this->config->item('environment');
    //$module_title = 'Badges List';

    // check permissions
    if (!has_permission('view_content')) {
      //return array('content' => '<p>You do not have permission to access brands.</p>');
    }

    //if (is_array($args) && count($args) > 0 && array_key_exists('brand_id', $args)) {
    if (is_array($args)) {
      if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
      if (array_key_exists('user_id', $args)) {
        $user_id = $args['user_id'];
      }else{
        $user_id = (int)($this->session->userdata("user_id"));
      }
     
      $retailers_info = retailer_user($user_id);
          if(!empty($retailers_info->logo_image_id)){
            $retailers_info->logo = asset_load_item($retailers_info->logo_image_id);            
          }    
          if(!empty($retailers_info->store_id) && $retailers_info->store_id != 9999){
            $retailers_info->store_details = retailer_store_details($retailers_info->store_id);            
          }           
      $data['retailers_info'] = $retailers_info;
 
      return array('content' => $this->render('widget_retailer_user', $data));          
      
    }else{
      // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
      return array('content' => '<p>Product not found.</p>');
    }

    if ($data['environment'] == 'admin_panel') {
      return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
    }
  }

}
?>