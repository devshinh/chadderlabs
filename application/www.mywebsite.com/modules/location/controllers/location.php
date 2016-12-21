<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Organiztion Controller
 *
 *
 * @package		baconCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2012, HotTomali.
 * @since		Version 3.0
 */
class Location extends HotCMS_Controller {

  public function _remap($method, $args) {
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      //redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_location')) {
      //show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }


    $this->load->model('location_model');
    $this->load->config('location', TRUE);
    //$this->module_url = $this->module_url = $this->config->item('module_url', 'location');
    //$this->module_header = $this->lang->line('hotcms_locations');
    //$this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_location'));
    //$this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'location');
    //$this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'location');


    $args = array_slice($this->uri->rsegments, 2);

    if (method_exists($this, $method)) {
      return call_user_func_array(array(&$this, $method), $args);
    }
  }

  /**
   * list all items
   * @param  int  page number for paginator
   * @param  array message(confrim,error)
   */
  public function index($page_num = 1, $message = '') {

    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;

    $right_data = array();
    $right_data['current'] = $this->location_model->get_all_locations(TRUE);

    //set message
    if (!empty($message)) {
      $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      $data['message'] = self::setMessage(false);
    }

    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();
    self::loadBackendView($data, 'location/location_submenu', NULL, 'location/location', $right_data);
  }

  /**
   * Set validation rules for item
   *
   */
  public function validate_location() {
    // assign validation rules
    $this->form_validation->set_rules('name', strtolower($this->lang->line('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('main_email', 'main email', 'trim|required|valid_email');
  }

  /**
   * Calling create function from model class.
   *
   * @param id of item
   */
  public function create() {

    $this->load->model('operation_hours/operation_hours_model');
    $data = array();
    $data['module_header'] = 'Create ' . $this->lang->line('hotcms_location');
    $data['module_url'] = "location";
    $data['add_new_text'] = $this->add_new_text;

    $this->validate_location();

    if ($this->form_validation->run()) {

      $this->location_model->insert();
      $loc_id = $this->db->insert_id();
      $this->operation_hours_model->add_empty_hours_for_location($loc_id);

      $message = array();
      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_created_item');

      $this->index(1, $message);
    } else {
      $right_data = array();
      $right_data['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 100, 20, 'text');
      $right_data['website_input'] = $this->_create_text_input('website', $this->input->post('website'), 100, 20, 'text');
      $right_data['main_email_input'] = $this->_create_text_input('main_email', $this->input->post('main_email'), 100, 20, 'text');
      $right_data['main_phone_input'] = $this->_create_text_input('main_phone', $this->input->post('main_phone'), 100, 20, 'text');
      $right_data['toll_free_phone_input'] = $this->_create_text_input('toll_free_phone', $this->input->post('toll_free_phone'), 100, 20, 'text');
      $right_data['main_fax_input'] = $this->_create_text_input('main_fax', $this->input->post('main_fax'), 100, 20, 'text');
      $right_data['address_1_input'] = $this->_create_text_input('address_1', $this->input->post('address_1'), 100, 20, 'text');
      $right_data['address_2_input'] = $this->_create_text_input('address_2', $this->input->post('address_2'), 100, 20, 'text');
      $right_data['city_input'] = $this->_create_text_input('city', $this->input->post('city'), 100, 20, 'text');
      $right_data['province_input'] = $this->_create_text_input('province', $this->input->post('province'), 100, 20, 'text');
      $right_data['postal_code_input'] = $this->_create_text_input('postal_code', $this->input->post('postal_code'), 100, 20, 'text');
      $right_data['latitude_input'] = $this->_create_text_input('latitude', $this->input->post('latitude'), 100, 20, 'text');
      $right_data['longitude_input'] = $this->_create_text_input('longitude', $this->input->post('longitude'), 100, 20, 'text');


      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);

      self::loadBackendView($data, 'location/location_submenu', NULL, 'location/location_create', $right_data);
    }
  }

  /**
   * Edit item
   * @param  int  id of item
   * @param  array message(confrim,error)
   */
  public function edit($id, $message = '') {


    $this->load->model('user/user_model');
    $data = array();
    $data['module_header'] = "Edit Location";
    $data['module_url'] = "location";
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = $this->java_script . ' modules/' . $this->module_url . '/js/location_edit.js http://maps.google.com/maps?file=api&amp;v=3&amp;key=' . $this->config->item('google_maps_api_key', 'location');
    $data['css'] = 'modules/' . $this->module_url . '/css/location_edit.css';
    $data['location_id'] = $id;

    if ($this->input->post('hdnMode') == 'edit') {
      $this->validate_location();
    }

    if ($this->form_validation->run()) {

      $this->location_model->update($id);

      $current_item = $this->location_model->get_location_by_id($id);

      $data['aCurrentItem'] = $current_item;

      $right_data = array();
      //load all user by role
      $right_data['user_select'] = modules::run('user/controller/get_users_location_select', 6, $id);
      //load all connected users
      $users = $this->location_model->get_users_for_location($id);
      if (empty($users)) {
        $right_data['users'] = 'No user associated with this location.';
      } else {
        $right_data['users'] = '<a onClick="return confirmDelete()" href="/hotcms/location/delete_users/' . $id . '">Delete all users</a>';
      }
      //hours of operation
      $right_data['form_hours'] = modules::run('operation_hours/controllers/get_edit_form', 'location', $id);

      $right_data['form'] = self::set_edit_form($current_item);

      //set message
      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
        $data['message'] = self::setMessage(false);
      } else {
        $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
        $data['message'] = self::setMessage(false);
      }
      self::loadBackendView($data, 'location/location_submenu', NULL, 'location/location_edit', $right_data);
    } else {
      $current_item = $this->location_model->get_location_by_id($id);
      $data['aCurrentItem'] = $current_item;

      $right_data['form'] = self::set_edit_form($current_item);

      //set message
      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
        $data['message'] = self::setMessage(false);
      } else {
        $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
        $data['message'] = self::setMessage(false);
      }

      //load all user by role
      $right_data['user_select'] = modules::run('user/controller/get_users_location_select', 6, $id);
      //$right_data['user_checkbox'] = modules::run('user/controller/get_users_checkboxes', 6, $id);
      //load all connected users
      $users = $this->location_model->get_users_for_location($id);
      if (empty($users)) {
        $right_data['users'] = 'No user associated with this location.';
      } else {
        // $right_data['users'] = '<a onClick="return confirmDelete()" href="/hotcms/location/delete_users/' . $id . '">Delete all users</a>';
        $right_data['users'] = $users;
      }
      //hours of operation
      $right_data['form_hours'] = modules::run('operation_hours/controllers/get_edit_form', 'location', $id);

      self::loadBackendView($data, 'location/location_submenu', NULL, 'location/location_edit', $right_data);
    }
  }

  /**
   * Edit user for location (add/remove)
   * @param  int  location id
   */
  public function edit_users($location_id) {
    $this->location_model->delete_all_users_for_location($location_id);
    $users = $this->input->post('users');
    if (!empty($users)) {
      foreach ($users as $usr_id) {
        $this->location_model->add_user($location_id, $usr_id);
      }
      $message = array();
      $message['type'] = 'confirm';
      if (sizeof($users) == 1) {
        $message['value'] = $this->lang->line('hotcms_user_added');
      } else {
        $message['value'] = $this->lang->line('hotcms_users_added');
      }
    } else {
      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_users_removed');
    }
    $this->edit($location_id, $message);
  }

  /**
   * Delete all users for location
   * @param  int  location id
   */
  public function delete_users($location_id) {
    $this->location_model->delete_all_users_for_location($location_id);
    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->edit($location_id, $message);
  }

  /**
   * Delete all users for location
   * @param  int  location id
   */
  public function delete_user($user_id, $location_id) {
    $this->location_model->delete_user_for_location($user_id, $location_id);
    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->edit($location_id, $message);
  }

  private function set_edit_form($currentItem) {
    $data = array();
    $data['name_input'] = $this->_create_text_input('name', $currentItem->name, 100, 20, 'text');
    $data['website_input'] = $this->_create_text_input('website', $currentItem->website, 50, 20, 'text');
    $data['main_email_input'] = $this->_create_text_input('main_email', $currentItem->main_email, 50, 20, 'text');
    $data['main_phone_input'] = $this->_create_text_input('main_phone', $currentItem->main_phone, 30, 20, 'text');
    $data['toll_free_phone_input'] = $this->_create_text_input('toll_free_phone', $currentItem->toll_free_phone, 30, 20, 'text');
    $data['main_fax_input'] = $this->_create_text_input('main_fax', $currentItem->main_fax, 30, 20, 'text');
    $data['address_1_input'] = $this->_create_text_input('address_1', $currentItem->address_1, 100, 20, 'text');
    $data['address_2_input'] = $this->_create_text_input('address_2', $currentItem->address_2, 100, 20, 'text');
    $data['city_input'] = $this->_create_text_input('city', $currentItem->city, 100, 20, 'text');
    $data['province_input'] = $this->_create_text_input('province', $currentItem->province, 100, 20, 'text');
    $data['postal_code_input'] = $this->_create_text_input('postal_code', $currentItem->postal_code, 100, 20, 'text');

    $data['page_location_title'] = $this->_create_text_input('page_location_title', $currentItem->page_location_title, 100, 20, 'text');

    $data['page_location_description'] = array(
        'name' => 'page_location_description',
        'id' => 'page_location_description',
        'value' => set_value('postal_code', $current_item->page_location_description),
        'rows' => '10',
        'cols' => '30',
        'class' => 'textarea'
    );
    $data['page_location_services'] = array(
        'name' => 'page_location_services',
        'id' => 'page_location_services',
        'value' => set_value('postal_code', $current_item->page_location_services),
        'rows' => '10',
        'cols' => '30',
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

    $this->location_model->delete_by_id($id);

    $this->location_model->delete_all_users_for_location($id);
    $this->location_model->delete_all_hours_for_location($id);

    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->index(1, $message);
  }

  /**
   * Save latitude and longtitude from google map
   *
   * @param id of location
   */
  public function save_coordinates($loc_id) {
    $this->location_model->set_coordinates($loc_id, $this->input->post('lat'), $this->input->post('lng'));

    $message = array();
    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_updated_item');

    $this->edit($loc_id, $message);
  }

}

?>
