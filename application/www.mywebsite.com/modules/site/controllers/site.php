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
    $this->load->model('site_model');
    $this->load->config('site', TRUE);
    $this->module_url = $this->config->item('module_url', 'site');
    $this->module_header = $this->lang->line( 'hotcms_sites' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_site' ));

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
* Set validation rules
*
*/

 public function validate() {
    // assign validation rules
    $this->form_validation->set_rules( 'name', strtolower(lang( 'hotcms_name' )), 'trim|required' );
    $this->form_validation->set_rules( 'url', strtolower(lang( 'hotcms_url' )),  'trim|required' );
 }

 public function edit($id) {

   $aData['module_url'] = $this->module_url;
   $aData['add_new_text'] = $this->add_new_text;
   $aData['module_header'] = "Edit Site";

   $this->validate();
 
   $this->load_messages();

  $aData['currentItem'] = $this->site_model->get_site_by_id($id);

  if ($this->form_validation->run()) {
    $this->site_model->update($id);

    $aData['currentItem'] = $this->site_model->get_site_by_id($id);

    $aData['form'] = self::set_edit_form($aData['currentItem']);

    $this->add_message('confirm', 'Site was updated.');
        
    //$moduleView = $this->load->view('site_edit', $aData, true);
    //self::loadView($moduleView);
    self::loadBackendView($aData, 'site/site_leftbar', NULL, 'site/site_edit', NULL);   
  } else {
    $currentItem = $this->site_model->get_site_by_id($id);

    $aData['form'] = self::set_edit_form($currentItem);

    $this->add_message('error', validation_errors());

    //$moduleView = $this->load->view('site_edit', $aData, true);
    //self::loadView($moduleView);
    self::loadBackendView($aData, 'site/site_leftbar', NULL, 'site/site_edit', NULL);   
  }
 }

 private function set_edit_form($currentItem)
 {

    $aData['name_input'] = $this->_create_text_input('name', $currentItem->name ,100,20,'text');
    $aData['url_input'] = $this->_create_text_input('url', $currentItem->domain ,100,20,'text');
    $aData['path_input'] = $this->_create_text_input('path', $currentItem->path ,100,20,'text');
    $aData['theme_input'] = $this->_create_text_input('theme', $currentItem->theme ,100,20,'text');

     $aData['primary_input']= array(
      'name'        => 'primary',
      'id'          => 'primary',
      'value'       => 'accept',
      'checked'     => $currentItem->primary==1,
      'style'       => 'margin:10px',
     );


     $aData['active_input'] = array(
         'name'        => 'active',
         'id'          => 'active',
         'value'       => 'accept',
         'checked'     => $currentItem->active==1,
         'style'       => 'margin:10px',
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
   * creates new site
   */
  public function create()
  {
    if (!has_permission('manage_sites')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create site";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " site";

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
//    $this->form_validation->set_rules('domain', strtolower(lang('hotcms_country')), 'trim|required');


    if ($this->form_validation->run()) {
      $site_id = $this->site_model->insert($this->input->post());
      if ($site_id > 0) {
        $this->add_message('confirm', 'Site was created.');
        //add default roles + default permissions
        $this->site_model->add_default_roles($site_id);
        $this->site_model->add_default_permissions($site_id);
        $this->site_model->add_default_modules($site_id);
        $this->site_model->add_default_module_widgets($site_id);
        $this->site_model->add_default_layouts($site_id);
        $this->site_model->add_default_menu_group($site_id);
        $this->site_model->add_default_asset_categories($site_id);
        
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

}
?>
