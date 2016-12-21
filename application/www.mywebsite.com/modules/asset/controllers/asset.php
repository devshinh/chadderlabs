<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Asset extends HotCMS_Controller {
/*
  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_content')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('asset', TRUE);
    $this->load->model('model_asset');
    $this->load->model('model_asset_category');
    $this->load->library('asset_item');

    $this->module_url = $this->config->item('module_url', 'asset');
    $this->module_header = $this->lang->line('hotcms_media_library');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_asset'));

    $this->asset_type = array(
        '1' => 'image',
            //'2'  => 'video',
            //'3'  => 'document'
    );

    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'asset');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'asset');
  }
*/
    
  public function _remap($method, $args) {

    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_content')) {
      //show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('asset', TRUE);
    $this->load->model('model_asset');
    $this->load->model('model_asset_category');
    $this->load->library('asset_item');
    
    $this->module_url = $this->config->item('module_url', 'asset');
    $this->module_header = $this->lang->line('hotcms_media_library');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_asset'));

    $this->asset_type = array(
        '1' => 'image',
            //'2'  => 'video',
            //'3'  => 'document'
    );

    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'asset');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'asset');    



    $args = array_slice($this->uri->rsegments, 2);

    if (method_exists($this, $method)) {
      return call_user_func_array(array(&$this, $method), $args);
    }
  }    
  
  /**
   * list all assets
   * @param  int  page number
   */
  public function index($page_num = 1) {
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;
    $pagination_config['total_rows'] = Asset_image_item::count_all_images();

    $images = Asset_image_item::get_all_images($page_num, $pagination_config['per_page']);
    $right_data = array();
    $right_data['aCurrent'] = $images;

    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();

    //$moduleView = $this->load->view('asset', $aData, true);

    self::loadBackendView($data, 'asset/asset', NULL, 'asset/asset', $right_data);
    //self::loadView($moduleView);
  }

  /**
   * Set validation rules
   *
   */
  private function validate() {
    // assign validation rules
    $this->form_validation->set_rules('asset_name', strtolower(lang('hotcms_name')), 'trim|required');
    //$this->form_validation->set_rules( 'asset_description', strtolower(lang( 'hotcms_description' )),  'trim|required' );
  }

  private function get_categories($empty_option = FALSE) {
    $categories = array();
    if ($empty_option) {
      $categories[0] = ' ';
    }
    $category_records = $this->model_asset_category->get_all_categories();
    foreach ($category_records as $category) {
      $categories[$category->id] = $category->name;
    }
    return $categories;
  }

  /**
   * Calling create function from model class.
   *
   * @param id of item
   */
  public function create() {

    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = $this->module_header;
    $aData['add_new_text'] = $this->add_new_text;

    $this->validate();

    $this->form_validation->set_rules('asset_file', 'asset_file', 'callback_check_file');

    if ($this->form_validation->run()) {

      //if image
      if ($this->input->post('asset_type') == 1) {
        $this->upload_picture();
      } else if ($this->input->post('asset_type') == 3) {
        // define constant
        define('PATH_UPLOAD_IMAGE', '../' . $this->session->userdata('siteURL') . '/asset/upload/document/');
        $filename = self::strip_extension($_FILES['asset_file']['name']);
        $extension = substr(strrchr($_FILES['asset_file']['name'], '.'), 1);

        $config['file_name'] = $filename;
        $config['allowed_types'] = 'pdf';
        $config['overwrite'] = true;
        $config['upload_path'] = PATH_UPLOAD_IMAGE;

        $this->load->library('upload', $config);

        //upload succesfull
        if ($this->upload->do_upload('asset_file')) {
          $this->model_asset->insert_asset($filename, $extension);

          $aData['aCurrent'] = $this->model_asset->get_all_assets();

          $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => $this->lang->line('hotcms_created_item')));
          $aData['message'] = self::setMessage(false);

          $moduleView = $this->load->view('asset', $aData, true);
          self::loadView($moduleView);
        } else {
          //upload failed
          $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => $this->upload->display_errors()));
          $aData['message'] = self::setMessage(false);
          //upload failed
          $this->render_create_view($aData);
        }
      } else {
        //bad type
      }
    } else {
      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $aData['message'] = self::setMessage(false);
      $this->render_create_view($aData);
      /*
        $aData['asset_name_input'] = $this->_create_text_input('asset_name', $this->input->post( 'name' ),100,20,'text');
        $aData['description_input'] = array(
        'name'        => 'description',
        'id'          => 'description',
        'value'       => set_value( 'description', $this->input->post( 'description' ) ),
        'rows'        => '5',
        'cols'        => '20',
        'class'       => 'textarea'
        );

        $aData['asset_type'] = $this->asset_type;
        $aData['categories'] = $categories;

        $aData['asset_file_input'] = $this->_create_text_input('asset_file', $this->input->post( 'asset_file' ),100,20,'');


        $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
        $aData['message'] = self::setMessage(false);

        $moduleView = $this->load->view('asset_create', $aData, true);
        self::loadView($moduleView);
       */
    }
  }

  public function edit($id) {

    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = "Edit Asset";

    //get full list of available categories
    $aData['asset_categories'] = $this->get_categories();

    $this->validate();

    if ($this->form_validation->run()) {
      $this->model_asset->update_asset($id, $this->input->post('asset_name'), $this->input->post('asset_description'));

      $aData['currentItem'] = $this->model_asset->get_asset_by_id($id);
      $aData['asset_selected_category'] = $aData['currentItem']->asset_category_id;

      $aData['form'] = self::set_edit_form($aData['currentItem']);
      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => 'Item was updated.'));
      $aData['message'] = self::setMessage(false);
      redirect('/media-library');

      //$moduleView = $this->load->view('asset_edit', $aData, true);
      //self::loadView($moduleView);
    } else {


      $aData['currentItem'] = $this->model_asset->get_asset_by_id($id);
      $aData['currentThumb'] = $this->model_asset->image_asset_get_thumbnail($id, 200, 200);
      $aData['asset_selected_category'] = $aData['currentItem']->asset_category_id;
      $aData['form'] = self::set_edit_form($aData['currentItem']);

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $aData['message'] = self::setMessage(false);
      $moduleView = $this->load->view('asset_edit', $aData, true);
      self::loadView($moduleView);
    }
  }

  private function check_and_make_directory($path) {
    $is_created = false;
    if (!is_dir($path)) {
      if (!file_exists($path)) {
        $is_created = mkdir($path, 0777, TRUE);
      }
    } else {
      $is_created = true;
    }
    return $is_created;
  }

  private function get_default_upload_config() {
    $upload_config = $this->config->item('upload', 'asset');
    if (!$upload_config) {
      $upload_config['allowed_types'] = 'gif|jpg|png';
      $upload_config['overwrite'] = true;
    }
    return $upload_config;
  }

  private function get_default_thumbnail_config() {
    $thumbnail_config = $this->config->item('thumbnails', 'asset');
    if ($thumbnail_config == NULL) {
      $thumbnail_config = array();
      $thumbnail_config['thumbnails'][] = array('height' => 50, 'width' => 50, 'keep_ratio' => true);
      $thumbnail_config['thumbnails'][] = array('height' => 200, 'width' => 200, 'keep_ratio' => true);
    }
    return $thumbnail_config;
  }

  private function get_site_path_for_upload() {
    $site_path = $this->session->userdata('sitePath');
    if (!$site_path) {
      $site_path = $this->session->userdata('siteURL');
    }
    return $site_path;
  }

  private function get_public_image_path() {
    return $this->config->item('public_path', 'asset');
  }

  private function get_default_category_id() {
    return $this->config->item('category_default', 'asset');
  }

  private function get_content_category_id() {
    return $this->config->item('category_content', 'asset');
  }

  private function get_full_upload_path() {
    $site_path = $this->get_site_path_for_upload();
    $public_path = $this->get_public_image_path();
    return '../' . $site_path . $public_path;
  }

  private function upload_picture() {
    // define constant
    $this->load->library('image_lib');

    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = $this->module_header;
    $aData['add_new_text'] = $this->add_new_text;

    $site_path = $this->get_site_path_for_upload();
    $public_path = $this->get_public_image_path();

    //define( 'PATH_UPLOAD_IMAGE', '../' .$site_path .$public_path );
    $upload_dir = $this->get_full_upload_path();

    $asset_category_id = $this->input->post('asset_categories');

    if ($asset_category_id == NULL) {
      $asset_category_id == $this->get_default_category_id();
    }

    //add path from selected category
    $category = $this->model_asset_category->get_category_by_id($asset_category_id);
    if ($category != NULL && $category->path != '') {
      $upload_dir .= $category->path . '/';
      $public_path .= $category->path . '/';
    }

    $thumbnail_config = $this->get_default_thumbnail_config();
    $upload_config = $this->get_default_upload_config();
    //var_dump($thumbnail_config);
    //var_dump($upload_config);
    //die();

    $filename = self::strip_extension($_FILES['asset_file']['name']);
    $extension = substr(strrchr($_FILES['asset_file']['name'], '.'), 1);
    $has_error = TRUE;

    $this->check_and_make_directory($upload_dir);

    $upload_config['file_name'] = $filename;
    $upload_config['upload_path'] = $upload_dir;

    //die(var_dump($upload_config));

    $this->load->library('upload', $upload_config);

    //die($upload_dir);
    //upload succesfull
    if ($this->upload->do_upload('asset_file')) {

      // assign image data
      $aImage = $this->upload->data();

      $image_path = $upload_dir . $filename . '.' . $extension;
      list($width, $height, $type, $attr) = getimagesize($image_path);
      $portrait = ($width <= $height);

      $site_id = $this->session->userdata('siteID');
      $author_id = $this->session->userdata('user_id');
      $image_name = $this->input->post('asset_name');
      $image_description = $this->input->post('asset_description');
      $upload_type = 1;

      //$this->model_asset->insert_asset($filename, $extension);
      $image_id = $this->model_asset->insert_image_asset_with_info($site_id, $author_id, $image_name, $image_description, $upload_type, $asset_category_id, $filename, $extension, $width, $height, $portrait);

      $maintain_ratio = TRUE;

      foreach ($thumbnail_config as $thumbnail_params) {
        // make thumbnail
        $resize_config['source_image'] = $upload_dir . $filename . '.' . $extension;
        $resize_config['create_thumb'] = TRUE;

        //$config['maintain_ratio'] = TRUE;
        //$resize_config['thumb_marker']   = '';
        $resize_config['width'] = $thumbnail_params['width'];
        $resize_config['height'] = $thumbnail_params['height'];
        $resize_config['maintain_ratio'] = $maintain_ratio;

        $thumbnail_folder = 'thumbnail_' . $thumbnail_params['width'] . 'x' . $thumbnail_params['height'];

        $thumbnail_path = $upload_dir . $thumbnail_folder;
        if (!is_dir($thumbnail_path)) {
          if (!file_exists($thumbnail_path)) {
            mkdir($thumbnail_path, 0777, TRUE);
          }
        }
        $resize_config['new_image'] = $thumbnail_path . '/' . $filename . '.' . $extension;

         if ($thumbnail_params['crop']) {
           $resize_config['x_axis'] = $thumbnail_params['x_axis'];
           $resize_config['y_axis'] = $thumbnail_params['y_axis'];
         }

        // load library/make thumb
        $this->image_lib->initialize($resize_config);
        if ($thumbnail_params['crop']) {
          $this->image_lib->crop();
        } else {
          $this->image_lib->resize();
        }
        $this->model_asset->image_asset_add_thumbnail($image_id, $thumbnail_params['height'], $thumbnail_params['height'], $maintain_ratio, $public_path . $thumbnail_folder);
      }

      redirect('/media-library');

      /*
        $aData['aCurrent'] = $this->model_asset->get_all_assets();

        $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
        $aData['message'] = self::setMessage(false);

        $moduleView = $this->load->view('asset', $aData, true);
        self::loadView($moduleView);
       */
    } else {
      //upload failed
      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => $this->upload->display_errors()));
      $aData['message'] = self::setMessage(false);
      $this->render_create_view($aData);
    }
  }
  
  /**
   * get the public upload path
   * e.g. "/asset/upload/"
   * @return string
   */
  private function get_public_upload_path()
  {
    $public_path = $this->config->item('public_path', 'asset'); // e.g. asset
    $subdomain_path = $this->session->userdata('sitePath'); // e.g. upload
    return '/' . $public_path . '/' . $subdomain_path . '/';
  }  

    /**
   * Get upload config parameters based on asset type
   * for all supported file types, see /config/mimes.php
   * @param  int  $asset_type  asset type
   * @return array
   */
  private function get_upload_config($asset_type = 1)
  {
    $upload_config = $this->config->item('upload','asset');
    if (!$upload_config) {
      $upload_config['allowed_types'] = 'gif|jpg|png';
      $upload_config['overwrite']     = true;
    }
    switch ($asset_type) {
      case 1:  // image
        $upload_config['allowed_types'] = 'gif|jpg|jpe|jpeg|png';
        break;
      case 3:  // video
        //$upload_config['allowed_types'] = '3g2|3gp|asf|asx|avi|flv|mov|mp4|mpg|rm|swf|vob|wmv';
        $upload_config['allowed_types'] = 'mp4';
        break;
      case 4:  // audio
        //$upload_config['allowed_types'] = 'm3u|m4a|mid|mp3|mpa|ra|wav|wma';
        $upload_config['allowed_types'] = 'mp3|wav';
        break;
      default: // documents
        $upload_config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|csv';
    }
    return $upload_config;
  }
  
  private function render_create_view($aData) {
    //get full list of available categories
    $categories = $this->get_categories();

    //get selected category from input form, if not then use default as per configuration file
    $selected_category = $this->input->post('asset_categories');
    if ($selected_category == NULL) {
      $selected_category = $this->get_default_category_id();
    }

    $aData['asset_name_input'] = $this->_create_text_input('asset_name', $this->input->post('asset_name'), 100, 20, 'text');
    $aData['asset_description_input'] = array(
        'name' => 'asset_description',
        'id' => 'asset_description',
        'value' => set_value('asset_description', $this->input->post('asset_description')),
        'rows' => '5',
        'cols' => '60',
        'class' => 'textarea'
    );
    $aData['asset_type'] = $this->asset_type;
    $aData['asset_file_input'] = $this->_create_text_input('asset_file', $this->input->post('asset_file'), 100, 20, '');
    $aData['asset_categories'] = $categories;
    $aData['asset_selected_category'] = $selected_category;

    //$moduleView = $this->load->view('asset_create', $aData, true);
    //self::loadView($moduleView);
    $right_data = array();
    self::loadBackendView($aData, 'asset/asset', NULL, 'asset/asset_create', $right_data);
  }

  private function set_edit_form($currentItem) {

    $aData['name_input'] = $this->_create_text_input('asset_name', $currentItem->name, 100, 20, 'text');

    $aData['description_input'] = array(
        'name' => 'asset_description',
        'id' => 'asset_description',
        'value' => set_value('asset_description', $currentItem->description),
        'rows' => '5',
        'cols' => '60',
        'class' => 'textarea'
    );

    //$aData['asset_type']= $this->asset_type;

    return $aData;
  }

  public function tiny_mce_list() {
    $images = Asset_image_item::get_all_images();
    $data['images'] = $images;
    $this->load->view('tiny_mce_array', $data);
  }

  /**
   * displays an interface for choosing image from media library
   * @param  int  image category id
   */
  public function tinymce_image_picker($category_id = 0) {
    $category_id = (int) $category_id;
    $data['category_id'] = $category_id;
    $data['categories'] = $this->get_categories(TRUE);
    $images = Asset_image_item::get_all_images_by_categoryid($category_id);
    $data['images'] = $images;
    $data['image_list'] = $this->load->view('asset_tinymce_images', $data, TRUE);
    $data['asset_file_input'] = $this->_create_text_input('asset_file', '', 100, 20, '');
    $data['asset_name_input'] = $this->_create_text_input('asset_name', '', 100, 20, 'text');
    $data['asset_description_input'] = array(
        'name' => 'asset_description',
        'id' => 'asset_description',
        'value' => '',
        'rows' => '2',
        'cols' => '40',
        'class' => 'textarea'
    );
    $this->load->view('asset_tinymce_picker', $data);
  }

  /**
   * displays images from media library
   * @param  int  image category id
   */
  public function tinymce_image_list($category_id = 0) {
    $category_id = (int) $category_id;
    $data['category_id'] = $category_id;
    $data['categories'] = $this->get_categories(TRUE);
    $images = Asset_image_item::get_all_images_by_categoryid($category_id);
    $data['images'] = $images;
    $this->load->view('asset_tinymce_images', $data);
  }

  public function ajax_upload_with_generated_category($generator_name, $generator_id) {
    $asset_category_id = $this->model_asset_category->get_system_generated_category($generator_name, $generator_id);
    $this->ajax_upload($asset_category_id);
  }

  /**
   * Lists images using ajax
   * @param  int  asset category id
   */
  public function ajax_images($asset_category_id) {
    $this->load->helper('asset/asset');
    $images = asset_images_ui(array('asset_category_id' => $asset_category_id));
    echo json_encode(array('raw' => $images['raw'], 'formatted' => $images['formatted']));
  }

  /**
   * Displays an images upload form
   * @param  int  asset category id
   */
  public function ajax_image_upload($asset_category_id) {
    $this->load->helper('asset/asset');
    echo asset_upload_ui(array('asset_category_id' => $asset_category_id));
  }

  public function ajax_upload($asset_category_id = null) {
    $this->load->library('image_lib');
    $status = '';
    $msg = '';

    $site_path = $this->get_site_path_for_upload();
    $public_path = $this->get_public_image_path();

    //define( 'PATH_UPLOAD_IMAGE', '../' .$site_path .$public_path );
    $upload_dir = $this->get_full_upload_path();

    if ($asset_category_id == NULL) {
      $asset_category_id = $this->input->post('asset_categories');
      //log_message('info',var_dump($asset_category_id));
      if ($asset_category_id == FALSE) {
        $asset_category_id = $this->get_content_category_id();
      }
    }

    //add path from selected category
    $category = $this->model_asset_category->get_category_by_id($asset_category_id);
    if ($category != NULL && $category->path != '') {
      $upload_dir .= $category->path . '/';
      $public_path .= $category->path . '/';
    }

    $thumbnail_config = $this->get_default_thumbnail_config();
    $upload_config = $this->get_default_upload_config();
    //var_dump($thumbnail_config);
    //var_dump($upload_config);
    //die();

    $filename = self::strip_extension($_FILES['asset_file']['name']);
    $extension = substr(strrchr($_FILES['asset_file']['name'], '.'), 1);
    $has_error = TRUE;

    $this->check_and_make_directory($upload_dir);

    $upload_config['file_name'] = $filename;
    $upload_config['upload_path'] = $upload_dir;

    //die(var_dump($upload_config));

    $this->load->library('upload', $upload_config);

    if ($this->upload->do_upload('asset_file')) {

      // assign image data
      $aImage = $this->upload->data();

      $image_path = $upload_dir . $filename . '.' . $extension;
      list($width, $height, $type, $attr) = getimagesize($image_path);
      $portrait = ($width <= $height);

      $site_id = $this->session->userdata('siteID');
      $author_id = $this->session->userdata('user_id');
      $image_name = $this->input->post('asset_name');
      $image_description = $this->input->post('asset_description');
      $image_category = $this->input->post('asset_categories');
      $upload_type = 1;

      //$this->model_asset->insert_asset($filename, $extension);
      $image_id = $this->model_asset->insert_image_asset_with_info($site_id, $author_id, $image_name, $image_description, $upload_type, $asset_category_id, $filename, $extension, $width, $height, $portrait);

      $maintain_ratio = TRUE;

      foreach ($thumbnail_config as $thumbnail_params) {
        // make thumbnail
        $resize_config['source_image'] = $upload_dir . $filename . '.' . $extension;
        $resize_config['create_thumb'] = TRUE;

        //$config['maintain_ratio'] = TRUE;
        //$resize_config['thumb_marker']   = '';
        $resize_config['width'] = $thumbnail_params['width'];
        $resize_config['height'] = $thumbnail_params['height'];
        $resize_config['maintain_ratio'] = $maintain_ratio;

        $thumbnail_folder = 'thumbnail_' . $thumbnail_params['width'] . 'x' . $thumbnail_params['height'];

        $thumbnail_path = $upload_dir . $thumbnail_folder;
        if (!is_dir($thumbnail_path)) {
          if (!file_exists($thumbnail_path)) {
            mkdir($thumbnail_path, 0777, TRUE);
          }
        }
        $resize_config['new_image'] = $thumbnail_path . '/' . $filename . '.' . $extension;

        // load library/make thumb
        $this->image_lib->initialize($resize_config);
        $this->image_lib->resize();
        $this->model_asset->image_asset_add_thumbnail($image_id, $thumbnail_params['height'], $thumbnail_params['height'], $maintain_ratio, $public_path . $thumbnail_folder);
      }
      $status = 'success';
      $msg = $public_path . $filename . '.' . $extension;
    } else {
      $status = 'error';
      $msg = $this->upload->display_errors('', '');
      $image_id = 0;
    }
    echo json_encode(array('status' => $status, 'msg' => $msg, 'asset_category_id' => $asset_category_id, 'asset_id' => $image_id));
  }

  public function build_form_data($args = NULL) {
    $data['asset_type'] = array('1' => 'image');
    $data['asset_file_input'] = $this->_create_text_input('asset_file', $args['asset_file'], 100, 20, '');
    $data['asset_name_input'] = $this->_create_text_input('asset_name', $args['asset_name'], 100, 20, 'text');
    $data['asset_description_input'] = array(
        'name' => 'asset_description',
        'id' => 'asset_description',
        'value' => set_value('asset_description', $args['asset_description']),
        'rows' => '5',
        'cols' => '20',
        'class' => 'textarea'
    );
    return $data;
  }

  /**
   * Calling delete function from model class
   *
   * @param id of item
   */
  public function delete($id) {
    $this->model_asset->delete_by_id($id);
    $this->index();
  }

  public function ajax_delete($id) {
    $result = $this->model_asset->delete_by_id($id);
    if ($result) {
      echo json_encode(array('status' => 'success', 'messages' => 'Image has been deleted.'));
    } else {
      echo json_encode(array('status' => 'failed', 'messages' => 'Unknown error.'));
    }
  }

  public function widgetupload($page_section_id) {
    $widget_name = $this->input->post('widget_name');
    $this->ajax_upload_with_generated_category($widget_name, $page_section_id);
  }

  /**
   * Update/delete multiple images
   * @param int category ID
   * @return bool
   */
  public function widgetupdate($category_id) {
    $category_id = (int) $category_id;
    if ($category_id == 0) {
      return FALSE;
    }
    $updated = FALSE;
    $deletes = $this->input->post('delete');
    $ids = $this->input->post('id');
    $names = $this->input->post('name');
    $descs = $this->input->post('description');
    // delete items
    if (is_array($deletes) && count($deletes) > 0) {
      $this->load->helper('asset/asset');
      foreach ($deletes as $id) {
        if ($id > 0) {
          asset_delete_item($id);
          $updated = TRUE;
        }
      }
    }
    // update exiting items
    if (is_array($ids) && count($ids) > 0) {
      foreach ($ids as $id) {
        if (is_array($deletes) && array_key_exists($id, $deletes)) {
          continue;
        }
        $this->model_asset->update_asset($id, $names[$id], $descs[$id]);
        $updated = TRUE;
      }
    }
    return $updated;
  }

  public function upload_picture_external($file, $category_id)
  {
    // define constant
    $this->load->library('image_lib');

    $public_path = $this->get_public_upload_path();


    $upload_dir = $this->get_full_upload_path();
    $upload_dir = 'asset/upload/';
    
    if($category_id == 34) {
        $upload_dir = 'asset/upload/verifications/';
    }

    $asset_category_id = $category_id;

    if ($asset_category_id == NULL)
    {
      $asset_category_id == $this->get_default_category_id();
    }

    //add path from selected category
    //$category = $this->model_asset_category->get_category_by_id($asset_category_id);
    //if ($category != NULL && $category->path != '') {
    //  $upload_dir .= $category->path.'/';
    //  $public_path .= $category->path.'/';
    //}

    $thumbnail_config = $this->get_default_thumbnail_config();
    $upload_config = $this->get_upload_config(1);

    $filename = strip_extension($file['name']);
    
    $filename = url_title($filename,'_');
    
    //verifications && avataras images extend name wi custom string
    if($category_id == 34 || $category_id == 9) {
        //$filename = uniqid(strip_extension($file['name']), 16, 36);
        $filename = strip_extension($filename).'_'.md5(date('Y-m-d H:i:s:u'));
    }
    $extension = get_extension($file['name']);
    $has_error = TRUE;

    $this->check_and_make_directory($upload_dir);

    $upload_config['file_name']     = $filename;
    $upload_config['upload_path']   = $upload_dir;


    $this->load->library('upload', $upload_config);

    //die($upload_dir);
    if(is_array($_FILES) && count($_FILES) == 1){
        foreach ($_FILES as $k => $v) {
            $file_id = $k;
        }
    }

    //upload succesfull
    if ($this->upload->do_upload( $file_id )) {

        // assign image data
        $aImage = $this->upload->data();

        $image_path = $upload_dir . $filename . '.' . $extension;
        list($width, $height, $type, $attr)= getimagesize($image_path);

        $image_name = $filename;
        $image_description = '';
        $upload_type = 1;

        //$this->model_asset->insert_asset($filename, $extension);
        $image_id = $this->model_asset->insert_image_asset_with_info($image_name,$image_description,$upload_type,$asset_category_id,$filename,$extension,$width,$height);

        $maintain_ratio = TRUE;

        foreach($thumbnail_config as $thumbnail_params)
        {
          // make thumbnail
          $resize_config['source_image']   = $upload_dir . $filename . '.' . $extension;
          $resize_config['create_thumb']   = TRUE;

          //$config['maintain_ratio'] = TRUE;
          //$resize_config['thumb_marker']   = '';
          $resize_config['width']          = $thumbnail_params['width'];
          $resize_config['height']         = $thumbnail_params['height'];
          $resize_config['maintain_ratio'] = $maintain_ratio;

          $thumbnail_folder = 'thumbnail_'.$thumbnail_params['width'].'x'.$thumbnail_params['height'];

          $thumbnail_path = $upload_dir.$thumbnail_folder;
          if (!is_dir($thumbnail_path))
          {
            if (!file_exists($thumbnail_path))
            {
              mkdir($thumbnail_path,0777,TRUE);
            }
          }
          $resize_config['new_image'] = $thumbnail_path.'/'.$filename . '.' . $extension;

          // load library/make thumb
          $this->image_lib->initialize( $resize_config );
          $this->image_lib->resize();
          $this->model_asset->image_asset_add_thumbnail($image_id,$thumbnail_params['height'],$thumbnail_params['height'],$maintain_ratio,$public_path.$thumbnail_folder);
        }

        return $image_id;
    }else {
      //upload failed
      $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => $this->upload->display_errors() ) );
      $aData['message'] = self::setMessage(false);
    }
  }
  
  public function upload_avatar(){
      $file = $_FILES['avatar'];
      $category_id = 9;
      $asset_id = $this->upload_picture_external($file, $category_id);
              $userid = $this->session->userdata('user_id');
            
            if(!empty($asset_id)){
                    $this->load->model('user/user_model');
                    $this->user_model->update_avatar($userid, $asset_id);            
                  
                    //add draws for avatar upload      
                  
                    $this->load->helper('account/account');
                    account_add_contest_entries($userid, 1000, 'upload_avat','asset', $asset_id,' earned 1000 contest entries for Avatar upload.');                    
            }           
      //reset user_info session
        $this->load->model('account/account_model');
        $user_info = $this->account_model->get_user($userid);
        $this->session->unset_userdata('user_info');
        $this->session->set_userdata('user_info', $user_info);                   
            
      redirect('/profile');
  }
  
  public function upload_verification(){
      $file = $_FILES['verification'];
      $category_id = 34;
      $asset_id = $this->upload_picture_external($file, $category_id);
      $userid = $this->session->userdata('user_id');
            
            if(!empty($asset_id)){
                    $this->load->model('verification/verification_model');
                   $this->verification_model->insert_from_form($userid, $asset_id);              
            }
      //reset user_info session
        $this->load->model('account/account_model');
        $user_info = $this->account_model->get_user($userid);
        $this->session->unset_userdata('user_info');
        $this->session->set_userdata('user_info', $user_info);                   
            
      redirect('/profile');
  }
  
}

?>
