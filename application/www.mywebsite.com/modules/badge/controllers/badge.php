<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Badge Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	        Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */
class Badge extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      //redirect($this->config->item('login_page'));
    }

    $this->load->config('badge/badge', TRUE);
    $this->load->model('badge/badge_model');
    $this->load->model('account/account_model');
    $this->load->library('pagination');

    $this->load->library('asset/asset_item');
    
    $this->load->helper('asset/asset');    
    $this->load->helper('account/account');
    $this->load->helper('badge/badge');
    
    
    $this->module_url = $this->config->item('module_url', 'badge');
    $this->module_header = $this->lang->line('hotcms_badge');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_badge'));
    
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'badge');
    
        /**
         * prepare module information
         * can be overriden in each function
         */
        $this->aModuleInfo = array(
            'name' => 'badge',
            'title' => $this->config->item('module_title', 'badge'),
            'meta_title' => $this->config->item('module_title', 'badge'),
            'url' => $this->config->item('module_url', 'badge'),
            'meta_description' => $this->config->item('meta_description', 'badge'),
            'meta_keyword' => $this->config->item('meta_keyword', 'badge'),
            'css' => $this->config->item('css', 'badge'),
            'javascript' => $this->config->item('js', 'badge')
        );    
  }

  /**
   * list all items
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    if (!has_permission('manage_badge')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/badge.js';

    // search/filter form
    $default_sort_by = 'id'; // default field to sort by
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
        //'country' => $this->input->post('country_code'),
        //'status' => $this->input->post('status'),
      );
      $this->session->set_userdata('badge_filters', $filters);
      redirect('badge');
    }
    $filters = $this->session->userdata('badge_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'country' => '',
        'status' => '',
      );
    }
    $data['filters'] = $filters;

    //active filters string
    $active_filters = '';
    $separator = false;
    foreach ($filters as $filter_key => $filter_value) {
      if ($filter_key == 'keyword' && $filter_value != '') {
        $active_filters = 'Keyword - ' . $filter_value;
        $separator = true;
      }
      if ($filter_key == 'country' && $filter_value != '') {
        if ($separator) {
          $active_filters.= ', ';
        }
        $active_filters.= 'Country: ';
        foreach ($filter_value as $code) {
          $active_filters.= $code . ', ';
        }
        $active_filters = substr($active_filters, 0, -2);
        $separator = true;
      }
      if ($filter_key == 'status' && $filter_value != '') {
        if ($separator) {
          $active_filters.= ', ';
        }
        $active_filters.= 'Status: ';

        foreach ($filter_value as $code) {
          switch ($code) {
            case 1:
              $active_filters.= lang('hotcms_confirmed') . ', ';
              break;
            case 2:
              $active_filters.= lang('hotcms_closed') . ', ';
              break;
            case 0:
              $active_filters.= lang('hotcms_pending') . ', ';
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

    //$data['form']['country_code_options'] = array('all' => 'All') + list_country_array();
    $data['form']['country_code_options'] = list_country_array();
    $data['form']['status_options'] = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Closed');
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->badge_model->badge_count($filters);
    $badges = $this->badge_model->badge_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    
    /*
    foreach( $badges as $badge){
       $count_stores = $this->badge_model->count_stores($badge->id);     
       $badge->stores = $count_stores->count;
       $count_users = $this->badge_model->count_users($badge->id);     
       $badge->users = $count_users->count;       
      }     
     */
    //die(var_dump)
    $data['badges'] = $badges;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('badge_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'badge/badge_leftbar', NULL, 'badge/badge', NULL);
  }

  /**
   * creates new badge
   */
  public function create()
  {
    if (!has_permission('manage_badge')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create badge";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " badge";

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('description', strtolower(lang('hotcms_description')), 'trim|required');

    if ($this->form_validation->run()) {
      $badge_id = $this->badge_model->insert($this->input->post());
      if ($badge_id > 0) {
        $this->add_message('confirm', 'Badge was created.');
        redirect('badge');
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['form']['description_input'] = $this->_create_text_input('description', $this->input->post('description'), 50, 20, 'text');
   
    //$pagination_config = pagination_configuration();
    //$data['badges'] = $this->badge_model->badge_list(FALSE, TRUE, 1, $pagination_config['per_page']);
    $data['index_page_num'] = $this->session->userdata('badge_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'badge/badge_leftbar', NULL, 'badge/badge_create', NULL);
  }

  /**
   * edit badge
   * @param  int  $id
   * @param  int  page number
   */
  public function edit($id, $page_num = 1)
  {
    if (!has_permission('manage_badge')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit badge";
    
    $data['java_script'] = 'modules/' . $this->module_url . '/js/badge_edit.js';

    //$this->load->model('user/user_model');

    $data['badge_id'] = $id;
    $badge = $this->badge_model->badge_load($id, FALSE);
    
    if($badge->icon_image_id != 0){  
      $badgeItems['icon'] = asset_load_item($badge->icon_image_id);
    }
    if($badge->big_image_id != 0){  
      $badgeItems['hover'] = asset_load_item($badge->big_image_id);
    }    
    if (!empty($badgeItems)){
      $badge->items = $badgeItems;
    }else{
      $badge->items = '';  
    }
    $data['currentItem'] = $badge;

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('description', strtolower(lang('hotcms_description')), 'trim|required');

    

    //$data['selected_country'] = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;

    if ($this->form_validation->run()) {
      // update badge
      $this->badge_model->badge_update($id, $this->input->post());
      // reload
        $badge = $this->badge_model->badge_load($id, FALSE);

        if($badge->icon_image_id != 0){  
          $badgeItems['icon'] = asset_load_item($badge->icon_image_id);
        }
        if($badge->big_image_id != 0){  
          $badgeItems['hover'] = asset_load_item($badge->big_image_id);
        }    
        if (!empty($badgeItems)){
          $badge->items = $badgeItems;
        }else{
          $badge->items = '';  
        }
        
        $data['currentItem'] = $badge;
        $this->add_message('confirm', 'Badge was updated.');
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    // display edit form
    $data['form']['name_input'] = $this->_create_text_input('name', $badge->name, 50, 20, 'text');
    $data['form']['description_input'] = $this->_create_text_input('description', $badge->description, 50, 20, 'text');

    $data['form']['badge_status_options'] = array('0' => 'Draft', '1' => 'Active', '2' => 'Archived');

    $data['index_page_num'] = $this->session->userdata('badge_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'badge/badge_leftbar', NULL, 'badge/badge_edit', NULL);
  }

  /**
   * delete a badge
   * @param  int  id of the item to be deleted
   */
  public function delete($id)
  {
    if (!has_permission('manage_badge')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $badge = $this->badge_model->badge_load($id, FALSE);
    $result = $this->badge_model->badge_delete($id);
    if ($result) {
      $this->add_message('confirm', 'Badge ' . $badge->name . ' was deleted.');
    }
    else {
      $this->add_message('error', 'Failed to delete badge ' . $badge->name . '.');
    }
    redirect('badge/index/' . $this->session->userdata('badge_index_page_num'));
  }
  
  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $training_id = 0) {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $badge_id > 0) {
      $result = $this->badge_model->asset_update($field_id, $attr);
    } else {
      $result = TRUE;
    }

    $image = NULL;
    $asset_id = (int) $asset_id;
    if ($asset_id > 0) {
      $image = asset_load_item($asset_id);
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;

    $asset_category_id = 1; // default image category
    $data['asset_category_id'] = $asset_category_id;
    // build the config form
    $category_context = 'badge_default';
    $asset_categories = asset_list_categories(array('context' => $category_context));

    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    $args['asset_category_id'] = $asset_category_id;
    $data['media_upload_ui'] = asset_upload_ui($args);
    $images = asset_images_ui($args + array('single_selection' => 'ON'));
    $data['media_library_ui'] = $images['formatted'];
    $content = $this->load->view('badge_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }  
  
  /**
   * Updates a icon image
   */
  public function ajax_update_image($badge_id, $asset_id) {
    $id = (int) $badge_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->badge_model->update_icon_image($id,$asset_id);
        //$messages = $draft->messages() . $draft->errors();
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Badge not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
  /**
   * Updates a big image image
   */
  public function ajax_update_big_image($badge_id, $asset_id) {
    $id = (int) $badge_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->badge_model->update_big_image($id,$asset_id);
        //$messages = $draft->messages() . $draft->errors();
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Badge not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
    /* function for call model fuction to store sequence in database */

    public function ajax_save_badge_sequence() {

        // load array
        $sequence = explode('_', $_GET['order']);
        // load model
        $this->load->model('badge_model');
        // loop sequence...
        $count = 0;
        foreach ($sequence as $id) {
            if(!empty($id)){
              $this->badge_model->save_badge_sequence('badge', $id, ++$count);
            }
        }
    }
    
    /*
     * Profile badge page (url badges/[screenname]
     * 
     * @param string sceen_name
     * 
     */
    
    public function badges_page($screenname) {

        $user = $this->account_model->get_user_public_data(urldecode($screenname));
        if (empty($user)) {
            redirect('page-not-found');
            show_404();
        }
        $this->data['user'] = $user;

     
        $this->data['screen_name'] = $screenname;
        //load badges
        $badges = get_all_badges();
        foreach($badges as $badge){
              if($badge->icon_image_id != 0){  
                $badge->icon = asset_load_item($badge->icon_image_id);
              }
              if($badge->big_image_id != 0){  
                $badge->hover = asset_load_item($badge->big_image_id);
              }              
        }        
        $this->data['all_badges'] = $badges;
        $user_badges = account_get_badges($user->user_id);
        $this->data['user_badges'] = $user_badges;

        
        self::loadModuleView($this->aModuleInfo, $this->data, 'badges_user');
    }
    
            /*
 * Add keener badge
 * 
 * every 1st of month - 10 users with most quizzes
 * 
 * 
 * @return bool
 */

  public function badge_keener($protect)
  {
    if($protect == 'hottomali_secret'){
        $CI =& get_instance();
        // check permission

        $CI->load->helper('account/account');
        $CI->load->model('badge/badge_model');    
        //get top 10 user for last month
        $users = $this->badge_model->get_users_for_keener();
        foreach($users as $user){
            account_add_badge($user->user_id, 'keener',$user->quiz_number);
        }
    }else{
        die('false attempt');  
    }
  }       
}
?>
