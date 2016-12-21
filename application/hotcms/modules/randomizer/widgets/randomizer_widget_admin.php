<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Randomizer_widget_admin extends Widget {

  public function run($args = array()) {
    $this->load->library('session');
    //$this->load->library('form_validation');
    $this->load->config('randomizer/randomizer', TRUE);
    //$this->load->model('randomizer/randomizer_model');

    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    if (array_key_exists('postback', $args)) {
      $settings = array();
      $settings['asset_category_id'] = (int) ($args['asset_category_id']);
      $settings['title'] = trim($args['title']);
      return $settings;
    }

    $this->load->helper('asset/asset');

    $images = array();
    $asset_category_id = 0;
    if (is_array($args) && array_key_exists('asset_category_id', $args)) {
      $asset_category_id = (int) ($args['asset_category_id']);
      if ($asset_category_id > 0) {
        $this->load->library('asset/asset_item');
        $images = Asset_image_item::get_all_images_by_categoryid($asset_category_id);
      }
    }
  
    $data['asset_category_id'] = $asset_category_id;
    $data['images'] = $images;

    // build the config form
    $data['title'] = array('name' => 'title',
        'id' => 'title',
        'type' => 'text',
        'value' => array_key_exists('title', $args) ? set_value('title', $args['title']) : NULL,
    );
    $categories = asset_list_categories(array('context' => 'randomizer_widget'));
    $options = array('' => ' -- select category -- ');
    foreach ($categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['categories'] = $options;

    $data['media_upload_ui'] = asset_upload_ui($args);
    $data['media_library_ui'] = asset_images_ui($args);
    // load widget view
    return $this->render('randomizer_admin', $data);
  }

}

?>