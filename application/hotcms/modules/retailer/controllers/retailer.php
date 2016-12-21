<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retailer Controller
 *
 * @package		HotCMS
 * @author		Jeffrey Tang
 * @copyright	Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */
class Retailer extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }

    $this->load->config('retailer/retailer', TRUE);
    $this->load->model('retailer/retailer_model');
    $this->load->library('pagination');
    
    $this->load->helper('asset/asset');
    $this->load->helper("array");

    $this->module_url = $this->config->item('module_url', 'retailer');
    $this->module_header = $this->lang->line('hotcms_organization');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_organization'));
  }

  /**
   * list all items
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = "modules/retailer/js/retailer.js";

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
        'country' => $this->input->post('country_code'),
        'status' => $this->input->post('status'),
      );
      $this->session->set_userdata('retailer_filters', $filters);
      redirect($this->module_url);
    }
    $filters = $this->session->userdata('retailer_filters');
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
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'country_code' => $filters['country'], 'status' => $filters['status'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->retailer_model->retailer_count($filters);
    $retailers = $this->retailer_model->retailer_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    
    foreach( $retailers as $retailer){
       //$active_count_stores = $this->retailer_model->count_stores($retailer->id,'active');     
       //$retailer->active_stores = $active_count_stores->count;
       //$pending_count_stores = $this->retailer_model->count_stores($retailer->id,'pending');     
       //$retailer->pending_stores = $pending_count_stores->count;       
       $count_users = $this->retailer_model->count_users($retailer->id);     
       $retailer->users = $count_users->count;       
      }
    
    
    $data['retailers'] = $retailers;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('retailer_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer', NULL);
  }

  /**
   * creates new retailer
   */
  public function create()
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = lang("hotcms_create")." ".lang("hotcms_organization");
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " ".lang("hotcms_organization");

    //TODO: validate unique retailer name
    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');
    $this->form_validation->set_rules('categories', strtolower(lang('hotcms_categories')), 'required');
    $this->form_validation->set_rules('types', strtolower(lang('hotcms_types')), 'required');

    $data['selected_country'] = $this->input->post('country_code') > '' ? $this->input->post('country_code') : '';
    $selected_categories = $this->input->post('categories');
    $all_categoties = $this->retailer_model->get_all_categories();
    $selected_types = $this->input->post("types");
    $all_types = $this->retailer_model->get_all_types();

    if ($this->form_validation->run()) {
      $retailer_id = $this->retailer_model->retailer_insert($this->input->post());     
      if ($retailer_id > 0) {
        // update retailer-category combinations
        foreach ($all_categoties as $category) {
            $this->retailer_model->delete_retailer_categories($retailer_id, $category->id);
        }
        if (is_array($selected_categories) && !empty($selected_categories)) {
            foreach ($all_categoties as $category) {
                if (in_array($category->name, $selected_categories)) {
                    $this->retailer_model->insert_retailer_category($retailer_id, $category->id);
                }
            }
        }
        // update organizations (n) to categories (n) relationship
        foreach ($all_types as $type) {
            $this->retailer_model->delete_organization_in_type($retailer_id, $type->id);
        }
        if (is_array($selected_types) && !empty($selected_types)) {
            foreach ($all_types as $type) {
                if (in_array($type->name, $selected_types)) {
                    $this->retailer_model->insert_organization_in_type($retailer_id, $type->id);
                }
            }
        }
        //add default store
        $data_store = array('store_name' => 'Default Store','status' => 0, 'store_num' => 0, 'street_1' => 'Address 1', 'city' => 'Vancouver', 'province' => 'BC', 'country' => 'CA');
        $this->retailer_model->store_insert($retailer_id, $data_store);
          
        $this->add_message('confirm', lang("hotcms_organization").lang("hotcms_created_item"));
        redirect($this->module_url."/edit/" . $retailer_id);
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['country_code_options'] = array('' => 'Select') + list_country_array();
    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['form']['website_input'] = $this->_create_text_input('website', $this->input->post('website'), 50, 20, 'text');
    $data['form']['status_pending'] = array(
      'id' => 'status_pending',
      'name' => 'status',
      'value' => '0',
      'checked' => $this->input->post('status') == 0,
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_confirmed'] = array(
      'id' => 'status_confirmed',
      'name' => 'status',
      'value' => '1',
      'checked' => $this->input->post('status') == 1,
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_closed'] = array(
      'id' => 'status_closed',
      'name' => 'status',
      'value' => '2',
      'checked' => $this->input->post('status') == 2,
      'style' => 'display:inline-block;margin-left:5px'
    );
    
    foreach ($all_categoties as $category) {
      if (is_array($selected_categories) && !empty($selected_categories)) {
          $checked = in_array($category->name, $selected_categories);
      } else {
          $checked = false;
      }
      $data['categories'][$category->name . "_category_checkbox"] = $this->_create_checkbox_input('categories[]', $category->name, $category->name, $checked, 'margin:10px');
    }
    
    foreach ($all_types as $type) {
      if (is_array($selected_types) && !empty($selected_types)) {
          $checked = in_array($type->name, $selected_types);
      } else {
          $checked = false;
      }
      $data['types'][$type->name . "_type_checkbox"] = $this->_create_checkbox_input('types[]', $type->name, $type->name, $checked, 'margin:10px');
    }

    $data['index_page_num'] = $this->session->userdata('retailer_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_create', NULL);
  }

  /**
   * edit retailer
   * @param  int  $id
   * @param  int  page number
   */
  public function edit($id)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = lang("hotcms_edit")." ".lang("hotcms_organization");

    $data['java_script'] = 'modules/retailer/js/retailer_edit.js';

    $data['retailer_id'] = $id;
    $retailer = $this->retailer_model->retailer_load($id, FALSE);

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');
    $this->form_validation->set_rules('categories', strtolower(lang('hotcms_categories')), 'required');
    $this->form_validation->set_rules('types', strtolower(lang('hotcms_types')), 'required');

    $data['selected_country'] = $_POST ? $this->input->post('country_code') : $retailer->country_code;
    $selected_categories = (($this->input->post('categories') === FALSE) ? $this->retailer_model->get_categories_names_by_reailer_id($id) : $this->input->post('categories'));
    $all_categoties = $this->retailer_model->get_all_categories();
    $selected_types = (($this->input->post('types') === FALSE) ? $this->retailer_model->get_types_names_by_organization_id($id) : $this->input->post('types'));
    $all_types = $this->retailer_model->get_all_types();

    if ($this->form_validation->run()) {
//      $old_name = strtolower(trim($retailer->name));
//      $new_name = strtolower(trim($this->input->post('name')));
      foreach ($all_categoties as $category) {
        $this->retailer_model->delete_retailer_categories($id, $category->id);
      }
      foreach ($all_categoties as $category) {
        if (in_array($category->name, $selected_categories)) {
            $this->retailer_model->insert_retailer_category($id, $category->id);
        }
      }
      // update organizations (n) to categories (n) relationship
      foreach ($all_types as $type) {
        $this->retailer_model->delete_organization_in_type($id, $type->id);
      }
      foreach ($all_types as $type) {
        if (in_array($type->name, $selected_types)) {
            $this->retailer_model->insert_organization_in_type($id, $type->id);
        }
      }
      
      // update retailer
      $this->retailer_model->retailer_update($id, $this->input->post());
      // reload
      $retailer = $this->retailer_model->retailer_load($id, FALSE);
      $this->add_message('confirm', lang("hotcms_organization").lang("hotcms_updated_item"));
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
      $selected_categories = $this->input->post('categories');
      $selected_types = $this->input->post('types');
    }

    // display edit form
    $data['form']['country_code_options'] = array('' => 'Select') + list_country_array();
    $data['form']['name_input'] = $this->_create_text_input('name', $retailer->name, 50, 20, 'text');
    $data['form']['website_input'] = $this->_create_text_input('website', $retailer->website, 50, 20, 'text');
    $data['form']['status_pending'] = array(
      'id' => 'status_pending',
      'name' => 'status',
      'value' => '0',
      'checked' => ($_POST ? $this->input->post('status') == 0 : $retailer->status == 0),
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_confirmed'] = array(
      'id' => 'status_confirmed',
      'name' => 'status',
      'value' => '1',
      'checked' => ($_POST ? $this->input->post('status') == 1 : $retailer->status == 1),
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_closed'] = array(
      'id' => 'status_closed',
      'name' => 'status',
      'value' => '2',
      'checked' => ($_POST ? $this->input->post('status') == 2 : $retailer->status == 2),
      'style' => 'display:inline-block;margin-left:5px'
    );
    
    $data['form_locations'] = modules::run('contact/controller/get_edit_forms', 'organization', $id);
    
    if($retailer->logo_image_id != 0){  
      $retailer->logo = asset_load_item($retailer->logo_image_id);
    }   
    
    //retailer_category
    foreach ($all_categoties as $category) {
      if (is_array($selected_categories) && !empty($selected_categories)) {
          $checked = in_array($category->name, $selected_categories);
      } else {
          $checked = false;
      }
      $data['categories'][$category->name . "_category_checkbox"] = $this->_create_checkbox_input('categories[]', $category->name, $category->name, $checked, 'margin:10px');
    }
    
    foreach ($all_types as $type) {
      if (is_array($selected_types) && !empty($selected_types)) {
          $checked = in_array($type->name, $selected_types);
      } else {
          $checked = false;
      }
      $data['types'][$type->name . "_type_checkbox"] = $this->_create_checkbox_input('types[]', $type->name, $type->name, $checked, 'margin:10px');
    }
    
    
    $data['currentItem'] = $retailer;
    //$pagination_config = pagination_configuration();
    $data['retailers'] = $this->retailer_model->retailer_list(FALSE, TRUE, 1, 0);
    $data['index_page_num'] = $this->session->userdata('retailer_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_edit', NULL);
  }

  /**
   * delete a retailer
   * @param  int  id of the item to be deleted
   */
  function delete($id) {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $retailer = $this->retailer_model->retailer_load($id, FALSE);
    $result = $this->retailer_model->retailer_delete($id);
    if (is_array($result)) {
      $this->add_message('error', lang("hotcms_organization")." ".$retailer->name." still has ".strtolower(lang("hotcms_store"))." ".implode(", ", $result).".");
    } elseif ($result) {
      $this->add_message('confirm', lang("hotcms_organization")." " . $retailer->name . lang("hotcms_deleted_item"));
    } else {
      $this->add_message('error', "Failed to delete ".lang("hotcms_organization")." " . $retailer->name . ".");
    }
    redirect($this->module_url."/index/" . $this->session->userdata('retailer_index_page_num'));
  }

  /**
   * list all stores
   * @param  int  retailer ID
   * @param  int  page number
   */
  public function store($retailer_id, $page_num = 1)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " " . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/retailer/js/retailer.js';

    // search/filter form
    $default_sort_by = 'store_name'; // default field to sort by
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
        'status' => $this->input->post('status'),
      );
      $this->session->set_userdata('retailer_stroe_filters', $filters);
      redirect('organization/store/' . $retailer_id);
    }
    $filters = $this->session->userdata('retailer_stroe_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'status' => '',
      );
    }
    $filters['retailer_id'] = $retailer_id;
    $data['filters'] = $filters;

    //active filters string
    $active_filters = '';
    $separator = false;
    foreach ($filters as $filter_key => $filter_value) {
      if ($filter_key == 'keyword' && $filter_value != '') {
        $active_filters = 'Keyword - ' . $filter_value;
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

    $data['form']['status_options'] = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Closed');
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'status' => $filters['status'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    $data['retailer'] = $this->retailer_model->retailer_load($retailer_id, FALSE);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/store/' . $retailer_id;
    $pagination_config['uri_segment'] = 5;
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->retailer_model->store_count($filters, FALSE);
    $data['stores'] = $this->retailer_model->store_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('retailer_store_page_num', $page_num);
    $data['index_page_num'] = $this->session->userdata('retailer_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/store', NULL);
  }

  /**
   * creates new store
   * @param  int  retailer ID
   */
  public function store_create($retailer_id)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create store";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " " . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/retailer/js/retailer.js';

    $data['retailer'] = $this->retailer_model->retailer_load($retailer_id, FALSE);

    $this->form_validation->set_rules('store_name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');
    $this->form_validation->set_rules('province', strtolower(lang('hotcms_province_state')), 'trim|required');
    
    if($this->input->post('status') == 1){
        //$this->form_validation->set_rules('store_num', strtolower(lang('hotcms_store_num')), 'trim|required');
        $this->form_validation->set_rules('street_1', strtolower(lang('hotcms_address')), 'trim|required');
        $this->form_validation->set_rules('city', strtolower(lang('hotcms_city')), 'trim|required');
        $this->form_validation->set_rules('postal_code', strtolower(lang('hotcms_city')), 'trim|required');
    }    

    $data['selected_province'] = $this->input->post('province') > '' ? $this->input->post('province') : '';
    $data['selected_country'] = $this->input->post('country_code') > '' ? $this->input->post('country_code') : '';

    if ($this->form_validation->run()) {
      $store_id = $this->retailer_model->store_insert($retailer_id, $this->input->post());
      if ($store_id > 0) {
        $this->add_message('confirm', lang("hotcms_store").lang("hotcms_created_item"));
        redirect('organization/store_edit/'. $store_id);
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    // build the form
    $data['form']['country_code_options'] = array('' => 'Select') + list_country_array();
    $selected_country_code = $_POST ? $this->input->post('country_code') : '';
    if ($selected_country_code > '') {
      $data['form']['province_options'] = array('' => 'Select') + list_province_array($selected_country_code);
    }
    else {
      $data['form']['province_options'] = array('' => 'Select');
    }
    $data['form']['store_name_input'] = $this->_create_text_input('store_name', $this->input->post('store_name'), 50, 20, 'text');
    $data['form']['store_num_input'] = $this->_create_text_input('store_num', $this->input->post('store_num'), 50, 20, 'text');
    $data['form']['street_1_input'] = $this->_create_text_input('street_1', $this->input->post('street_1'), 50, 20, 'text');
    $data['form']['street_2_input'] = $this->_create_text_input('street_2', $this->input->post('street_2'), 50, 20, 'text');
    $data['form']['city_input'] = $this->_create_text_input('city', $this->input->post('city'), 50, 20, 'text');
    $data['form']['postal_code_input'] = $this->_create_text_input('postal_code', $this->input->post('postal_code'), 50, 20, 'text');
    $data['form']['phone_input'] = $this->_create_text_input('phone', $this->input->post('phone'), 50, 20, 'text');
    $data['form']['email_input'] = $this->_create_text_input('email', $this->input->post('email'), 50, 20, 'text');
    $data['form']['fax_input'] = $this->_create_text_input('fax', $this->input->post('fax'), 50, 20, 'text');
    $data['form']['longitude_input'] = $this->_create_text_input('longitude', $this->input->post('longitude'), 50, 20, 'text');
    $data['form']['latitude_input'] = $this->_create_text_input('latitude', $this->input->post('latitude'), 50, 20, 'text');
    $data['form']['status_pending'] = array(
      'id' => 'status_pending',
      'name' => 'status',
      'value' => '0',
      'checked' => $this->input->post('status') == 0,
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_confirmed'] = array(
      'id' => 'status_confirmed',
      'name' => 'status',
      'value' => '1',
      'checked' => $this->input->post('status') == 1,
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_closed'] = array(
      'id' => 'status_closed',
      'name' => 'status',
      'value' => '2',
      'checked' => $this->input->post('status') == 2,
      'style' => 'display:inline-block;margin-left:5px'
    );

    $data['index_page_num'] = $this->session->userdata('retailer_store_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/store_create', NULL);
  }

  /**
   * edit store
   * @param  int  retailer ID
   * @param  int  $id
   */
  public function store_edit($id)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . " " . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/retailer/js/retailer.js';

    $store = $this->retailer_model->store_load($id, FALSE);
    $data['retailer'] = $this->retailer_model->retailer_load($store->retailer_id, FALSE);
    $data['store_id'] = $id;
    $data['currentItem'] = $store;

    $this->form_validation->set_rules('store_name', strtolower(lang('hotcms_name')), 'trim|required');
    //$this->form_validation->set_rules('store_num', strtolower(lang('hotcms_store_num')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');
    $data['selected_retailer'] = $_POST ? $this->input->post('retailer') : $data['retailer'];
    $data['selected_province'] = $_POST ? $this->input->post('province') : $data['currentItem']->province;
    $data['selected_country'] = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;

    if($this->input->post('status') == 1){
        //$this->form_validation->set_rules('store_num', strtolower(lang('hotcms_store_num')), 'trim|required');
        $this->form_validation->set_rules('street_1', strtolower(lang('hotcms_address')), 'trim|required');
        $this->form_validation->set_rules('city', strtolower(lang('hotcms_city')), 'trim|required');
        $this->form_validation->set_rules('postal_code', strtolower(lang('hotcms_city')), 'trim|required');
    }
    if ($this->form_validation->run()) {
      // update
      $this->retailer_model->store_update($id, $this->input->post());
      // reload
      $data['currentItem'] = $this->retailer_model->store_load($id, FALSE);
      //die(var_dump($data['currentItem']));
      $this->add_message('confirm', lang("hotcms_store").lang("hotcms_updated_item"));
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    // display edit form
    $all_retailers = $this->retailer_model->retailer_list(FALSE, TRUE, 1, 0);
    $retailer_dropdown_options = array();
    foreach ($all_retailers as $retailer) {
      $retailer_dropdown_options[$retailer->id] = $retailer->name;
    }
    $data["form"]["retailer_options"] = $retailer_dropdown_options;
    $data['form']['country_code_options'] = array('' => 'Select') + list_country_array();
    $selected_country_code = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;    
    if ($selected_country_code > '') {
      $data['form']['province_options'] = array('' => 'Select') + list_province_array($selected_country_code);
    }
    else {
      $data['form']['province_options'] = array('' => 'Select');
    }
    $data['form']['store_name_input'] = $this->_create_text_input('store_name', ($_POST ? $this->input->post('store_name') : $data['currentItem']->store_name), 50, 20, 'text');
    $data['form']['store_num_input'] = $this->_create_text_input('store_num', ($_POST ? $this->input->post('store_num') : $data['currentItem']->store_num), 50, 20, 'text');
    $data['form']['street_1_input'] = $this->_create_text_input('street_1', ($_POST ? $this->input->post('street_1') : $data['currentItem']->street_1), 50, 20, 'text');
    $data['form']['street_2_input'] = $this->_create_text_input('street_2', ($_POST ? $this->input->post('street_2') : $data['currentItem']->street_2), 50, 20, 'text');
    $data['form']['city_input'] = $this->_create_text_input('city', ($_POST ? $this->input->post('city') : $data['currentItem']->city), 50, 20, 'text');
    $data['form']['postal_code_input'] = $this->_create_text_input('postal_code', ($_POST ? $this->input->post('postal_code') : $data['currentItem']->postal_code), 50, 20, 'text');
    $data['form']['phone_input'] = $this->_create_text_input('phone', ($_POST ? $this->input->post('phone') : $data['currentItem']->phone), 50, 20, 'text');
    $data['form']['email_input'] = $this->_create_text_input('email', ($_POST ? $this->input->post('email') : $data['currentItem']->email), 50, 20, 'text');
    $data['form']['fax_input'] = $this->_create_text_input('fax', ($_POST ? $this->input->post('fax') : $data['currentItem']->fax), 50, 20, 'text');
    $data['form']['longitude_input'] = $this->_create_text_input('longitude', ($_POST ? $this->input->post('longitude') : $data['currentItem']->longtitude), 50, 20, 'text');
    $data['form']['latitude_input'] = $this->_create_text_input('latitude', ($_POST ? $this->input->post('latitude') : $data['currentItem']->latitude), 50, 20, 'text');    
    $data['form']['status_pending'] = array(
      'id' => 'status_pending',
      'name' => 'status',
      'value' => '0',
      'checked' => ($_POST ? $this->input->post('status') == 0 : $data['currentItem']->status == 0),
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_confirmed'] = array(
      'id' => 'status_confirmed',
      'name' => 'status',
      'value' => '1',
      'checked' => ($_POST ? $this->input->post('status') == 1 : $data['currentItem']->status == 1),
      'style' => 'display:inline-block;margin-left:5px'
    );
    $data['form']['status_closed'] = array(
      'id' => 'status_closed',
      'name' => 'status',
      'value' => '2',
      'checked' => ($_POST ? $this->input->post('status') == 2 : $data['currentItem']->status == 2),
      'style' => 'display:inline-block;margin-left:5px'
    );

    $data['index_page_num'] = $this->session->userdata('retailer_store_page_num');
    
    $data['count_users'] = $this->retailer_model->count_users($data['currentItem']->id, "store");     


    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/store_edit', NULL);
  }

  /**
   * delete a store
   * @param  int  retailer ID
   * @param  int  id of the item to be deleted
   */
  public function store_delete($retailer_id, $id)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $store = $this->retailer_model->store_load($id, FALSE);
    $result = $this->retailer_model->store_delete($id);
    if (is_array($result)) {
      $this->add_message('error', lang("hotcms_user")." ".implode(", ", $result)." still belong to ".strtolower(lang("hotcms_store"))." ".$store->store_name);
    } elseif ($result) {
      $this->add_message('confirm', lang("hotcms_store")." " . $store->store_name . lang("hotcms_deleted_item"));
    }
    else {
      $this->add_message('error', "Failed to ".strtolower(lang("hotcms_delete"))." ".  strtolower(lang("hotcms_store"))." " . $store->store_name . ".");
    }
    redirect($this->module_url."/store/" . $retailer_id . "/" . $this->session->userdata('retailer_store_page_num'));
  }

  /**
   * list all retailers and permissions
   * @param  int  page number
  public function access($page_num = 1)
  {
    if (!has_permission('manage_retailer_permission')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text;    
    $data['module_header'] = $this->module_header;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/retailer.js';

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
        'country' => $this->input->post('country_code'),
      );
      $this->session->set_userdata('retailer_access_filters', $filters);
      redirect('retailer/access');
    }
    $filters = $this->session->userdata('retailer_access_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'country' => '',
      );
    }
    $filters['status'] = 1;
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

//      if ($filter_key == 'status' && $filter_value != '') {
//        if ($separator) {
//          $active_filters.= ', ';
//        }
//        $active_filters.= 'Status: ';
//
//        foreach ($filter_value as $code) {
//          switch ($code) {
//            case 1:
//              $active_filters.= lang('hotcms_confirmed') . ', ';
//              break;
//            case 2:
//              $active_filters.= lang('hotcms_closed') . ', ';
//              break;
//            case 0:
//              $active_filters.= lang('hotcms_pending') . ', ';
//              break;
//            default:
//              break;
//          }
//        }
//        $active_filters = substr($active_filters, 0, -2);
//        $separator = true;
//      }

    }
    if ($separator == false)
      $active_filters.= 'None';

    $data['active_filters'] = $active_filters;
    

    $data['form']['country_code_options'] = array('' => 'All') + list_country_array();
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction']);

    //set permissions for retailers - hardcoded
    $retailer_permission = array (
      'earn_points' => 'Earn points',  
      'earn_draws' => 'Earn draws',              
      'spent_points' => 'Spent points',              
    );
    
    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/access/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->retailer_model->retailer_count($filters);
    $retailers = $this->retailer_model->retailer_list($filters, TRUE, $page_num, $pagination_config['per_page']);

    foreach( $retailers as $retailer){
        $count_stores = $this->retailer_model->count_stores($retailer->id);     
        $retailer->stores = $count_stores->count;
        $count_users = $this->retailer_model->count_users($retailer->id);     
        $retailer->users = $count_users->count; 
        //set checkboxes for retailer permissions
        $retailer_active_permissions = array();
        foreach ($this->retailer_model->get_retailer_permissions_by_user_id($retailer->id) as $p) {
            $retailer_active_permissions[$p->permission_key] = $p->permission_key;
        }
        //$retailer->per = $retailer_active_permissions;
        
        foreach ($retailer_permission as $k => $v) {
           $retailer->percheck = array();
            //var_dump($retailer_active_permissions);
            if (!empty($retailer_active_permissions)) {
                $checked = in_array($k, $retailer_active_permissions);
            } else {
                $checked = false;
            }
            //$retailer->percheck[$k] = $checked;
            $permission_checkbox[$k] = $this->_create_checkbox_input('permissions[]', $v, $k, $checked, $retailer->id,'ret_per');
        }    
        
        $retailer->per_chec = $permission_checkbox;
        
    }
    
    $data['retailers'] = $retailers;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();

    // build the form
    $data['form']['access_options'] = $this->retailer_model->list_permissions();

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_permission', NULL);
  }
   */

  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  retailer ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $retailer_id = 0) {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $retailer_id > 0) {
      $result = $this->retailer_model->asset_update($field_id, $attr);
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

    $asset_category_id = 35; // default image category
    $data['asset_category_id'] = $asset_category_id;
    // build the config form
    $category_context = 'retailer_logo';
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
    $content = $this->load->view('retailer_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }  
  
  /**
   * Updates a icon image
   */
  public function ajax_update_image($retailer_id, $asset_id) {
    $id = (int) $retailer_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      try {
        $result = $this->retailer_model->update_logo_image($id,$asset_id);
        $messages = 'Logo image added.';
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Retailer not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }  
  
  /**
   * list all usres for a retailer or store
   * @param int    row ID
   * @param string determine retailer or store
   * @param int    page number
   */
  public function users($row_id, $table = "retailer", $page_num = 1)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " " . strtolower(lang('hotcms_store'));
    $data['java_script'] = 'modules/retailer/js/retailer.js';

    // search/filter form
    $default_sort_by = 'last_name'; // default field to sort by
    $default_per_page = 10; // default items to display per page
    
    if($this->input->get('filter') == 'clear'){
      $this->session->set_userdata('retailer_user_filters');
    }
    
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
        'status' => $this->input->post('status'),
      );
      $this->session->set_userdata('retailer_user_filters', $filters);
      redirect($this->module_url."/users/".$row_id."/".$table);
    }
    $filters = $this->session->userdata('retailer_user_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'status' => '',
      );
    }
    $filters['row_id'] = $row_id;
    $filters['table'] = $table;
    $data['filters'] = $filters;

    //active filters string
    $active_filters = '';
    $separator = false;
    foreach ($filters as $filter_key => $filter_value) {
      if ($filter_key == 'keyword' && $filter_value != '') {
        $active_filters = 'Keyword - ' . $filter_value;
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

    $data['form']['status_options'] = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Closed');
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'status' => $filters['status'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    $data["table"] = $table;
    $data[$table] = $this->retailer_model->{$table."_load"}($row_id, FALSE);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url').$this->module_url."/users/".$row_id."/".$table;
    $pagination_config['uri_segment'] = 6;
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->retailer_model->count_users($row_id, $table)->count;
    $data['users'] = $this->retailer_model->users_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('retailer_store_page_num', $page_num);
    $data['index_page_num'] = $this->session->userdata('retailer_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/users', NULL);
  }  
  
    /**
   * list all organization's categories
   * @param  int  page number
   */
  public function categories($page_num = 1)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text ."'s ".lang("hotcms_category");    
    $data['module_header'] = $this->module_header;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/retailer.js';

    // search/filter form
    //$default_sort_by = 'name'; // default field to sort by
    //$default_per_page = 10; // default items to display per page
    
    // paginate configuration
    //$pagination_config = pagination_configuration();
    //$pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/access/';
    //$pagination_config['per_page'] = $default_per_page;
    //$pagination_config['total_rows'] = $this->retailer_model->retailer_count($filters);
    $retailer_categories = $this->retailer_model->get_all_categories();


    
    $data['retailer_categories'] = $retailer_categories;
    // paginate
    //$this->pagination->initialize($pagination_config);
    //$data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('categories_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_categories', NULL);
  }
  
  /**
   * edit retailer's category
   * @param  int  $id
   * @param  int  page number
   */
  public function category_edit($id, $page_num = 1)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = lang("hotcms_edit")." ".strtolower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_category"));

    /*$data['java_script'] = 'modules/' . $this->module_url . '/js/retailer_edit.js';*/

    $data['category_id'] = $id;
    $retailer_category = $this->retailer_model->retailer_category_load($id);

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');

    if ($this->form_validation->run()) { 
      // update retailer
      $this->retailer_model->retailer_category_update($id, $this->input->post());
      // reload
      //$retailer_category = $this->retailer_model->retailer_category_load(category_id);
      $this->add_message('confirm', lang("hotcms_organization")." ".  strtolower(lang("hotcms_catgory")).lang("hotcms_updated_item"));
      redirect($this->module_url."/categories");
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    // display edit form
    $data['form']['name_input'] = $this->_create_text_input('name', $retailer_category->name, 50, 20, 'text');
 
    $data['currentItem'] = $retailer_category;
    $data['index_page_num'] = $this->session->userdata('categories_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_category_edit', NULL);
  }

  /**
   * creates new category
   */
  public function category_create()
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = lang("hotcms_create")." ".strtotlower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_category"));
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " ".strtotlower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_category"));

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');

    if ($this->form_validation->run()) {
      $retailer_id = $this->retailer_model->insert_category($this->input->post());
      if ($retailer_id > 0) {
        $this->add_message('confirm',  lang("hotcms_organization")." ".strtolower(lang("hotcms_category")).lang("hotcms_created_item"));
        //redirect('retailer/category_edit/' . $retailer_id);
        redirect($this->module_url."/categories/");
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    //$pagination_config = pagination_configuration();
    //$data['retailers'] = $this->retailer_model->retailer_list(FALSE, TRUE, 1, $pagination_config['per_page']);
    $data['index_page_num'] = $this->session->userdata('categories_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_category_create', NULL);
  }
  
  /**
   * delete a retailer's category
   * @param  int  id of the item to be deleted
   */
  public function category_delete($id)
  {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $category = $this->retailer_model->retailer_category_load($id);
    $result = $this->retailer_model->retailer_category_delete($id);
    if (is_array($result)) {
      $organizations = array();
      foreach ($result as $organization) {
        $organizations[] = $organization->name;
      }
      $this->add_message('error', lang("hotcms_organizations")." ".implode(", ", $organizations)." are still using ".strtolower(lang("hotcms_category"))." ". $category->name . ".");
    } elseif ($result === TRUE) {
      $this->add_message('confirm', lang("hotcms_organization")." ".strtolower(lang("hotcms_category"))." " . $category->name . lang("hotcms_deleted_item"));
    } else {
      $this->add_message('error', "Failed to ".strtolower(lang("hotcms_delete"))." ".strtolower(lang("hotcms_organization"))." ".strtolower(lang("hotcms_category"))." ". $category->name . ".");
    }
    redirect($this->module_url."/categories/" . $this->session->userdata('categories_index_page_num'));
  }  
  
  /**
   * List all organization's types.
   * @param int page number
   */
   function types($page_num = 1) {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text ."'s ".lang("hotcms_type");    
    $data['module_header'] = $this->module_header;
    $data['java_script'] = 'modules/retailer/js/retailer.js';

    $data['organization_types'] = $this->retailer_model->get_all_types();
    $this->session->set_userdata('types_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_types', NULL);
  }

  /**
   * Create new organization type
   */
  function type_create() {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = lang("hotcms_create")." ".strtolower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_type"));
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " ".strtolower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_type"));

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');

    if ($this->form_validation->run()) {
      $type_id = $this->retailer_model->insert_type(elements(array("name"), $this->input->post()));
      if ($type_id > 0) {
        $this->add_message('confirm',  lang("hotcms_organization")." ".strtolower(lang("hotcms_type")).lang("hotcms_created_item"));
        redirect($this->module_url."/types/");
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['index_page_num'] = $this->session->userdata('types_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_type_create', NULL);
  }
  
  /**
   * Edit organization category.
   * @param int $id row id of database table
   */
  function type_edit($id) {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = lang("hotcms_edit")." ".strtolower(lang("hotcms_organization"))."'s ".strtolower(lang("hotcms_type"));

    /*$data['java_script'] = 'modules/' . $this->module_url . '/js/retailer_edit.js';*/

    $data['type_id'] = $id;
    $organization_type = $this->retailer_model->organization_type_load($id);

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');

    if ($this->form_validation->run()) { 
      $this->retailer_model->organization_type_update($id, elements(array("name"), $this->input->post()));
      $this->add_message('confirm', lang("hotcms_organization")." ".  strtolower(lang("hotcms_type")).lang("hotcms_updated_item"));
      redirect($this->module_url."/types");
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    // display edit form
    $data['form']['name_input'] = $this->_create_text_input('name', $organization_type->name, 50, 20, 'text');
 
    $data['currentItem'] = $organization_type;
    $data['index_page_num'] = $this->session->userdata('types_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/organization_type_edit', NULL);
  }
  
  /**
   * Delete a organization type by row id
   * @param int $id row id in database table
   */
  function type_delete($id) {
    if (!has_permission('manage_retailer')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $type = $this->retailer_model->organization_type_load($id);
    $result = $this->retailer_model->organization_type_delete($id);
    if (is_array($result)) {
      $organizations = array();
      foreach ($result as $organization) {
        $organizations[] = $organization->name;
      }
      $this->add_message('error', lang("hotcms_organizations")." ".implode(", ", $organizations)." are still using ".strtolower(lang("hotcms_type"))." ". $type->name . ".");
    } elseif ($result === TRUE) {
      $this->add_message('confirm', lang("hotcms_organization")." ".strtolower(lang("hotcms_type"))." " . $type->name . lang("hotcms_deleted_item"));
    } else {
      $this->add_message('error', "Failed to ".strtolower(lang("hotcms_delete"))." ".strtolower(lang("hotcms_organization"))." ".strtolower(lang("hotcms_type"))." ". $type->name . ".");
    }
    redirect($this->module_url."/types/" . $this->session->userdata('types_index_page_num'));
  }
  
  /**
   * list all retailers with targeting
   * @param  int  page number
  public function targeting($page_num = 1)
  {
    if (!has_permission('manage_retailer_targeting')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text;    
    $data['module_header'] = $this->module_header;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/retailer.js';

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
        'country' => $this->input->post('country_code'),
      );
      $this->session->set_userdata('retailer_access_filters', $filters);
      redirect('retailer/access');
    }
    $filters = $this->session->userdata('retailer_access_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'country' => '',
      );
    }
    $filters['status'] = 1;
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

    }
    if ($separator == false)
      $active_filters.= 'None';

    $data['active_filters'] = $active_filters;
    

    $data['form']['country_code_options'] = array('' => 'All') + list_country_array();
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction']);

    $retailer_targeting = array (
      'allow' => 'Allow'         
    );
    
    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/access/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->retailer_model->retailer_count($filters);
    $retailers = $this->retailer_model->retailer_list($filters, TRUE, $page_num, $pagination_config['per_page']);

    foreach( $retailers as $retailer){
        $count_stores = $this->retailer_model->count_stores($retailer->id);     
        $retailer->stores = $count_stores->count;
        $count_users = $this->retailer_model->count_users($retailer->id);     
        $retailer->users = $count_users->count; 
        //set checkboxes for retailer permissions
        $retailer_active_targets = array();
        foreach ($this->retailer_model->get_retailer_targets_by_site_id($retailer->id) as $p) {
            $retailer_active_permissions[$p->permission_key] = $p->permission_key;
        }
        //$retailer->per = $retailer_active_permissions;
        
        foreach ($retailer_permission as $k => $v) {
           $retailer->percheck = array();
            //var_dump($retailer_active_permissions);
            if (!empty($retailer_active_permissions)) {
                $checked = in_array($k, $retailer_active_permissions);
            } else {
                $checked = false;
            }
            //$retailer->percheck[$k] = $checked;
            $permission_checkbox[$k] = $this->_create_checkbox_input('permissions[]', $v, $k, $checked, $retailer->id,'ret_per');
        }    
        
        $retailer->per_chec = $permission_checkbox;
        
    }
    
    $data['retailers'] = $retailers;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();

    // build the form
    $data['form']['access_options'] = $this->retailer_model->list_permissions();

    $this->load_messages();
    self::loadBackendView($data, 'retailer/retailer_leftbar', NULL, 'retailer/retailer_targeting', NULL);
  }  
   */
}

?>
