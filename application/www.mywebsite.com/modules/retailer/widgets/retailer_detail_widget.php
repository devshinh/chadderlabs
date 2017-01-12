<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retailer_detail_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('retailer/retailer', TRUE);
    $this->load->model('retailer/retailer_model');
    
    $this->load->helper('asset/asset');


    $data = array();
    $data['js'] = $this->config->item('js', 'retailer');
    $data['css'] = $this->config->item('css', 'retailer');
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Retailer detail';

    // check permissions
    // unregistered users can view brands, but should not be able to bid on brands
    //$data['userid'] = (int)($this->session->userdata("user_id"));
    if (!has_permission('view_content')) {
      return array('content' => '<p>You do not have permission to access brands.</p>');
    }

   if (is_array($args) && count($args) > 0 && array_key_exists('slug', $args)) {
     if (is_array($args)) {
        if (array_key_exists('title', $args)) {
        $data['title'] = $args['title'];
      }
        if (array_key_exists('slug', $args)) {
          $retailer_slug = $args['slug'];
        }         
      
      if(!empty($retailer_slug)){
          //load retiler details
          $retailer = $this->retailer_model->get_retailer_by_slug($retailer_slug);
          if(!empty($retailer->logo_image_id)){
            $retailer->logo = asset_load_item($retailer->logo_image_id);            
          }
          $data['retailer'] = $retailer;
          return array('content' => $this->render('widget_retailer_detail', $data));          
      
      }
     }
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