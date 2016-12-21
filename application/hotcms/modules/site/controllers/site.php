<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sites Controller
 *
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */

class Site extends HotCMS_Controller {

   public function _remap($method,$args){

    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('super_admin')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    
    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset');       
    
    $this->load->model('site_model');
    $this->load->config('site', TRUE);
    $this->module_url = $this->config->item('module_url', 'site');
    $this->module_header = $this->lang->line( 'hotcms_labs' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_lab' ));

    $args = array_slice($this->uri->rsegments,2);

    if (method_exists($this,$method)) {
      return call_user_func_array(array(&$this,$method),$args);
    }

  }

   public function index() {

   $aData['module_url'] = $this->module_url;
   $aData['module_header'] = $this->module_header;
   $aData['add_new_text'] = $this->add_new_text;


    $aData['sites']   = $this->site_model->get_all_sites();
    
    $this->load_messages();
    self::loadBackendView($aData, 'site/site_leftbar', NULL, 'site/site', NULL);    
  }

/**
* Set validation rules for general settings tab
*
*/

 public function validate_settings() {
    // assign validation rules
    $this->form_validation->set_rules( 'name', strtolower(lang( 'hotcms_name' )), 'trim|required' );
    $this->form_validation->set_rules( 'url', strtolower(lang( 'hotcms_url' )),  'trim|required' );
 }
/**
* Set validation rules for Point balance tab
*
*/

 public function validate_balance() { 
   $this->form_validation->set_rules("points", strtolower(lang( "hotcms_deposit" )." ".lang( 'hotcms_point' )), "trim|required_all_fields[points.method]|numeric");
   $this->form_validation->set_rules("method", strtolower(lang( "hotcms_deposit" )." ".lang( 'hotcms_method' )), "trim");
   $this->form_validation->set_rules("cost", strtolower(lang( "hotcms_deposit" )." ".lang( 'hotcms_cost' )), "trim|numeric");
 }
 public function edit($id) {

   $aData['module_url'] = $this->module_url;
   $aData['add_new_text'] = $this->add_new_text;
   $aData['module_header'] = "Edit Lab";
   
   $aData['java_script'] = 'modules/' . $this->module_url . '/js/site_edit.js';


   if($this->input->post("hdnMode") == 'edit_settings'){
     $this->validate_settings();
   }elseif($this->input->post("hdnMode") == 'edit_balance'){
     $this->validate_balance();
   }

   $site = $this->site_model->get_site_by_id($id);
  
    if($site->site_image_id != 0){  
      $siteItems['image'] = asset_load_item($site->site_image_id);
    }
    if (!empty($siteItems)){
      $site->items = $siteItems;
    }else{
      $site->items = '';  
    }    
    $aData['currentItem'] = $site;
    
  if ($this->form_validation->run()) {
         if($this->input->post("hdnMode") == 'edit_settings'){
           $this->site_model->update($id);
         }
         if($this->input->post("hdnMode") == 'edit_balance'){
            // Deposit points
            $new_deposit = $this->input->post("points");
            if ( !empty($new_deposit)) {
              if ($this->site_model->deposit_points($id) !== FALSE) {
                $this->add_message('confirm', "Points ".$new_deposit." are deposited.");
              } else {
                $this->add_message('error', "Database failed to deposit points.");
              }
            }
         }
   $site = $this->site_model->get_site_by_id($id);
    
    if($site->site_image_id != 0){  
      $siteItems['image'] = asset_load_item($site->site_image_id);
    }
    if (!empty($siteItems)){
      $site->items = $siteItems;
    }else{
      $site->items = '';  
    }    
    $aData['currentItem'] = $site;
    $aData['form'] = self::set_edit_form($site);
    
    $points_deposit_history = $this->site_model->get_points_deposit_history($id);
    $aData['deposit_history'] = $points_deposit_history;
     
    $this->add_message('confirm', 'Site was updated.');
        
    //$moduleView = $this->load->view('site_edit', $aData, true);
    //self::loadView($moduleView);
   $this->load_messages();
    self::loadBackendView($aData, 'site/site_leftbar', NULL, 'site/site_edit', NULL);   
  } else {
    $site = $this->site_model->get_site_by_id($id);
  
    if($site->site_image_id != 0){  
      $siteItems['image'] = asset_load_item($site->site_image_id);
    }
    if (!empty($siteItems)){
      $site->items = $siteItems;
    }else{
      $site->items = '';  
    }    
    $aData['siteItems'] = $site;    
    
    $points_deposit_history = $this->site_model->get_points_deposit_history($id);
    $aData['deposit_history'] = $points_deposit_history;
    
   $site_modules = $this->site_model->get_site_modules($id);
   $aData['site_modules'] = $site_modules;    
    
    //$currentItem = $this->site_model->get_site_by_id($id);

    $aData['form'] = self::set_edit_form($site);

    $this->add_message('error', validation_errors());

    //$moduleView = $this->load->view('site_edit', $aData, true);
    //self::loadView($moduleView);
    $this->load_messages();
    self::loadBackendView($aData, 'site/site_leftbar', NULL, 'site/site_edit', NULL);   
  }
 }

 private function set_edit_form($currentItem)
 {

    $aData['name_input'] = $this->_create_text_input('name', $currentItem->name ,100,20,'text');
    $aData['url_input'] = $this->_create_text_input('url', $currentItem->domain ,100,20,'text');
    $aData['path_input'] = $this->_create_text_input('path', $currentItem->path ,100,20,'text');
    $aData['theme_input'] = $this->_create_text_input('theme', $currentItem->theme ,100,20,'text');
    $aData["point_input"] = $this->_create_text_input("points", 0,100,20,"text");
    $aData["method_input"] = $this->_create_text_input("method", "",100,20,"text");
    $aData["cost_input"] = $this->_create_text_input("cost", "0.00",100,20,"text");

     $aData['primary_input']= array(
      'name'        => 'primary',
      'id'          => 'primary',
      'value'       => 'accept',
      'checked'     => $currentItem->primary==1,
      'style'       => '',
     );


     $aData['active_input'] = array(
         'name'        => 'active',
         'id'          => 'active',
         'value'       => 'accept',
         'checked'     => $currentItem->active==1,
         'style'       => '',
     );

     $aData['hidden_site_input'] = array(
         'name'        => 'hidden',
         'id'          => 'hidden',
         'value'       => 'accept',
         'checked'     => $currentItem->hidden==1,
         'style'       => '',
     );     
     return $aData;
 }

/**
* Calling delete function from model class
*
* @param id of item
*/
 public function delete($id) {

  //$this->load->model('user_model');

  //$this->user_model->deleteById($id);
    
    $result = $this->site_model->delete_by_id($id);
    
    if ($result) {
      $this->add_message('confirm', 'Site was deleted.');
      $this->index();
    }
    else {
      $this->add_message('error', 'Failed to delete site.');
      $this->index();
    }     

  
 }

 /**
* Calling delete function from model class
*
* @param id of item
*/
 public function delete_balance($id,$site_id) {

  //$this->load->model('user_model');

  //$this->user_model->deleteById($id);
    
    $result = $this->site_model->delete_balance_by_id($id);
    
    if ($result) {
      $this->add_message('confirm', 'Point balance record was deleted.');
      $this->edit($site_id);
    }
    else {
      $this->add_message('error', 'Failed to delete point balance record.');
      $this->edit($site_id);
    }     

  
 }

  /**
   * creates new site
   */
  public function create()
  {
    if (!has_permission('manage_sites')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create Lab";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " lab";

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
//    $this->form_validation->set_rules('domain', strtolower(lang('hotcms_country')), 'trim|required');


    if ($this->form_validation->run()) {
      $site_id = $this->site_model->insert($this->input->post());
      if ($site_id > 0) {
        $this->add_message('confirm', 'Site was created.');
        //add default roles + default permissions
        $default_role_ids = $this->site_model->add_default_roles($site_id);
        $this->site_model->add_default_permissions($site_id, $default_role_ids);
        $this->site_model->add_default_modules($site_id);
        $this->site_model->add_default_module_widgets($site_id);
        $this->site_model->add_default_layouts($site_id);
        $this->site_model->add_default_menu_group($site_id);
        $this->site_model->add_default_asset_categories($site_id);
        $this->site_model->add_default_quiz_type($site_id);
        $this->site_model->add_default_pages($site_id);
        
        //create file for sitemap
        //$ourFileName = sprintf("/sitemap_%s.xml", $site_id);
        //$ourFileHandle = fopen($ourFileName, 'w') or die("can't create file");
        //fclose($ourFileHandle);        
        //$this->load->helper('file');
        //$path = sprintf('/application/www.mywebsite.com/sitemap_%s.xml',$site_id);
       //var_dump(write_file($path, ''));
       //die();
           
       
        $this->edit($site_id);
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    
    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['form']['url_input'] = $this->_create_text_input('url',  $this->input->post('domain') ,100,20,'text');   
    
    $data['index_page_num'] = $this->session->userdata('retailer_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'site/site_leftbar', NULL, 'site/site_create', NULL);
  } 

  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $site_id = 0) {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $site_id > 0) {
     // $result = $this->badge_model->asset_update($field_id, $attr);
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
    $category_context = 'site_default';
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
    $content = $this->load->view('site_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }  
  
  /**
   * Updates a icon image
   */
  public function ajax_update_image($s_id, $asset_id) {
    $id = (int) $s_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->site_model->update_site_image($id,$asset_id);
        $messages = 'Site image added.';
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
 * Activate/deactivate site module permission using Ajax
 * The URL to get here is /ajax/site/access/retailer_id/access_code
 */

  public function site_module_activation_ajax($module_id)
  {
    $json = array(
      'result' => FALSE,  // mandatory for all JSON output
      'messages' => '',   // mandatory for all JSON output
    );
    
    $result = $this->site_model->site_module_activation((int)$module_id);
    
    $json['result'] = $result;
    $json['messages'] = $result;
    return json_encode($json);
  }
}
?>
