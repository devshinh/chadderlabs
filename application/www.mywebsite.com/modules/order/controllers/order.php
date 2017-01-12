<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Order Controller
 *
 * @package		HotCMS
 * @author		Jeffrey Tang
 * @copyright	Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */
class Order extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }

    $this->load->config('order/order', TRUE);
    $this->load->model('order/order_model');
    $this->load->library('pagination');

    $this->module_url = $this->config->item('module_url', 'order');
    $this->module_header = $this->lang->line('hotcms_order');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_order'));
  }

  /**
   * list all items
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/order.js';

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
      $this->session->set_userdata('order_filters', $filters);
      redirect('order');
    }
    $filters = $this->session->userdata('order_filters');
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
    $pagination_config['total_rows'] = $this->order_model->order_count($filters);
    $orders = $this->order_model->order_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    
    /*
    foreach( $orders as $order){
       $count_stores = $this->order_model->count_stores($order->id);     
       $order->stores = $count_stores->count;
       $count_users = $this->order_model->count_users($order->id);     
       $order->users = $count_users->count;       
      }     
     */
    //die(var_dump)
    $data['orders'] = $orders;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('order_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/order', NULL);
  }

  /**
   * creates new order
   */
  public function create()
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create order";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " order";

    //TODO: validate unique order name
    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');

    $data['selected_country'] = $this->input->post('country_code') > '' ? $this->input->post('country_code') : '';

    if ($this->form_validation->run()) {
      $order_id = $this->order_model->order_insert($this->input->post());
      if ($order_id > 0) {
        $this->add_message('confirm', 'Order was created.');
        redirect('order/edit/' . $order_id);
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['country_code_options'] = array('' => 'Select') + list_country_array();
    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
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

    //$pagination_config = pagination_configuration();
    //$data['orders'] = $this->order_model->order_list(FALSE, TRUE, 1, $pagination_config['per_page']);
    $data['index_page_num'] = $this->session->userdata('order_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/order_create', NULL);
  }

  /**
   * edit order
   * @param  int  $id
   * @param  int  page number
   */
  public function edit($id, $page_num = 1)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit order";

    //$this->load->model('user/user_model');

    $data['order_id'] = $id;
    $order = $this->order_model->order_load($id, FALSE);
    
    $orderItems = $this->order_model->order_load_items($id, FALSE);
    $order->items = $orderItems;
    $data['currentItem'] = $order;

    $this->form_validation->set_rules('shipping_firstname', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('shipping_lastname', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('shipping_street1', strtolower(lang('hotcms_street1')), 'trim|required');
    $this->form_validation->set_rules('shipping_city', strtolower(lang('hotcms_city')), 'trim|required');
    $this->form_validation->set_rules('shipping_province', strtolower(lang('hotcms_province')), 'trim|required');
    $this->form_validation->set_rules('shipping_postal', strtolower(lang('hotcms_postal_code')), 'trim|required');

    

    //$data['selected_country'] = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;

    if ($this->form_validation->run()) {
      // update order
      $this->order_model->order_update($id, $this->input->post());
      // reload
        $order = $this->order_model->order_load($id, FALSE);

        $orderItems = $this->order_model->order_load_items($id, FALSE);
        $order->items = $orderItems;
        $data['currentItem'] = $order;
      $this->add_message('confirm', 'Order was updated.');
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    // display edit form
    $data['form']['shipping_firstname'] = $this->_create_text_input('shipping_firstname', $order->shipping_firstname, 50, 20, 'text');
    $data['form']['shipping_lastname'] = $this->_create_text_input('shipping_lastname', $order->shipping_lastname, 50, 20, 'text');
    $data['form']['shipping_street1'] = $this->_create_text_input('shipping_street1', $order->shipping_street1, 50, 20, 'text');
    $data['form']['shipping_street2'] = $this->_create_text_input('shipping_street2', $order->shipping_street2, 50, 20, 'text');
    $data['form']['shipping_city'] = $this->_create_text_input('shipping_city', $order->shipping_city, 50, 20, 'text');
    $data['form']['shipping_province'] = $this->_create_text_input('shipping_province', $order->shipping_province, 50, 20, 'text');
    $data['form']['shipping_postal'] = $this->_create_text_input('shipping_postal', $order->shipping_postal, 50, 20, 'text');
    $data['form']['shipping_phone'] = $this->_create_text_input('shipping_phone', $order->shipping_phone, 50, 20, 'text');
    $data['form']['shipping_email'] = $this->_create_text_input('shipping_email', $order->shipping_email, 50, 20, 'text');
    $data['form']['shipping_instruction'] = $this->_create_text_input('shipping_instruction', $order->shipping_instruction, 50, 20, 'text');
    
    $data['form']['order_status_options'] = array('pending' => 'Pending', 'canceled' => 'Canceled', 'processing' => 'Processing','completed' => 'Completed');

   // $data['form']['name_input'] = $this->_create_text_input('name', $data['currentItem']->name, 50, 20, 'text');
/*
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
*/
    //$pagination_config = pagination_configuration();
    //$data['orders'] = $this->order_model->order_list(FALSE, TRUE, 1, $pagination_config['per_page']);
    $data['index_page_num'] = $this->session->userdata('order_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/order_edit', NULL);
  }

  /**
   * delete a order
   * @param  int  id of the item to be deleted
   */
  public function delete($id)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $order = $this->order_model->order_load($id, FALSE);
    $result = $this->order_model->order_delete($id);
    if ($result) {
      $this->add_message('confirm', 'Order ' . $order->name . ' was deleted.');
    }
    else {
      $this->add_message('error', 'Failed to delete order ' . $order->name . '.');
    }
    redirect('order/index/' . $this->session->userdata('order_index_page_num'));
  }

  /**
   * list all stores
   * @param  int  order ID
   * @param  int  page number
   */
  public function store($order_id, $page_num = 1)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/' . $this->module_url . '/js/order.js';

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
      $this->session->set_userdata('order_stroe_filters', $filters);
      redirect('order/store/' . $order_id);
    }
    $filters = $this->session->userdata('order_stroe_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'status' => '',
      );
    }
    $filters['order_id'] = $order_id;
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

    $data['order'] = $this->order_model->order_load($order_id, FALSE);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/store/' . $order_id;
    $pagination_config['uri_segment'] = 5;
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->order_model->store_count($filters, FALSE);
    $data['stores'] = $this->order_model->store_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('order_store_page_num', $page_num);
    $data['index_page_num'] = $this->session->userdata('order_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/store', NULL);
  }

  /**
   * creates new store
   * @param  int  order ID
   */
  public function store_create($order_id)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create store";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/' . $this->module_url . '/js/order.js';

    $data['order'] = $this->order_model->order_load($order_id, FALSE);

    $this->form_validation->set_rules('store_name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');

    $data['selected_province'] = $this->input->post('province') > '' ? $this->input->post('province') : '';
    $data['selected_country'] = $this->input->post('country_code') > '' ? $this->input->post('country_code') : '';

    if ($this->form_validation->run()) {
      $store_id = $this->order_model->store_insert($order_id, $this->input->post());
      if ($store_id > 0) {
        $this->add_message('confirm', 'Store was created.');
        redirect('order/store_edit/' . $order_id . '/' . $store_id);
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

    $data['index_page_num'] = $this->session->userdata('order_store_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/store_create', NULL);
  }

  /**
   * edit store
   * @param  int  order ID
   * @param  int  $id
   */
  public function store_edit($order_id, $id)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . ' ' . strtolower($this->lang->line('hotcms_store'));
    $data['java_script'] = 'modules/' . $this->module_url . '/js/order.js';

    $data['order'] = $this->order_model->order_load($order_id, FALSE);
    $data['store_id'] = $id;
    $data['currentItem'] = $this->order_model->store_load($id, FALSE);

    $this->form_validation->set_rules('store_name', strtolower(lang('hotcms_name')), 'trim|required');
    //$this->form_validation->set_rules('store_num', strtolower(lang('hotcms_store_num')), 'trim|required');
    $this->form_validation->set_rules('country_code', strtolower(lang('hotcms_country')), 'trim|required');

    $data['selected_province'] = $_POST ? $this->input->post('province') : $data['currentItem']->province;
    $data['selected_country'] = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;

    if ($this->form_validation->run()) {
      // update
      $this->order_model->store_update($id, $this->input->post());
      // reload
      $data['currentItem'] = $this->order_model->store_load($id, FALSE);
      $this->add_message('confirm', 'Store was updated.');
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    // display edit form
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

    $data['index_page_num'] = $this->session->userdata('order_store_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/store_edit', NULL);
  }

  /**
   * delete a store
   * @param  int  order ID
   * @param  int  id of the item to be deleted
   */
  public function store_delete($order_id, $id)
  {
    if (!has_permission('manage_order')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $store = $this->order_model->store_load($id, FALSE);
    $result = $this->order_model->store_delete($id);
    if ($result) {
      $this->add_message('confirm', 'Store ' . $store->store_name . ' was deleted.');
    }
    else {
      $this->add_message('error', 'Failed to delete store ' . $store->store_name . '.');
    }
    redirect('order/store/' . $order_id . '/' . $this->session->userdata('order_store_page_num'));
  }

  /**
   * list all orders and permissions
   * @param  int  page number
   */
  public function access($page_num = 1)
  {
    if (!has_permission('manage_order_permission')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/order.js';

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
      $this->session->set_userdata('order_access_filters', $filters);
      redirect('order/access');
    }
    $filters = $this->session->userdata('order_access_filters');
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

    $data['form']['country_code_options'] = array('' => 'All') + list_country_array();
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction']);

    $data['order_roles'] = $this->order_model->list_order_roles();

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/access/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->order_model->order_count($filters);
    $orders = $this->order_model->order_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    $data['orders'] = $orders;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();

    // build the form
    $data['form']['access_options'] = $this->order_model->list_permissions();

    $this->load_messages();
    self::loadBackendView($data, 'order/order_leftbar', NULL, 'order/order_permission', NULL);
  }

}

?>
