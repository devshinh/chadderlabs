<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Module Controller
 *
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */

class Module extends HotCMS_Controller {

   public function _remap($method,$args){

    $this->load->model('model_module');

    $this->load->config('module', TRUE);
    $this->module_url = $this->config->item('module_url', 'module');
    $this->module_header = $this->lang->line( 'hotcms_modules' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ) ." ".$this->lang->line( 'hotcms_module' );

    $args = array_slice($this->uri->rsegments,2);

    if(method_exists($this,$method)){
        return call_user_func_array(array(&$this,$method),$args);
    }

  }


   public function index() {

   $data['module_url'] = $this->module_url;
   $data['module_header'] = $this->module_header;
   $data['add_new_text'] = $this->add_new_text;

   $data['aCurrent']   = $this->model_module->get_all_modules();
   $view = $this->load->view('module', $data, true);

    self::loadView($view);
  }

/**
* Set validation rules
*
*/

 private function validate() {
    // assign validation rules
    $this->form_validation->set_rules( 'name', strtolower(lang( 'hotcms_name' )), 'trim|required' );
    //$this->form_validation->set_rules( 'core_level', strtolower(lang( 'hotcms_core_level' )), 'alpha_numeric|required' );
 }

/**
* Calling create function from model class.
*
* @param id of item
*/

 public function create() {

  $data['module_header'] = "Create module";
  $data['module_url'] = $this->module_url;
  $data['add_new_text'] = $this->add_new_text;

  $this->validate();

  if ($this->form_validation->run()) {
   $attr = $this->input->post();
   $this->model_module->insert($attr);
    // assign values
    $data['aCurrent'] = $this->model_module->get_all_modules();

    $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
    $data['message'] = self::setMessage(false);

    $view = $this->load->view('module', $data, true);
    self::loadView($view);

  } else {

    $data['name_input'] = $this->_create_text_input('name', $this->input->post( 'name' ),50,20,'text');
    $data['version_input'] = $this->_create_text_input('version', $this->input->post( 'version' ),20,20,'text');
    $data['core_level_input'] = $this->_create_text_input('core_level', $this->input->post( 'core_level' ),4,20,'text');
    $data['is_embed_input'] = $this->_create_checkbox_input('is_embed','is_embed','is_embed', false , 'margin:10px');
    $data['active_input'] = $this->_create_checkbox_input('active','active','accept', false , 'margin:10px');

    $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
    $data['message'] = self::setMessage(false);

    $view = $this->load->view('module_create', $data, true);
    self::loadView($view);
  }
 }


 public function edit($id) {

   $data['module_url'] = $this->module_url;
   $data['module_header'] = "Edit module";

   $this->validate();

   if ($this->form_validation->run()) {
     $attr = $this->input->post();
     $this->model_module->update($id, $attr);

     $data['currentItem'] = $this->model_module->get_module_by_id($id);

     $data['form'] = self::set_edit_form($data['currentItem']);

     $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => 'Item was updated.' ) );
     $data['message'] = self::setMessage(false);

     $view = $this->load->view('module_edit', $data, true);
     self::loadView($view);
   } else {
     $data['currentItem'] = $this->model_module->get_module_by_id($id);

     $data['form'] = self::set_edit_form($data['currentItem']);

     $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
     $data['message'] = self::setMessage(false);
     $view = $this->load->view('module_edit', $data, true);
     self::loadView($view);
   }
 }

 private function set_edit_form($currentItem)
 {
    $data['name_input'] = $this->_create_text_input('name', $currentItem->name, 50, 20,'text');
    $data['version_input'] = $this->_create_text_input('version', $currentItem->version, 20, 20,'text');
    $data['core_level_input'] = $this->_create_text_input('core_level', $currentItem->core_level,4 ,20,'text');
    $data['is_embed_input'] = $this->_create_checkbox_input('is_embed','is_embed','is_embed', $currentItem->is_embed==1, 'margin:10px');
    $data['active_input'] = $this->_create_checkbox_input('active','active','accept', $currentItem->active==1, 'margin:10px');

     return $data;
 }

/**
* Calling delete function from model class
*
* @param id of item
*/
 public function delete($id) {

  $this->load->model('model_module');

  $this->model_module->delete_by_id($id);

  $this->index();
 }


}
?>
