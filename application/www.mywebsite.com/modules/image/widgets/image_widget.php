<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_widget extends Widget {

  public function run( $args=array() )
  {
    $this->load->config('image/image', TRUE);
    $module_titile = $this->config->item('module_title', 'image');
    $data['environment'] = $this->config->item('environment');
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      $data['asset_category_id'] = $asset_category_id;
      if (array_key_exists('link', $args) && array_key_exists('link_title', $args)) {
        $data['link'] = $args['link'];
        $data['link_title'] = $args['link_title'];        
      }
      if (array_key_exists('link_blank', $args)) { 
          $data['link_blank'] = $args['link_blank'];
      }else{
          $data['link_blank'] = 0;
      }
      
      if (array_key_exists('for_logged_users', $args)) { 
          $data['for_logged_users'] = $args['for_logged_users'];
      }else{
          $data['for_logged_users'] = 0;
      }      
      //var_dump($data['for_logged_users']);die();
      if (array_key_exists('asset_id', $args)) {
          if($data['for_logged_users']==1 && !$this->ion_auth->logged_in() ){
              return array('content' => (''));
          }
        $asset_id = (int)($args['asset_id']);
        $data['asset_id'] = $asset_id;
        if ($asset_id > 0) {
          $this->load->helper('asset/asset');
          $image = asset_load_item( $asset_id );
          $data['image'] = $image;
          return array('content' =>$this->render('image', $data));
        }
      }
    }
    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_titile . ' widget.<br />Click here to edit.</p>';
    }
  }

}
?>