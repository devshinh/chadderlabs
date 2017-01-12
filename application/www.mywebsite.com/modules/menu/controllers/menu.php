<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Menu Controller
 *
 * @package		Hotcms
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Menu extends HotCMS_Controller {

  public function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_content')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->helper('menu_item');
    $this->load->model('model_menu_group');

    $this->load->config('menu', TRUE);
    $this->module_url = $this->config->item('module_url', 'menu');
    $this->module_header = $this->lang->line('hotcms_menu_group');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_menu_group'));
    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'menu');
    $this->css = '';
  }

  public function index() {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

    //$aData['aCurrent']   = $this->model_menu_group->get_all_groups();
    //self::setMessage( false );
    //self::loadView();
    //$roleView = $this->load->view('menu', $aData, true);
    //self::loadView($roleView);

    $data['group_list'] = $this->model_menu_group->get_all_groups();
    self::loadBackendView($data, 'menu/menu_leftbar', NULL, 'menu/menu', NULL);
  }

  /**
   * Set validation rules
   *
   */
  private function validate() {
    // assign validation rules
    $this->form_validation->set_rules('menu_name', strtolower(lang('hotcms_name')), 'trim|required');
    //$this->form_validation->set_rules( 'core_level', strtolower(lang( 'hotcms_core_level' )), 'alpha_numeric|required' );
  }

  /**
   * Calling create function from model class.
   */
  public function create() {
    $data['module_header'] = "Create menu group";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_menu_group');
    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;

    $this->validate();

    if ($this->form_validation->run()) {
      $new_id = $this->model_menu_group->insert();
      // assign values
      //$data['group_list'] = $this->model_menu_group->get_all_groups();
      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => $this->lang->line('hotcms_created_item')));
      $data['message'] = self::setMessage(false);

      if ($new_id > 0) {
        redirect('/menu/edit/' . $new_id);
        exit;
      }
      //$roleView = $this->load->view('menu', $aData, true);
      //self::loadView($roleView);
    } else {
      $right_data['menu_name_input'] = $this->_create_text_input('menu_name', $this->input->post('menu_name'), 50, 20, 'text');
      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $right_data['message'] = self::setMessage(false);
      //$roleView = $this->load->view('menu_group_create', $aData, true);
      //self::loadView($roleView);
    }
    $data['group_list'] = $this->model_menu_group->get_all_groups();
    self::loadBackendView($data, 'menu/menu_leftbar', NULL, 'menu/menu_group_create', $right_data);
  }

  public function edit($id) {
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit " . $this->lang->line('hotcms_menu_group');
    $data['group_id'] = $id;
    $data['java_script'] = $this->java_script;
    $data['css'] = $this->css;
    $data['add_new_text'] = $this->add_new_text;

    $this->load->model('model_menu_item');

    $this->validate();

    if ($this->form_validation->run()) {
      //get menu name from input form
      $menu_name = $this->input->post('menu_name');

      //parse tree view for menu
      $menu_data_string = $this->input->post('menu_data');
      $menu_data = json_decode($menu_data_string);
      $menu_items = Menu_item::parse_menu($menu_data->menu_items);

      //update menu_group with supplied name
      $this->model_menu_group->update_name($id, $menu_name);
      $this->model_menu_item->update_menu($id, $menu_items);

      //load current item
      $data['currentItem'] = $this->model_menu_group->get_group_by_id($id);

      //load menu items
      $menu_item_records = $this->model_menu_item->get_all_root_menu_items_by_menu_id($id);
      $menu_items = $this->build_menu_item_array($menu_item_records);

      //$data['currentMenuItems'] = $this->model_menu_item->get_all_menu_items_by_group_id($id);
      $data['currentMenuItems'] = $menu_items;

      $data['form'] = self::set_edit_form($data['currentItem']);

      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
      $data['message'] = self::setMessage(false);

      //$menu_group_view = $this->load->view('menu_group_edit', $data, true);
      //self::loadView($menu_group_view);
    } else {
      $data['currentItem'] = $this->model_menu_group->get_group_by_id($id);

      //load menu items
      $menu_item_records = $this->model_menu_item->get_all_root_menu_items_by_menu_id($id);
      $menu_items = $this->build_menu_item_array($menu_item_records);

      //$data['currentMenuItems'] = $this->model_menu_item->get_all_menu_items_by_group_id($id);
      $data['currentMenuItems'] = $menu_items;

      $data['form'] = self::set_edit_form($data['currentItem']);

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);
      //$menu_group_view = $this->load->view('menu_group_edit', $data, true);
      //self::loadView($menu_group_view);
    }
    $data['group_list'] = $this->model_menu_group->get_all_groups();
    self::loadBackendView($data, 'menu/menu_leftbar', NULL, 'menu/menu_group_edit', NULL);
  }

  private function build_menu_item_array($menu_item_records) {
    $menu_items = array();
    foreach ($menu_item_records as $menu_item_record) {
      $menu_item = Menu_item::instantiate_from_database($menu_item_record);
      $sub_menu_records = $this->model_menu_item->get_all_root_menu_items_by_menu_id($menu_item_record->menu_group_id, $menu_item_record->id);
      $menu_item->sub_menu = $this->build_menu_item_array($sub_menu_records);
      $menu_items[] = $menu_item;
    }
    return $menu_items;
  }

  private function set_edit_form($currentItem) {
    $aData['menu_name_input'] = $this->_create_text_input('menu_name', $currentItem->menu_name, 50, 20, 'text');

    return $aData;
  }

  /**
   * Calling delete function from model class
   *
   * @param id of item
   */
  public function delete($id) {
    $this->model_menu_group->delete_by_id($id);

    $aData['module_url'] = $this->module_url;
    $aData['module_header'] = $this->module_header;
    $aData['add_new_text'] = $this->add_new_text;

    $aData['aCurrent'] = $this->model_menu_group->get_all_groups();

    $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_deleted_item')));
    $aData['message'] = self::setMessage(false);

    //$moduleView = $this->load->view('menu', $aData, true);
    //self::loadView($moduleView);
    redirect('/menu');
    exit;
  }

  /* menu item functions */

  /**
   * Function calling delete function from menu item module
   *
   * @param id of location item
   */
  public function delete_menu_item($id, $group_id) {

    $aData['module_header'] = "Edit " . $this->lang->line('hotcms_menu_group');
    $aData['module_url'] = "menu";

    $this->load->model('model_menu_item');
    if ($this->model_menu_item->has_children($id)) {
      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => 'Cannot delete menu unless menu has no children'));
    } else {
      $this->model_menu_item->delete_by_id($id);
      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
      redirect('/menu/edit/' . $group_id);
    }
    $this->edit_menu_item($id, $group_id);
    //redirect to main menu editor instead of loading different view
    /*
      $aData['currentItem'] = $this->model_menu_group->get_group_by_id($group_id);

      //load menu items
      $aData['currentMenuItems'] = $this->model_menu_item->get_all_menu_items_by_group_id($group_id);

      $aData['group_id'] = $group_id;


      $aData['form'] = self::set_edit_form($aData['currentItem']);

      $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => lang( 'hotcms_deleted_item' ) ) );
      $aData['message'] = self::setMessage(false);

      $moduleView = $this->load->view('menu_group_edit', $aData, true);
      self::loadView($moduleView);
     */
  }

  /**
   * Set validation rules
   */
  private function validate_menu_item() {
    // assign validation rules
    $this->form_validation->set_rules('title', strtolower($this->lang->line('hotcms_name')), 'trim|required');
  }

  public function add_menu_item($group_id) {
    $data = array();
    $data['module_header'] = "Add Menu Item";
    $data['module_url'] = "menu";
    $data['add_new_text'] = $this->add_new_text;

    $this->load->model('model_menu_item');

    $this->load->model('page/page_model');

    $data['menu_group'] = $this->model_menu_group->get_group_by_id($group_id);
    $data['group_id'] = $group_id;

    $this->validate_menu_item();

    if ($this->form_validation->run()) {
      //new way
      $menu_item_title = $this->input->post('title');
      $menu_item_page_id = $this->input->post('pages_array');
      $this->model_menu_item->create_menu_item($group_id, $menu_item_title, $menu_item_page_id);

      //old way
      //$this->model_menu_item->insert($group_id);

      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
      redirect('/menu/edit/' . $group_id);

      //the following has been commented out because the user should be redirected to the menu editor after adding a new menu item
      /*
        // assign values
        $aData['currentItem'] = $this->model_menu_group->get_group_by_id($group_id);
        //load menu items
        $aData['currentMenuItems'] = $this->model_menu_item->get_all_menu_items_by_group_id($group_id);

        $aData['form'] = self::set_edit_form($aData['currentItem']);

        $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
        $aData['message'] = self::setMessage(false);

        $moduleView = $this->load->view('menu_group_edit', $aData, true);
        self::loadView($moduleView);
       */
    } else {
      $data['item_title_input'] = $this->_create_text_input('title', $this->input->post('title'), 100, 20, 'text');

      $pages = $this->page_model->list_all_pages();
      foreach ($pages as $page) {
        $menu_item = $this->model_menu_item->get_menu_item_by_page_id($page->id);
        if (isset($menu_item->parent_id) && $menu_item->parent_id != 0) {
          $pages_array[$page->id] = ' --- ' . $page->name;
        } else {
          $pages_array[$page->id] = $page->name;
        }
      }

      $data['pages_array'] = $pages_array;

      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);

      //$moduleView = $this->load->view('menu_item_create', $aData, true);
      //self::loadView($moduleView);
      $data['group_list'] = $this->model_menu_group->get_all_groups();
      self::loadBackendView($data, 'menu/menu_leftbar', NULL, 'menu/menu_item_create', NULL);
    }
  }

  public function edit_menu_item($id, $group_id) {
    $data = array();
    $data['module_header'] = "Edit menu item";
    $data['module_url'] = "menu";
    $data['add_new_text'] = $this->add_new_text;

    $this->load->model('model_menu_item');

    $data['menu_group'] = $this->model_menu_group->get_group_by_id($group_id);
    $data['group_id'] = $group_id;

    $this->validate_menu_item();


    if ($this->form_validation->run()) {
      //new way
      $menu_item_title = $this->input->post('title');
      $menu_item_page_id = $this->input->post('pages_array');
      $menu_item_enabled = $this->input->post('menu_enabled') == '1';
      $this->model_menu_item->update_menu_item($id, $menu_item_title, $menu_item_page_id, $menu_item_enabled);
      //old way
      //$this->model_menu_item->update($id);
      // we want to go back to editting menu group item after saving menu item
      /*
        $aData['aCurrent'] = $this->model_menu_group->get_all_groups();
        $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
        $aData['message'] = self::setMessage(false);
        $moduleView = $this->load->view('menu', $aData, true);
        self::loadView($moduleView);
       */
      $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
      redirect('/menu/edit/' . $group_id);
    } else {
      $data['current_item'] = $this->model_menu_item->get_menu_item_by_id($id);
      $data['menu_group_item'] = $this->model_menu_group->get_group_by_id($group_id);
      $data['menu_deletable'] = !$this->model_menu_item->has_children($id);
      $data['form'] = self::set_edit_form_menu_item($data['current_item']);
      $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
      $data['message'] = self::setMessage(false);

      //$moduleView = $this->load->view('menu_item_edit', $data, true);
      //self::loadView($moduleView);
      $data['group_list'] = $this->model_menu_group->get_all_groups();
      self::loadBackendView($data, 'menu/menu_leftbar', NULL, 'menu/menu_item_edit', NULL);
    }
  }

  private function set_edit_form_menu_item($currentItem) {
    $this->load->model('page/page_model');
    $data = array();
    $data['title_input'] = $this->_create_text_input('title', $currentItem->title, 100, 20, 'text');

    $pages = $this->page_model->list_all_pages();
    foreach ($pages as $page) {
      $menu_item = $this->model_menu_item->get_menu_item_by_page_id_menu_id($page->id, $currentItem->menu_group_id);
      if (isset($menu_item->parent_id) && $menu_item->parent_id != 0) {
        $pages_array[$page->id] = ' --- ' . $page->name;
      } else {
        $pages_array[$page->id] = $page->name;
      }
    }

    $data['pages_array'] = $pages_array;
    $data['menu_enabled'] = TRUE;
    return $data;
  }

  /* function for call model fuction to store sequence in database */

  public function ajax_sequence() {
    // load array
    $sequence = explode('_', $_GET['menuItem']);
    //var_dump($sequence);
    // load model
    $this->load->model('model_menu_item');
    // loop sequence...
    $count = 0;
    foreach ($sequence as $id) {
      $this->model_menu_item->save_sequence('menu', $id, ++$count);
    }
  }

  public function save_menu() {
    var_dump($_POST);
  }

}

?>