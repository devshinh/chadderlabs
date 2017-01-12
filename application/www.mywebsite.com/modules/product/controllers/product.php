<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Product Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Product extends HotCMS_Controller {

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_auction')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->model('product_model');
    $this->load->model('product_category_model');
    $this->load->model('asset/model_asset');

    $this->load->config('product', TRUE);
    $this->module_url = $this->config->item('module_url', 'product');
    $this->module_header = $this->lang->line('hotcms_products');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_product');

    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'product');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'product');
  }

  /**
   * list all products
   * @param  int  page number
   * @param  array message for showing message to user (message[type], message[value]
   *
   * @return backendview for products
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
    $pagination_config['total_rows'] = $this->product_model->count_all_products();

    $right_data['items_array'] = $this->product_model->get_all_products($page_num, $pagination_config['per_page']);

    //set message
    if (!empty($message)) {
      $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      $data['message'] = self::setMessage(false);
    }
    // paginate
    $this->pagination->initialize($pagination_config);
    $right_data['pagination'] = $this->pagination->create_links();

    self::loadBackendView($data, 'product/product', NULL, 'product/product', $right_data);
  }

  /**
   * Set validation rules
   *
   */
  private function validate_create() {
    // assign validation rules
    $this->form_validation->set_rules('name', strtolower('lang:hotcms_name'), 'trim|required|unique_product');
    $this->form_validation->set_rules('description', strtolower('lang:hotcms_description'), 'trim|required');
    $this->form_validation->set_rules('minimum_bid', strtolower('lang:hotcms_minimum_bid'), 'numeric');
    $this->form_validation->set_rules('minimum_increment', strtolower('lang:hotcms_bid_increment'), 'numeric');
  }

  /**
   * Calling create function from model class.
   *
   * @param id of item
   */
  public function create() {

    $data['module_header'] = $this->lang->line('hotcms_create') . ' ' . $this->lang->line('hotcms_product');
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->add_new_text;

    $data['java_script'] = $this->java_script;

    $this->validate_create();

    if ($this->form_validation->run()) {

      $this->product_model->insert();
      // assign values
      //$data['items_array'] = $this->product_model->get_all_products();


      //$this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
      //$data['message'] = self::setMessage(false);

      $message['type'] = 'confirm';
      $message['value'] = $this->lang->line('hotcms_created_item');

      $this->sitemap->generateXML();
      //$view = $this->load->view('product', $data, true);
      //self::loadView($view);
      $this->index(1, $message);
    } else {

      $data['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
      $data['short_description_input'] = array(
          'name' => 'short_description',
          'id' => 'short_description',
          'value' => set_value('short_description', $this->input->post('short_description')),
          'rows' => '10',
          'cols' => '30',
          'class' => 'textarea'
      );
      $data['description_input'] = array(
          'name' => 'description',
          'id' => 'description',
          'value' => set_value('description', $this->input->post('description')),
          'rows' => '10',
          'cols' => '30',
          'class' => 'textarea'
      );
      $data['minimum_bid_input'] = $this->_create_text_input('minimum_bid', $this->input->post('minimum_bid'), 50, 20, 'text');
      $data['minimum_increment_input'] = $this->_create_text_input('minimum_increment', $this->input->post('minimum_increment'), 50, 20, 'text');
      $data['opening_time_input'] = $this->_create_text_input('opening_time', $this->input->post('opening_time'), 50, 20, 'text');
      $data['closing_time_input'] = $this->_create_text_input('closing_time', $this->input->post('closing_time'), 50, 20, 'text');
      $data['active_input'] = $this->_create_checkbox_input('active', 'active', 'active', 'accept', false, 'margin:10px');

      $categories = $this->product_category_model->get_all_product_categories();

      foreach ($categories as $category) {
        $cat[$category->id] = $category->name;
      }
      $data['categories'] = $cat;

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);

      $right_data = '';

      self::loadBackendView($data, 'product/product', NULL, 'product/product_create', $right_data);
    }
  }
   /**
   * Set validation rules for edit (send id of product for url check)
   *
   */
  private function validate_edit($id) {
    // assign validation rules
    $this->form_validation->set_rules('name', strtolower('lang:hotcms_name'), 'trim|required|unique_product[' . $id . ']');
    $this->form_validation->set_rules('description', strtolower('lang:hotcms_description'), 'trim|required');
    $this->form_validation->set_rules('minimum_bid', strtolower('lang:hotcms_minimum_bid'), 'numeric');
    $this->form_validation->set_rules('minimum_increment', strtolower('lang:hotcms_bid_increment'), 'numeric');
  }

  public function edit($id, $message = '') {

    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . ' ' . $this->lang->line('hotcms_product');
    $data['add_new_text'] = $this->add_new_text;

    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;

    $this->validate_edit($id);

    if ($this->form_validation->run()) {
      $this->product_model->update($id);

      $right_data['current_item'] = $this->product_model->get_product_by_id($id);

      $right_data['form'] = self::set_edit_form($right_data['current_item']);

      //$data['assets'] = $this->model_asset->get_all_images();
      $right_data['assets'] = $this->model_asset->get_all_images_by_catgegory_id(8);

      $right_data['product_assets'] = $this->product_model->get_product_assets($id);

      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => $this->lang->line('hotcms_updated_item')));
      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      }
      $data['message'] = self::setMessage(false);

      $this->sitemap->generateXML();
      //$view = $this->load->view('product_edit', $data, true);
      //self::loadView($view);
      self::loadBackendView($data, 'product/product', NULL, 'product/product_edit', $right_data);
    } else {
      $right_data['current_item'] = $this->product_model->get_product_by_id($id);

      //$data['assets'] = $this->model_asset->get_all_images();
      $right_data['assets'] = $this->model_asset->get_all_images_by_catgegory_id(8);

      $right_data['product_assets'] = $this->product_model->get_product_assets($id);

      $right_data['form'] = self::set_edit_form($right_data['current_item']);

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));

      if (!empty($message)) {
        $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
      }
      $data['message'] = self::setMessage(false);

      self::loadBackendView($data, 'product/product', NULL, 'product/product_edit', $right_data);
    }
  }

  private function set_edit_form($current_item) {
    $data = array();
    $data['name_input'] = $this->_create_text_input('name', $current_item->name, 50, 20, 'text');
    $data['short_description_input'] = array(
        'name' => 'short_description',
        'id' => 'short_description',
        'value' => set_value('postal_code', $current_item->short_description),
        'rows' => '10',
        'cols' => '30',
        'class' => 'textarea'
    );
    $data['description_input'] = array(
        'name' => 'description',
        'id' => 'description',
        'value' => set_value('postal_code', $current_item->description),
        'rows' => '10',
        'cols' => '30',
        'class' => 'textarea'
    );

    $categories = $this->product_category_model->get_all_product_categories();

    foreach ($categories as $category) {
      $cat[$category->id] = $category->name;
    }
    $data['categories'] = $cat;

    $data['minimum_bid_input'] = $this->_create_text_input('minimum_bid', $current_item->minimum_bid, 50, 20, 'text');
    $data['minimum_increment_input'] = $this->_create_text_input('minimum_increment', $current_item->minimum_increment, 50, 20, 'text');
    $data['opening_time_input'] = $this->_create_text_input('opening_time', $current_item->opening_time, 50, 20, 'text');
    $data['closing_time_input'] = $this->_create_text_input('closing_time', $current_item->closing_time, 50, 20, 'text');


    $data['active_input'] = $this->_create_checkbox_input('active', 'active', 'accept', $current_item->active == 1, 'margin:10px');

    return $data;
  }

  /**
   * Calling delete function from model class
   *
   * @param id of item
   */
  public function delete($id) {

    $this->load->model('product_model');

    $this->product_model->delete_by_id($id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_item');

    $this->index(1, $message);
  }

  /**
   *  Delete asset for product
   *
   * @param a_id auction id
   * @param product_ id id of item
   */
  public function delete_asset($a_id, $product_id) {

    $this->load->model('product_model');

    $this->product_model->delete_asset($a_id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_deleted_asset');

    $this->edit($product_id, $message);
  }

  /**
   *  Add image for product
   *
   * @param a_id auction id
   * @param product_id id of item
   */
  public function add_image_asset($a_id, $product_id) {

    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->lang->line('hotcms_edit') . ' ' . $this->lang->line('hotcms_product');

    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;


    $this->load->model('product_model');

    $this->product_model->add_image_asset($a_id, $product_id);

    $message['type'] = 'confirm';
    $message['value'] = $this->lang->line('hotcms_added_asset');

    $this->edit($product_id, $message);
//  $this->edit($product_id);
  }

  /* function for call model fuction to store sequence in database */

  public function ajax_assets_sequence() {

    // load array
    $sequence = explode('_', $_GET['asset']);
    //var_dump($sequence);
    // load model
    $this->load->model('product_model');
    // loop sequence...
    $count = 0;
    foreach ($sequence as $id) {
      $this->product_model->save_asset_sequence('menu', $id, ++$count);
    }
  }

  /* function for call model fuction to store sequence in database */

  public function ajax_sequence() {

    // load array
    $sequence = explode('_', $_GET['asset']);
    var_dump($sequence);
    // load model
    $this->load->model('product_model');
    // loop sequence...
    $count = 0;
    foreach ($sequence as $id) {
      $this->product_model->save_product_sequence('menu', $id, ++$count);
    }
  }

}

?>
