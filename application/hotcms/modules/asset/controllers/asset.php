<?php

class Asset extends HotCMS_Controller {

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

    $this->module_url = $this->config->item('module_url','asset');
    $this->module_header = $this->lang->line( 'hotcms_media_library' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_asset' ));

    $this->asset_types = array(
      '1' => 'Image',
      '2' => 'Document',
      '3' => 'Video',
      '4' => 'Audio'
    );

    $this->java_script = 'modules/'.$this->module_url.'/js/'.$this->config->item('js', 'asset');
    $this->css = 'modules/'.$this->module_url.'/css/'.$this->config->item('css', 'asset');
  }

  /**
   * list all assets
   * @param  int  page number
   * TODO: sorting and searching feature
   */
  public function index($page_num = 1)
  {
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    
    $data['java_script'] = 'modules/asset/js/asset.js';
    
    //display first page after per page change
    if($this->input->post('per_page_change')) {
        $page_num = 1;
    }
    // search/filter form
    $default_sort_by = 'name'; // default field to sort by
    $default_per_page = 10; // default items to display per page
    if ($this->input->post()) {
        $sort_direction = $this->input->post('sort_direction');
        if (!in_array($sort_direction, array('asc', 'desc'))) {
            $sort_direction = 'asc';
        }
        $filters = array(
            'sort_by' => $this->input->post('sort_by') > '' ? $this->input->post('sort_by') : $default_sort_by,
            'sort_direction' => $this->input->post('sort_direction'),
            'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
            'keyword' => $this->input->post('keyword'),
            'category' => $this->input->post('category'),
            'type' => $this->input->post('type'),
        );
        $this->session->set_userdata('media_library_filters', $filters);
        //redirect('retailer/store/' . $retailer_id);
    }
    $filters = $this->session->userdata('media_library_filters');
    if (!is_array($filters)) {
        $filters = array(
            'sort_by' => $default_sort_by,
            'sort_direction' => 'asc',
            'per_page' => $default_per_page,
            'keyword' => '',
            'category' => '',
            'type' => '',
        );
    }
    //$filters['retailer_id'] = $retailer_id;
    $data['filters'] = $filters;

    //active filters string
    $active_filters = '';
    $separator = false;
    foreach ($filters as $filter_key => $filter_value) {
        if ($filter_key == 'keyword' && $filter_value != '') {
            $active_filters = 'Keyword - ' . $filter_value;
            $separator = true;
        }
        if ($filter_key == 'category' && $filter_value != '') {
            if ($separator) {
                $active_filters.= ', ';
            }
            $active_filters.= '<b>Category:</b> ';
            foreach ($filter_value as $code) {
                $active_filters.= $code . ', ';
            }
            $active_filters = substr($active_filters, 0, -2);
            $separator = true;
        }
        if ($filter_key == 'type' && $filter_value != '') {
            if ($separator) {
                $active_filters.= ', ';
            }
            $active_filters.= '<b>Type:</b> ';

            foreach ($filter_value as $code) {
                switch ($code) {
                    case 1:
                        $active_filters.= lang('hotcms_image') . ', ';
                        break;
                    case 2:
                        $active_filters.= lang('hotcms_document') . ', ';
                        break;
                    case 3:
                        $active_filters.= lang('hotcms_video') . ', ';
                        break;
                    case 4:
                        $active_filters.= lang('hotcms_audio') . ', ';
                        break;                    
                    default:
                        break;
                }

            }
            $active_filters = substr($active_filters, 0, -2);
            $separator = true;
        }
    }
    if ($separator == false)
        $active_filters.= 'None';

    $data['active_filters'] = $active_filters;   

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = Asset_item::count_all_items($filters);

    $assets = Asset_item::list_all_items($filters, $page_num, $pagination_config['per_page']);
    
    $right_data = array();
    $right_data['aCurrent'] = $assets;

    $data['form']['type_options'] = $this->asset_types; 
    $data['form']['categories'] = $this->model_asset_category->list_media_categories_array();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword'], 'category' => $filters['category'], 'type' => $filters['type']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);        
    
    $data['form']['per_page_options'] = list_page_options();
    
    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();

    $this->load_messages();
    self::loadBackendView($data, 'asset/asset_leftbar', NULL, 'asset/asset', $right_data);
    //$moduleView = $this->load->view('asset', $aData, true);
    //self::loadView($moduleView);
  }

  /**
   * list media categories
   * @param  bool  if true, add an empty option to the top of the list
   * @return array
   */
  private function list_categories($empty_option = FALSE)
  {
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
   */
  public function create()
  {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

    $this->form_validation->set_rules('asset_name', 'lang:hotcms_name', 'trim|required');
    $this->form_validation->set_rules('asset_type', 'asset type', 'required');
    $this->form_validation->set_rules('asset_categories', 'lang:hotcms_category', 'required');
    // fake a post variable for form validation
    if (isset($_FILES['asset_file'])) {
      $_POST['asset_file'] = $_FILES['asset_file']['name'];
    }
    $this->form_validation->set_rules('asset_file', 'lang:hotcms_file', 'required|unique_filename[asset_file]');

    if ($this->form_validation->run() === TRUE) {
      $asset_type = (int)($this->input->post('asset_type'));
      $asset_category_id = (int)($this->input->post('asset_categories'));
      if ($asset_category_id == 0) {
        $asset_category_id = (int)($this->input->post('asset_category_id'));
        if ($asset_category_id == 0) {
          $asset_category_id = $this->get_default_category_id();
        }
      }
      $asset_name = $this->input->post('asset_name');
      $asset_description = $this->input->post('asset_description');
      $asset_id = $this->model_asset->insert($asset_type, $asset_category_id, $asset_name, $asset_description);
      if ($asset_id > 0) {
        $upload_result = $this->process_upload($asset_id, $asset_type, $asset_category_id);
        if ($upload_result['status'] == 'success') {
          redirect('/media-library/edit/' . $asset_id);
          exit;
        }
        else {
          $this->add_message('error', $upload_result['msg']);
        }
      }
    }
    elseif (validation_errors() > "") {
      $this->add_message('error', validation_errors());
    }

    // build the form
    $right_data = array();
    $right_data['asset_types']= $this->asset_types;
    //get full list of available categories
    $categories = $this->list_categories();
    //get selected category from input form, if not then use default as per configuration file
    $selected_category = $this->input->post('asset_categories');
    if ($selected_category == NULL) {
      $selected_category = $this->get_default_category_id();
    }
    $right_data['asset_categories'] = $categories;
    $right_data['asset_selected_category'] = $selected_category;

    $right_data['form']['hidden'] = array(
      'asset_file_overwrite' => 0,
      'asset_sd_overwrite' => 0,
      'asset_webmhd_overwrite' => 0,
      'asset_webmsd_overwrite' => 0,
      'asset_poster_overwrite' => 0,
    );
    $right_data['form']['asset_name_input'] = $this->_create_text_input('asset_name', $this->input->post('asset_name'), 100, 20, 'text');
    $right_data['form']['asset_file_input'] = $this->_create_text_input('asset_file', NULL, 100, 20, '');
    $right_data['form']['asset_sd_input'] = $this->_create_text_input('asset_sd', NULL, 100, 20, '');
    $right_data['form']['asset_webmhd_input'] = $this->_create_text_input('asset_webmhd', NULL, 100, 20, '');
    $right_data['form']['asset_webmsd_input'] = $this->_create_text_input('asset_webmsd', NULL, 100, 20, '');
    $right_data['form']['asset_poster_input'] = $this->_create_text_input('asset_poster', NULL, 100, 20, '');
    $right_data['form']['asset_description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => $this->input->post('asset_description'),
      'rows'        => '5',
      'cols'        => '60',
      'class'       => 'textarea'
    );

    $this->load_messages();
    self::loadBackendView($data, 'asset/asset_leftbar', NULL, 'asset/asset_create', $right_data);
  }

  /**
   * updates an asset
   * @param int $asset_id
   */
  public function edit($asset_id)
  {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit Asset";

    //get full list of available categories
    $data['asset_categories'] = $this->list_categories();

    $this->form_validation->set_rules('asset_name', 'lang:hotcms_name', 'required');
    $this->form_validation->set_rules('asset_categories', 'lang:hotcms_category', 'required');
    // fake a post variable for form validation
    if (isset($_FILES['asset_file'])) {
      $_POST['asset_file'] = $_FILES['asset_file']['name'];
    }
    $this->form_validation->set_rules('asset_file', 'lang:hotcms_file', 'unique_filename[asset_file]');

    if ($this->form_validation->run()) {
      $asset_type = (int)($this->input->post('asset_type'));
      //$file_validates = $this->validate_upload($asset_type);
      $asset_category_id = (int)($this->input->post('asset_categories'));
      if ($asset_category_id == 0) {
        $asset_category_id = (int)($this->input->post('asset_category_id'));
      }
      $attributes = array(
        'asset_category_id' => $asset_category_id,
        'name' => $this->input->post('asset_name'),
        'description' => $this->input->post('asset_description'),
      );
      $updated = $this->model_asset->update($asset_id, $attributes);
      $upload_result = $this->process_upload($asset_id, $asset_type, $asset_category_id);
      if ($updated && $upload_result['status'] == 'success') {
        $this->add_message('confirm', 'Item was updated.');
      }
      elseif ($upload_result['status'] != 'success') {
        $this->add_message('error', $upload_result['msg']);
      }
      else {
        $this->add_message('error', 'Sorry but there was an error when trying to update asset.');
      }
    }
    elseif (validation_errors() > "") {
      $this->add_message('error', validation_errors());
    }

    $data['currentItem'] = new Asset_item($asset_id);
    // build the form
    $data['form']['hidden'] = array(
      'asset_type' => $data['currentItem']->type,
      'asset_file_current' => (isset($data['currentItem']->file_name) && $data['currentItem']->file_name > '' ? $data['currentItem']->file_name . '.' . $data['currentItem']->extension : ''),
      'asset_sd_current' => (isset($data['currentItem']->mp4_sd) && $data['currentItem']->mp4_sd > '' ? $data['currentItem']->mp4_sd : ''),
      'asset_webmhd_current' => (isset($data['currentItem']->webm_hd) && $data['currentItem']->webm_hd > '' ? $data['currentItem']->webm_hd : ''),
      'asset_webmsd_current' => (isset($data['currentItem']->webm_sd) && $data['currentItem']->webm_sd > '' ? $data['currentItem']->webm_sd : ''),
      'asset_poster_current' => (isset($data['currentItem']->poster) && $data['currentItem']->poster > '' ? $data['currentItem']->poster : ''),
      'asset_file_overwrite' => ($_POST ? $this->input->post('asset_file_overwrite') : 0),
      'asset_sd_overwrite' => ($_POST ? $this->input->post('asset_sd_overwrite') : 0),
      'asset_webmhd_overwrite' => ($_POST ? $this->input->post('asset_webmhd_overwrite') : 0),
      'asset_webmsd_overwrite' => ($_POST ? $this->input->post('asset_webmsd_overwrite') : 0),
      'asset_poster_overwrite' => ($_POST ? $this->input->post('asset_poster_overwrite') : 0),
    );
    $data['form']['name_input'] = $this->_create_text_input('asset_name', $data['currentItem']->name, 100, 20, 'text');
    $data['form']['asset_file_input'] = $this->_create_text_input('asset_file', NULL, 100, 20, '');
    $data['form']['asset_sd_input'] = $this->_create_text_input('asset_sd', NULL, 100, 20, '');
    $data['form']['asset_webmhd_input'] = $this->_create_text_input('asset_webmhd', NULL, 100, 20, '');
    $data['form']['asset_webmsd_input'] = $this->_create_text_input('asset_webmsd', NULL, 100, 20, '');
    $data['form']['asset_poster_input'] = $this->_create_text_input('asset_poster', NULL, 100, 20, '');
    $data['form']['description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => ($_POST ? $this->input->post('asset_description') : $data['currentItem']->description),
      'rows'        => '5',
      'cols'        => '60',
      'class'       => 'textarea'
    );

    $this->load_messages();
    self::loadBackendView($data, 'asset/asset_leftbar', NULL, 'asset/asset_edit', NULL);
  }

 /**
  * check a directory, and create one if not exists.
  * @param  string  $path
  * @return bool
  */
  private function check_and_make_directory($path)
  {
    $is_created = false;
    if (!is_dir($path)) {
      if (!file_exists($path)) {
        $is_created = mkdir($path, 0777, TRUE);
      }
    }
    else {
      $is_created = true;
    }
    return $is_created;
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

  private function get_default_thumbnail_config()
  {
    $thumbnail_config = $this->config->item('thumbnails', 'asset');
    if (empty($thumbnail_config)) {
      $thumbnail_config = array();
      $thumbnail_config['thumbnails'][] = array('height' => 50, 'width' => 50, 'keep_ratio' => true);
      $thumbnail_config['thumbnails'][] = array('height' => 200, 'width' => 200, 'keep_ratio' => true);
    }
    return $thumbnail_config;
  }

  /**
   * return the application name, which is under the application folder
   * e.g. "www.mywebsite.com"
   * @return string
   */
  private function get_application_path()
  {
    return $this->config->item('application_path', 'asset');
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

  private function get_default_category_id()
  {
    return $this->config->item('category_default','asset');
  }

  /*
  private function get_content_category_id()
  {
    return $this->config->item('category_content','asset');
  } */

  /**
   * get the full upload path
   * e.g. "../www.mywebsite.com/asset/upload/"
   * @return str
   */
  private function get_full_upload_path()
  {
    $public_path = $this->get_public_upload_path();
    return '../' . $this->config->item('application_path', 'asset') . $public_path;
  }

  public function tiny_mce_list()
  {
    $images = Asset_item::list_all_items(1);
    $data['images'] = $images;
    $this->load->view('tiny_mce_array',$data);
  }

  /**
   * displays an interface for choosing image from media library
   * @param  int  image category id
   */
  public function tinymce_image_picker($category_id = 0)
  {
    $category_id = (int)$category_id;
    $data['category_id'] = $category_id;
    $data['categories'] = $this->list_categories(TRUE);
    $images = Asset_item::list_all_images($category_id);
    $data['images'] = $images;
    $data['image_list'] = $this->load->view('asset_tinymce_images', $data, TRUE);
    $data['asset_file_input'] = $this->_create_text_input('asset_file', '', 100, 20, '');
    $data['asset_name_input'] = $this->_create_text_input('asset_name', '', 100, 20, 'text');
    $data['asset_description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => '',
      'rows'        => '2',
      'cols'        => '40',
      'class'       => 'textarea'
    );
    $this->load->view('asset_tinymce_picker', $data);
  }

  /**
   * displays images from media library
   * @param  int  image category id
   */
  public function tinymce_image_list($category_id = 0)
  {
    $category_id = (int)$category_id;
    $data['category_id'] = $category_id;
    $data['categories'] = $this->list_categories(TRUE);
    $images = Asset_item::list_all_images($category_id);
    $data['images'] = $images;
    $this->load->view('asset_tinymce_images', $data);
  }

  public function ajax_upload_with_generated_category($generator_name,$generator_id)
  {
    $asset_category_id = $this->model_asset_category->get_system_generated_category($generator_name,$generator_id);
    $this->ajax_upload($asset_category_id);
  }

 /**
  * Lists assets using ajax
  * @param  int  asset category id
  */
  public function ajax_assets($asset_category_id, $type = 1)
  {
    $this->load->helper('asset/asset');
    switch ($type) {
      case 1:  // image
        $assets = asset_images_ui(array('asset_category_id' => $asset_category_id));
        break;
      case 3:  // video
        $assets = asset_images_ui(array('asset_category_id' => $asset_category_id));
        break;          
      case 4:  // audio
      default: // document
        $assets = asset_files_ui(array('asset_category_id' => $asset_category_id));
    }
    echo json_encode(array('raw' => $assets['raw'], 'formatted' => $assets['formatted']));
  }

 /**
  * Displays an image upload form
  * @param  int  asset category id
  */
  public function ajax_image_upload($asset_category_id, $type = 1)
  {
    $this->load->helper('asset/asset');
    echo asset_upload_ui(array(
      'asset_type' => $type,
      'asset_category_id' => $asset_category_id
    ));
  }

 /**
  * Lists files using ajax
  * @param  int  asset category id
  */
  public function ajax_files($asset_category_id)
  {
    $this->load->helper('asset/asset');
    $files = asset_files_ui(array('asset_category_id' => $asset_category_id));
    echo json_encode(array('raw' => $files['raw'], 'formatted' => $files['formatted']));
  }

 /**
  * Displays a file upload form
  * @param  int  asset category id
  * @param  int  asset type
  * @return string
  */
  public function ajax_asset_upload($asset_category_id, $type = 2)
  {
    $this->load->helper('asset/asset');
    echo asset_upload_ui(array(
      'asset_type' => $type,
      'asset_category_id' => $asset_category_id
    ));
  }

 /**
  * Process file upload using Ajax
  * @param  int  asset category id
  * @param  int  asset type
  * @return string
  */
  public function ajax_upload($asset_category_id = null, $asset_type = 4)
  {
    $asset_name = $this->input->post('asset_name');
    $asset_description = $this->input->post('asset_description');
    $asset_category_id = (int)$asset_category_id;
    if ($asset_category_id == 0) {
      $asset_category_id = (int)($this->input->post('asset_category_id'));
      if ($asset_category_id == 0) {
        $asset_category_id = $this->get_default_category_id();
      }
    }
    // fake a post variable for form validation
    if (isset($_FILES['asset_file'])) {
      $_POST['asset_file'] = $_FILES['asset_file']['name'];
    }
    //$this->form_validation->set_rules('asset_file', 'lang:hotcms_file', 'unique_filename[asset_file]');
    $overwrite = $this->input->post('asset_file_overwrite');
    $old_filename = url_title($this->input->post('asset_file_current'),'_');
    $new_filename = url_title($this->input->post('asset_file'),'_');
    if ($overwrite != '1' && $new_filename > '') { // && $new_filename != $old_filename) {
      $public_path = '/' . $this->config->item('public_path', 'asset') . '/' . $this->session->userdata('sitePath') . '/';
      $abs_upload_path = $_SERVER['DOCUMENT_ROOT'] . '/application/' . $this->config->item('application_path', 'asset') . $public_path;
      $file_name = $abs_upload_path . $new_filename;
      if (file_exists($file_name)) {
        //$this->set_message('unique_filename', 'A file named ' . $new_filename . ' already exists on the server.');
        $result = array(
          'status' => 'error_filename',
          'msg' => 'A file named ' . $new_filename . ' already exists on the server.',
          'asset_category_id' => $asset_category_id,
          'asset_id' => 0,
          'asset_type' => $asset_type);
        echo json_encode($result);
        exit();
      }
    }
    $asset_id = $this->model_asset->insert($asset_type, $asset_category_id, $asset_name, $asset_description);
    $upload_result = $this->process_upload($asset_id, $asset_type, $asset_category_id);
    echo json_encode($upload_result);
  }

  /**
   * Process media (image, video, audio, document) file uploads
   * @param  int  asset id
   * @param  int  asset type
   * @param  int  asset category id
   * @return array
   */
  private function process_upload($asset_id, $asset_type, $asset_category_id)
  {
    if ($asset_id == 0 || $asset_type == 0 || $asset_category_id == 0) {
      return array('status' => 'error', 'msg' => 'Asset not found.', 'asset_category_id' => $asset_category_id, 'asset_id' => $asset_id, 'asset_type' => $asset_type);
    }
    $this->load->library('upload');
    $status = 'success';
    $msg = '';
    $public_path = $this->get_public_upload_path();
    $upload_dir = $this->get_full_upload_path();
    $abs_upload_path = $_SERVER['DOCUMENT_ROOT'] . '/application/' . $this->get_application_path() . $public_path;
    // using a fixed temp path for the system command
    // folder must be under root user group and writable to all users
    $abs_tmp_path = $_SERVER['DOCUMENT_ROOT'] . '/tmp';

    // check the folder for the selected category
    //$category = $this->model_asset_category->get_category_by_id($asset_category_id);
    //if ($category && $category->path > '') {
    //  $upload_dir .= $category->path . '/';
    //  $public_path .= $category->path . '/';
    //  $abs_upload_path .= $category->path . '/';
    //}
    $this->check_and_make_directory($upload_dir);

    $upload_config = $this->get_upload_config($asset_type);
    $upload_config['upload_path'] = $upload_dir;

    if ($_FILES['asset_file']['name'] > '') {
      $filename = strip_extension($_FILES['asset_file']['name']);
      $extension = get_extension($_FILES['asset_file']['name']);
      $filename = url_title($filename,'_');
      $upload_config['file_name'] = $filename;
      //$upload_config['allowed_types'] = 'jpg|png|gif';
      $this->upload->initialize($upload_config);
      if ($this->upload->do_upload('asset_file')) {
        $attributes = array(
          'file_name' => $filename,
          'extension' => $extension,
          'width' => 0,  // to be determined below
          'height' => 0  // to be determined below
        );
        switch ($asset_type) {
          case 1:  // image
            // assign image data
            //$aImage = $this->upload->data();
            $file_path = $upload_dir . $filename . '.' . $extension;
            list($width, $height, $type, $attr) = getimagesize($file_path);
            $attributes['width'] = $width;
            $attributes['height'] = $height;
            break;
          case 3:  // video
            // get the video resolution
            require_once(dirname(__FILE__) . '/../libraries/getid3/getid3.php');
            $getID3 = new getID3;
            $file_path = $upload_dir . $filename . '.' . $extension;
            $file = $getID3->analyze($file_path);
            $attributes['width'] = $file['video']['resolution_x'];
            $attributes['height'] = $file['video']['resolution_y'];
            $duration = $file['playtime_seconds'];
            $halfwaytime = (int)($duration/2);
            $screenshot_name = $abs_upload_path . $filename . '.png';
            $abs_video_name = $abs_upload_path . $filename . '.' . $extension;
            // get screen shots and use as poster
            $retval1 = $retval2 = '';
            //$last_line1 = system("mplayer -nosound -ss $halfwaytime -frames 1 -vo png:outdir=$abs_tmp_path $abs_video_name 2>&1", $retval1);
            //$last_line2 = system("mv $abs_tmp_path/00000001.png $screenshot_name 2>&1", $retval2);
            system("mplayer -nosound -ss $halfwaytime -frames 1 -vo png:outdir=$abs_tmp_path $abs_video_name");
            system("mv $abs_tmp_path/00000001.png $screenshot_name");
            $attributes['poster'] = $filename . '.png';
            break;
          //case 4:  // audio
            //break;
          //default:  // document
        }
        $updated = $this->model_asset->update($asset_id, $attributes);
        if ($updated) {
          $msg .= $public_path . $filename . '.' . $extension;
        }
        else {
          $status = 'error';
        }
        // make thumbnails for image assets
        if ($updated && $asset_type == 1) {
          $this->load->library('image_lib');
          $this->model_asset->asset_delete_thumbnails($asset_id);
          $maintain_ratio = TRUE;
          $thumbnail_config = $this->get_default_thumbnail_config();
          foreach ($thumbnail_config as $thumbnail_params) {
            $resize_config['source_image']   = $upload_dir . $filename . '.' . $extension;
            $resize_config['create_thumb']   = TRUE;
            $resize_config['width']          = $thumbnail_params['width'];
            $resize_config['height']         = $thumbnail_params['height'];
            $resize_config['maintain_ratio'] = $maintain_ratio;

            $thumbnail_folder = 'thumbnail_' . $thumbnail_params['width'] . 'x' . $thumbnail_params['height'];
            $thumbnail_path = $upload_dir . $thumbnail_folder;
            if (!is_dir($thumbnail_path)) {
              if (!file_exists($thumbnail_path)) {
                mkdir($thumbnail_path, 0777, TRUE);
              }
            }
            $resize_config['new_image'] = $thumbnail_path . '/' . $filename . '.' . $extension;

            // make thumb
            $this->image_lib->initialize( $resize_config );
            $this->image_lib->resize();
            $this->model_asset->image_asset_add_thumbnail($asset_id, $thumbnail_params['width'], $thumbnail_params['height'], $maintain_ratio, $public_path . $thumbnail_folder);
          }
        }
      }
      else {
        $status = 'error';
        $msg .= $this->upload->display_errors('', '');
      }
    }

    // upload video poster image
    if ($asset_type == 3 && isset($_FILES['asset_poster']) && $_FILES['asset_poster']['name'] > '') {
      $poster_filename = $_FILES['asset_poster']['name'];
      $upload_config['file_name'] = strip_extension($poster_filename);
      $upload_config['allowed_types'] = 'jpg|png|gif';
      $this->upload->initialize($upload_config);
      if ($this->upload->do_upload('asset_poster')) {
        $this->model_asset->asset_update_poster($asset_id, $poster_filename);
      }
      else {
        $status = 'error';
        $msg .= $this->upload->display_errors('', '');
      }
    }

    // upload video SD file
    if (($asset_type == 3) && isset($_FILES['asset_sd']) && $_FILES['asset_sd']['name'] > '') {
      $filename_sd = strip_extension($_FILES['asset_sd']['name']);
      $extension_sd = get_extension($_FILES['asset_sd']['name']);
      $upload_config['file_name'] = $filename_sd;
      $upload_config['allowed_types'] = 'mp4';
      $this->upload->initialize($upload_config);
      if ($this->upload->do_upload('asset_sd')) {
        $this->model_asset->asset_add_alternative($asset_id, $filename_sd, $extension_sd, 'mp4_sd');
      }
      else {
        $status = 'error';
        $msg .= $this->upload->display_errors('', '');
      }
    }

    // upload video webm SD file
    if ($asset_type == 3 && isset($_FILES['asset_webmsd']) && $_FILES['asset_webmsd']['name'] > '') {
      $filename_webm = strip_extension($_FILES['asset_webmsd']['name']);
      $extension_webm = get_extension($_FILES['asset_webmsd']['name']);
      $upload_config['file_name'] = $filename_webm;
      $upload_config['allowed_types'] = 'webm';
      $this->upload->initialize($upload_config);
      if ($this->upload->do_upload('asset_webmsd')) {
        $this->model_asset->asset_add_alternative($asset_id, $filename_webm, $extension_webm, 'webm_sd');
      }
      else {
        $status = 'error';
        $msg .= $this->upload->display_errors('', '');
      }
    }

    // upload video webm HD file
    if (($asset_type == 3) && isset($_FILES['asset_webmhd']) && $_FILES['asset_webmhd']['name'] > '') {
      $filename_webmhd = strip_extension($_FILES['asset_webmhd']['name']);
      $extension_webmhd = get_extension($_FILES['asset_webmhd']['name']);
      $upload_config['file_name'] = $filename_webmhd;
      $upload_config['allowed_types'] = 'webm';
      $this->upload->initialize($upload_config);
      if ($this->upload->do_upload('asset_webmhd')) {
        $this->model_asset->asset_add_alternative($asset_id, $filename_webmhd, $extension_webmhd, 'webm_hd');
      }
      else {
        $status = 'error';
        $msg .= $this->upload->display_errors('', '');
      }
    }

    $result = array('status' => $status, 'msg' => $msg, 'asset_category_id' => $asset_category_id, 'asset_id' => $asset_id, 'asset_type' => $asset_type);
    return $result;
  }

  public function build_form_data($args = NULL)
  {
    $data['asset_type'] = array('1'  => 'image');
    $data['asset_file_input'] = $this->_create_text_input('asset_file', $args['asset_file'],100,20,'');
    $data['asset_name_input'] = $this->_create_text_input('asset_name', $args['asset_name'],100,20,'text');
    $data['asset_description_input'] = array(
      'name'        => 'asset_description',
      'id'          => 'asset_description',
      'value'       => set_value( 'asset_description', $args['asset_description'] ),
      'rows'        => '5',
      'cols'        => '20',
      'class'       => 'textarea'
    );
    return $data;
  }

  /**
   * Calling delete function from model class
   * @param  int  id of item
   */
  public function delete($id)
  {
    $this->model_asset->delete_by_id($id);
    redirect('/media-library');
  }

  public function ajax_delete($id)
  {
    $result = $this->model_asset->delete_by_id($id);
    if ($result) {
      echo json_encode(array('status' => 'success', 'messages' => 'Image has been deleted.'));
    }
    else {
      echo json_encode(array('status' => 'failed', 'messages' => 'Unknown error.'));
    }
  }

  public function widgetupload($page_section_id)
  {
    $widget_name = $this->input->post('widget_name');
    $this->ajax_upload_with_generated_category($widget_name, $page_section_id);
  }

  /**
   * Update/delete multiple images
   * @param int category ID
   * @return bool
   */
  public function widgetupdate($category_id)
  {
    $category_id = (int)$category_id;
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
    //var_dump($_FILES['asset_file']);
    // define constant
    $this->load->library('image_lib');

    $public_path = $this->get_public_upload_path();


    $upload_dir = $this->get_full_upload_path();

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

    $filename = strip_extension($_FILES['asset_file']['name']);
    $extension = get_extension($_FILES['asset_file']['name']);
    $has_error = TRUE;

    $this->check_and_make_directory($upload_dir);

    $upload_config['file_name']     = $filename;
    $upload_config['upload_path']   = $upload_dir;


    $this->load->library('upload', $upload_config);

    //die($upload_dir);

    //upload succesfull
    if ($this->upload->do_upload( 'asset_file' )) {

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

}
?>
