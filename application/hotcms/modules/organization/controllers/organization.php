<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Organization Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Organization extends HotCMS_Controller {

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_organization')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->model('organization_model');

    $this->load->config('organization', TRUE);
    $this->module_url = $this->config->item('module_url', 'organization');
    $this->module_header = $this->lang->line('hotcms_organizations');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_organization');

    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'organization');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'organization');
  }

  /**
   * list all organizations
   * @param  int  page number
   * @param  array message for showing message to user (message[type], message[value]
   *
   * @return backendview for organizations
   */
  public function index($page_num = 1, $message = '') {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;

    // paginate configuration
    $this->load->library('pagination');
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = 10;
    $pagination_config['total_rows'] = $this->organization_model->count_all_organizations();

    $right_data['items_array'] = $this->organization_model->get_all_organizations($page_num, $pagination_config['per_page']);

    //set message
    if (!empty($message)) {
      $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      $data['message'] = self::setMessage(false);
    }
    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();

    self::loadBackendView($data, 'organization/organization', NULL, 'organization/organization', $right_data);
  }

  /**
   * Set validation rules
   *
   */
  private function validate() {
    // assign validation rules
    $this->form_validation->set_rules('name', strtolower('lang:hotcms_name'), 'trim|required|unique_organization');
    $this->form_validation->set_rules('email', strtolower('lang:hotcms_email'), 'trim|required');
    $this->form_validation->set_rules('phone', strtolower('lang:hotcms_phone'), 'trim|required');
  }

  /**
   * Calling create function from model class.
   *
   * @param id of item
   */
  public function create() {

    $data = array();
    $data['module_header'] = $this->lang->line('hotcms_create') . ' ' . $this->lang->line('hotcms_organization');
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = $this->java_script;

    $this->validate();

    $message = array();
    if ($this->form_validation->run()) {

      $this->organization_model->insert();
      // assign values

      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_created_item');

      $this->index(1, $message);
    } else {

      $data['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
      $data['email_input'] = $this->_create_text_input('email', $this->input->post('email'), 50, 20, 'text');
      $data['phone_input'] = $this->_create_text_input('phone', $this->input->post('phone'), 50, 20, 'text');

      $data['active_input'] = $this->_create_checkbox_input('active', 'active', 'active', 'accept', false, 'margin:10px');

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);

      $right_data = '';

      self::loadBackendView($data, 'organization/organization', NULL, 'organization/organization_create', $right_data);
    }
  }

  public function edit($slug, $message = '') {

    $id = $this->organization_model->get_id_by_slug($slug)->id;

    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . ' ' . $this->lang->line('hotcms_organization');
    $data['add_new_text'] = $this->add_new_text;

    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;

    $this->validate();

    if ($this->form_validation->run()) {
      $this->organization_model->update($id);

      $right_data['current_item'] = $this->organization_model->get_organization_by_id($id);

      $right_data['form'] = self::set_edit_form($right_data['current_item']);


      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => $this->lang->line('hotcms_updated_item')));
      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      }
      $data['message'] = self::setMessage(false);

      self::loadBackendView($data, 'organization/organization', NULL, 'organization/organization_edit', $right_data);
      //$this->edit($slug,$data['message']);
    } else {
      $right_data['current_item'] = $this->organization_model->get_organization_by_id($id);


      $right_data['form'] = self::set_edit_form($right_data['current_item']);

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));

      //load locations
      $right_data['locations_table'] = self::get_locations($id);

      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      }
      $data['message'] = self::setMessage(false);

      //$this->edit($slug,$data['message']);
      self::loadBackendView($data, 'organization/organization', NULL, 'organization/organization_edit', $right_data);
    }
  }

  private function set_edit_form($current_item) {
    $data = array();
    $data['name_input'] = $this->_create_text_input('name', $current_item->name, 50, 20, 'text');
    $data['email_input'] = $this->_create_text_input('email', $current_item->email, 50, 20, 'text');
    $data['phone_input'] = $this->_create_text_input('phone', $current_item->phone, 50, 20, 'text');
    $data['active_input'] = $this->_create_checkbox_input('active', 'active', 'accept', $current_item->active == 1, '');

    return $data;
  }

  /**
   * Calling delete function from model class
   *
   * @param id of item
   */
  public function delete($id) {

    $this->load->model('organization_model');

    $this->organization_model->delete_by_id($id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->index(1, $message);
  }

  /**
   *  Delete asset for organization
   *
   * @param a_id auction id
   * @param organization_ id id of item
   */
  public function delete_asset($a_id, $organization_id) {

    $this->load->model('organization_model');

    $this->organization_model->delete_asset($a_id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_asset');

    $this->edit($organization_id, $message);
  }

  /**
   *  Add image for organization
   *
   * @param a_id auction id
   * @param organization_id id of item
   */
  public function add_image_asset($a_id, $organization_id) {

    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . ' ' . $this->lang->line('hotcms_organization');

    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;


    $this->load->model('organization_model');

    $this->organization_model->add_image_asset($a_id, $organization_id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_added_asset');

    $this->edit($organization_id, $message);
//  $this->edit($organization_id);
  }

  /* function for call model fuction to store sequence in database */

  public function ajax_assets_sequence() {

    // load array
    $sequence = explode('_', $_GET['asset']);
    //var_dump($sequence);
    // load model
    $this->load->model('organization_model');
    // loop sequence...
    $count = 0;
    foreach ($sequence as $id) {
      $this->organization_model->save_asset_sequence('menu', $id, ++$count);
    }
  }

  /* function for call model fuction to store sequence in database */

  public function ajax_sequence() {

    // load array
    $sequence = explode('_', $_GET['asset']);
    var_dump($sequence);
    // load model
    $this->load->model('organization_model');
    // loop sequence...
    $count = 0;
    foreach ($sequence as $id) {
      $this->organization_model->save_organization_sequence('menu', $id, ++$count);
    }
  }

  public function add_location($org_id) {

    $right_data['test'] = modules::run('location/controller/create');
    var_dump($right_data['test']);
    die();
    return $org_id;
  }

  public function get_locations($org_id) {


    $locations = $this->organization_model->get_locations($org_id);

    return $locations;
  }

}

?>
