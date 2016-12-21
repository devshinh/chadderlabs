<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Contact Controller
 *
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */

class Contact extends HotCMS_Controller {
 
    public function _remap($method,$args){
   
    $this->load->model('model_contact');
   
    $this->load->config('contact', TRUE);
    
    $this->module_url = $this->config->item('module_url', 'contact');
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_contact' ));    
    $this->module_header = $this->lang->line( 'hotcms_contact' );
    //$this->add_new_text = $this->lang->line( 'hotcms_add_new' ) ." ".$this->lang->line( 'hotcms_contact' );

    $args = array_slice($this->uri->rsegments,2);
      
    
    if(method_exists($this,$method)){    
        return call_user_func_array(array(&$this,$method),$args);            
    }
    
  }
  
  public function index() {

    $aData['aCurrent'] = $this->model_contact->getAllContacts();
    
    $moduleView = $this->load->view('contact', $aData, true);
    
    self::loadView($moduleView);
  }
  
/**
* Set validation rules
*
*/

 public function validate($item_id) {
    $this->form_validation->set_rules( 'email_'.$item_id, strtolower(lang( 'hotcms_email_address' )), 'trim|valid_email|required' );
 }    
 
/**
* Calling create function from model class.
*
* @param $con_name = table name
* @param $con_id = item id 
*/

 public function create($con_name, $con_id) {
  $left_data = '';
  $aData['module_url'] = $this->module_url;
  $aData['module_header'] = $this->module_header;
  $aData['con_name'] = $con_name;
  $aData['con_id'] = $con_id;
  $right_data['add_new_text'] = $this->add_new_text;
  $this->validate();

  if ($this->form_validation->run()) {
   
   $this->model->insert();
    // assign values
    $right_data['aCurrent'] = $this->model->getAllContacts();

   //self::loadView('contact');
   self::loadBackendView($aData,'',$left_data,'contact/contact',$right_data);    
  } else {
   $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );   
   self::setMessage(false);
   //self::loadView('contact_create');
   self::loadBackendView($aData,'contact/contact_submenu',$left_data,'contact/contact_create',$right_data);
  }
 }
/* 
 public function edit($id) {
  self::initModule($this, 'contact');

  $this->validate();
  //$this->load->library('form_validation');
  
  $this->aData['aCurrentItem'] = $this->model->getContactById($id);

  if ($this->form_validation->run()) {
    $this->model->update();
    $this->aData['aCurrentItem'] = $this->model->getContactById($id);
    $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => 'Item updated.' ) );
    self::setMessage(false);
    self::loadView('contact_edit');
  } else {
    $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );   
    self::setMessage(false);
    self::loadView('contact_edit');
  }
 }
*/
  public function edit_contact($item_id, $con_id, $back_url) {
          
     $data['module_url'] = $this->module_url;
     $data['module_header'] = "Edit ". $this->module_header;
         
     $this->validate($item_id);
  
     if ($this->form_validation->run()) {
       $this->model_contact->update($item_id);

       $messages = 'Item saved.';
       $json = array('result' => 'updated', 'messages' => $messages);
       echo json_encode($json);
     }else {
       $json = array('result' => 'valid-error', 'messages' => strip_tags(validation_errors()));
       echo json_encode($json);
     }
 }
/**
* Calling delete function from model class
*
* @param id of item
*/
 public function delete($id) {


  $this->model_contact->deleteById($id);

  $this->index();
 } 

/**
* Add contact for organization - load in box window
*
* @param id of organization
*/
 public function addDefaultContactToUser($id, $name ='user'){

   $this->model_contact->insert($id, $name);
    // assign values    

 } 
 
/**
* Add contact for organization - load in box window
*
* @param id of organization
*/
 public function addContactToUser($id){

  $this->validate(); 


  if ($this->form_validation->run()) {

   $this->model_location->insert();
    // assign values
  
    
    $aData['aCurrentItemLocations'] = $this->model_location->getLocationsByOrganizationId($id);
  
    $this->load->model('organization/model_organization');
    $aData['aCurrent'] = $this->model_organization->getAllOrganizations();
    $moduleView = $this->load->view('organization', $aData, true);
    self::loadView($moduleView);      
    

  } else {
   $aData['organizationID'] = $id;
   $this->load->view('location_create',$aData);
   
  }  
 }
 /* function returning view for all contact for item */
 
 public function get_edit_forms($con_table, $id){
    $this->load->model('model_contact');
     
    $data['module_back_url'] = $con_table;
    $data['module_url'] = $con_table;
    $data['connection_id'] = $id;
    
    $contacts = $this->model_contact->get_contact_by_connection($con_table, $id);
    foreach ($contacts as $contact){
       $data['form_contact'] = self::set_edit_form_contact($contact); 
       $contact_forms[$contact->name] = $this->load->view('contact_edit',$data ,true);
    }
   if(!empty($contacts)){
    return $contact_forms;
   }else{
    return false;
   }
 }
 
  /* function returning ONE contact for item */
 
 
  private function set_edit_form_contact($current_item)
 {
    $data['contact_name'] = $current_item->name;
    $data['contact_id'] = $current_item->id;
    $id = $current_item->id;
    
    $data['email'] = $this->_create_text_input('email_'.$id, $current_item->email ,100,20,'text');
    $data['twitter'] = $this->_create_text_input('twitter_'.$id, $current_item->twitter ,100,20,'text');
    $data['website'] = $this->_create_text_input('website_'.$id, $current_item->website ,100,20,'text');
    $data['address_1'] = $this->_create_text_input('address_1_'.$id, $current_item->address_1 ,100,20,'text');
    $data['address_2'] = $this->_create_text_input('address_2_'.$id, $current_item->address_2 ,100,20,'text');
    $data['city'] = $this->_create_text_input('city_'.$id, $current_item->city ,100,20,'text');
    $data['province'] = $this->_create_text_input('province_'.$id, $current_item->province ,100,20,'text');
    $data['postal_code'] = $this->_create_text_input('postal_code_'.$id, $current_item->postal_code ,100,20,'text');
    $data['phone'] = $this->_create_text_input('phone_'.$id, $current_item->phone ,100,20,'text');
    $data['ext'] = $this->_create_text_input('ext_'.$id, $current_item->ext ,3,3,'');
    $data['cell'] = $this->_create_text_input('cell_'.$id, $current_item->cell ,100,20,'text');
    $data['fax'] = $this->_create_text_input('fax_'.$id, $current_item->fax ,100,20,'text');

    return $data;
  }
 
}
?>
