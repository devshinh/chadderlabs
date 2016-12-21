<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retailer_detail_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    $this->load->config('retailer/retailer', TRUE);

    $data = array();
    $data['js'] = $this->config->item('js', 'retailer');
    $data['css'] = $this->config->item('css', 'retailer');
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
      
      //$retailers = get_all_retailers();
      //$data['all_retailers'] = $retailers;
 
     

      return array('content' => $this->render('widget_retailer_detail', $data));          
      

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