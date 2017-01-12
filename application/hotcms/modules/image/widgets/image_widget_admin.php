<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_widget_admin extends Widget {

  public function run( $args=array() )
  {
    $this->load->library('session');
    //$this->load->library('form_validation');
    //$this->load->config('image/image', TRUE);
    //$this->load->model('image/image_model');

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['asset_id'] = (int)($args['asset_id']);
      $settings['asset_category_id'] = (int)($args['asset_category_id']);
      $settings['title'] = trim($args['title']);
      $settings['link'] = trim($args['link']);
      $settings['link_title'] = trim($args['link_title']);
      $settings['link_blank'] = trim($args['link_blank']);
      $settings['for_logged_users'] = trim($args['for_logged_users']);
      return $settings;
    }

    $this->load->helper('asset/asset');

    $images = array();
    $asset_category_id = 0;
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int)($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $this->load->library('asset/asset_item');
        $images = Asset_item::list_all_images($asset_category_id);
      }
    }
    $data['asset_category_id'] = $asset_category_id;
    $data['images'] = $images;
    $asset_options = array('' => ' -- select image -- ');
    foreach ($images as $img) {
      $asset_options[$img->id] = $img->name;
    }
    $data['asset_options'] = $asset_options;

    $image = NULL;
    $asset_id = 0;
    if (is_array($args) && array_key_exists('asset_id', $args)) {
      $asset_id = (int)($args['asset_id']);
      if ($asset_id > 0) {
        $image = asset_load_item( $asset_id );
      }
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;


    // build the config form
    $data['title'] = array('name' => 'title',
                      'id'      => 'title',
                      'type'    => 'text',
                      'value'   => array_key_exists('title', $args) ? set_value( 'title', $args['title'] ) : NULL,
                      'size'    => 40,
                     );
    $data['link'] = array('name' => 'link',
                      'id'      => 'link',
                      'type'    => 'text',
                      'value'   => array_key_exists('link', $args) ? set_value( 'link', $args['link'] ) : NULL,
                      'size'    => 60,
                     );
    $data['link_title'] = array('name' => 'link_title',
                      'id'      => 'link_title',
                      'type'    => 'text',
                      'value'   => array_key_exists('link_title', $args) ? set_value( 'link_title', $args['link_title'] ) : NULL,
                      'size'    => 40,
                     );
    $link_blank = false;
    if(isset($args['link_blank']) && $args['link_blank'] == '1') 
        $link_blank = TRUE;
    $data['link_blank'] = array(
        'name' => 'link_blank',
        'id' => 'link_blank',
        'checked' => $link_blank,
        'value' => '1'
    );   
    
    if(isset($args['for_logged_users']) && $args['for_logged_users'] == '1') 
        $for_logged_users = TRUE;
    $data['for_logged_users'] = array(
        'name' => 'for_logged_users',
        'id' => 'for_logged_users',
        'checked' => $for_logged_users,
        'value' => '1'
    );  
    
    $categories = asset_list_categories(array('context' => 'image_widget'));
    $options = array('' => ' -- select category -- ');
    foreach ($categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['categories'] = $options;

    $data['media_upload_ui'] = asset_upload_ui($args);
    $images = asset_images_ui($args + array('single_selection' => 'ON'));
    $data['media_library_ui'] = $images['formatted'];
    // load widget view
    return $this->render('image_admin', $data);
  }

}
?>