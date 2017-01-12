<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Role Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Role extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('manage_role')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('role', TRUE);
    $this->load->model('role_model');

    $this->module_url = $this->config->item('module_url','role');
    $this->module_header = $this->lang->line( 'hotcms_role' );
    $this->add_new_text = $this->lang->line( 'hotcms_add_new' ).' '.strtolower($this->lang->line( 'hotcms_role' ));
  }

  public function index()
  {
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    
    $this->load->helper('site/site');
    $data['sites'] = get_sites_array();
    if ($this->input->post('site_select') > '') {
     $site_id = (int) ($this->input->post('site_select'));
    }else {
     $site_id = 1;
    }
    $data['site_id_for_roles'] = $site_id;    

    $data['roles'] = $this->role_model->get_all_roles(TRUE, $site_id);
    // only super admin can view and edit super admin role
    if (!has_permission('super_admin')) {
      foreach ($data['roles'] as $k => $v) {
        if ($v->system == 1) {
          unset($data['roles'][$k]);
          continue;
        }
      }
    }

    //$data['leftbar'] = $this->load->view('role/role_leftbar', $data, TRUE);
    //$data['main_area'] = $this->load->view('role/role', $data, TRUE);
    //$this->load->view('global', $data);
    $this->load_messages();
    self::loadBackendView($data, 'role/role_leftbar', NULL, 'role/role', $data);
  }

  /**
   * Set validation rules
   */
  private function validate()
  {
    // assign validation rules
    $this->form_validation->set_rules( 'name', strtolower(lang( 'hotcms_name' )), 'trim|required' );
    //$this->form_validation->set_rules( 'core_level', strtolower(lang( 'hotcms_core_level' )), 'alpha_numeric|required' );
  }

  /**
   * creates new role
   */
  public function create()
  {
    $data = array();
    $data['module_header'] = "Create role";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line( 'hotcms_add_new' ) . " role";

    $this->validate();
    //TODO: validate unique role name

    if ($this->form_validation->run()) {
      $role_id = $this->role_model->insert();
      $this->add_message('confirm', 'Role was created.');
      if ($role_id > 0) {
        redirect('role/edit/' . $role_id);
      }
      //$data['message'] = self::setMessage(false);

      //$roleView = $this->load->view('role', $aData, true);
      //self::loadView($roleView);
    }

    $data['name_input'] = $this->_create_text_input('name', $this->input->post( 'name' ),50,20,'text');
    $data['description_input'] = $this->_create_text_input('description', $this->input->post( 'description' ),250,20,'text');
    $data['active_input'] = $this->_create_checkbox_input('active','1', false , 'margin:10px');

    if (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    //$data['message'] = self::setMessage(false);

    $data['roles'] = $this->role_model->get_all_roles();
    //$roleView = $this->load->view('role_create', $data, true);
    //self::loadView($roleView);
    //$data['leftbar'] = $this->load->view('role/role_leftbar', $data, TRUE);
    //$data['main_area'] = $this->load->view('role/role_create', $data, TRUE);
    //$this->load->view('global', $data);
    $this->load_messages();
    self::loadBackendView($data, 'role/role_leftbar', NULL, 'role/role_create', NULL);
  }

  /**
   * edit role
   * @param  int  $id
   */
  public function edit($id, $site_id)
  {
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit role";

    $this->load->model('user/user_model');

    $data['role_id'] = $id;
    $data['role_site_id'] = $site_id;
    $data['currentItem'] = $this->role_model->get_role_by_id($id, $site_id);
    // only super admin can edit super admin role and users
    if (!has_permission('super_admin') && $data['currentItem']->system == 1) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    // check individual permission
    //if (!has_permission('manage_user_' . str_replace(' ', '_', $data['currentItem']->name)) {
    //  show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    //}
    $this->validate();
    
    if ($this->form_validation->run()) {
      $old_name = strtolower(trim($data['currentItem']->name));
      $new_name = strtolower(trim($this->input->post('name')));
      //update role
      $this->role_model->update($id);
      // if role name changed, update related permission key
      if ($old_name != $new_name) {
        $this->role_model->update_permission_key($old_name, $new_name);
      }
      //update role permissions
      $this->permission->update_permissions($id, $this->input->post('permissions'));

      $this->add_message('confirm', 'Role was updated.');
    }
    // display edit form
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form'] = self::_edit_form($data['currentItem']);
    //load role permission
    $active_role_permissions = array();
    foreach ($this->permission->list_role_permissions($id,$site_id) as $active_role_permission) {
      $active_role_permissions[$active_role_permission->id] = $active_role_permission->description;
    }
    $data['permissions'] = $this->permission->list_permissions('',$site_id);
    foreach ($data['permissions'] as $permission) {
      if (count($active_role_permissions)) {
        $checked = array_key_exists($permission->id, $active_role_permissions);
      }else{
        $checked = FALSE;
      }
      $data['form']['permissions'][$permission->id] = $this->_create_checkbox_input('permissions[' . $permission->id . ']', $permission->permission_key, 1, $checked , 'margin-right:10px');
    }
    //$data['message'] = self::setMessage(false);
    //$roleView = $this->load->view('role_edit', $data, true);
    //self::loadView($roleView);
    $data['roles'] = $this->role_model->get_all_roles();
    //$data['leftbar'] = $this->load->view('role/role_leftbar', $data, TRUE);
    $data['role_users'] = $this->user_model->lists_users_by_role($id);
    //$data['main_area'] = $this->load->view('role/role_edit', $data, TRUE);
    //$this->load->view('global', $data);
    $this->load_messages();
    self::loadBackendView($data, 'role/role_leftbar', NULL, 'role/role_edit', NULL);
  }

  private function _edit_form($currentItem)
  {
    $data['name_input'] = $this->_create_text_input('name', $currentItem->name, 50, 20,'text');
    $data['description_input'] = array(
      'name'        => 'description',
      'id'          => 'description',
      'value'       => set_value( 'description', $currentItem->description ),
      'cols'        => 60,
      'rows'        => 5,
      'class'       => 'text'
    );
    $data['active_input'] = $this->_create_checkbox_input('active','active','1', $currentItem->active==1, 'margin:10px');
    return $data;
  }

 /**
  * Calling delete function from model class
  * @param id of item
  */
  public function delete($id)
  {
    $role = $this->role_model->get_role_by_id($id);
    // do not allow deleting system roles
    if ($role->system > 0) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $result = $this->role_model->delete_by_id($id);
    if ($result) {
      $this->role_model->delete_permission($role->name);
      $this->add_message('confirm', 'Role was deleted.');
    }
    else {
      //TODO: output error message
    }
    redirect('role');
  }

/*role permissions functions*/

/**
* Function calling delete function from location module
*
* @param id of location item
*
 public function delete_permission($id, $role_id) {

  $aData['module_header'] = "Edit Role + Add permissions";
  $aData['module_url'] = "role";

  $this->load->model('role/model_role_permission');

  $this->model_role_permission->delete_by_id($id);

  $aData['currentItem'] = $this->role_model->get_role_by_id($role_id);

  $aData['currentItemPermissions'] = $this->model_role_permission->get_role_permissions_by_role_id($role_id);
  $aData['role_id'] = $role_id;


  $aData['form'] = self::_edit_form($aData['currentItem']);

  $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => lang( 'hotcms_deleted_item' ) ) );
  $aData['message'] = self::setMessage(false);

  $moduleView = $this->load->view('role_edit', $aData, true);
  self::loadView($moduleView);
 }

  /**
  * Set validation rules
  *
  *

   private function validate_permission() {
      // assign validation rules
    $this->form_validation->set_rules( 'permission', strtolower( $this->lang->line( 'hotcms_permission' ) ), 'trim|required' );
    $this->form_validation->set_rules( 'description_permission', strtolower( $this->lang->line( 'hotcms_description' ) ), 'trim|required' );

   }

  public function add_role_permission($role_id) {

   $aData['module_header'] = "Add permisson for role";
   $aData['module_url'] = "role";

   $this->load->model('role/model_role_permission');

   $aData['roleItem'] = $this->role_model->get_role_by_id($role_id);


   $this->validate_permission();

   if ($this->form_validation->run()) {

    $this->model_role_permission->insert($role_id);
     // assign values
     $aData['currentItem'] = $this->role_model->get_role_by_id($role_id);
     $aData['currentItemPermissions'] = $this->model_role_permission->get_role_permissions_by_role_id($role_id);
     $aData['role_id'] = $role_id;

     $aData['form'] = self::_edit_form($aData['currentItem']);

     $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
     $aData['message'] = self::setMessage(false);

     $moduleView = $this->load->view('role_edit', $aData, true);
     self::loadView($moduleView);

   } else {

     $aData['permission_input'] = $this->_create_text_input('permission', $this->input->post( 'permission' ),100,20,'text');
     $aData['description_permission_input'] = $this->_create_text_input('description_permission', $this->input->post( 'description_permission' ),100,20,'text');

     $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
     $aData['message'] = self::setMessage(false);

     $moduleView = $this->load->view('permission_create', $aData, true);
     self::loadView($moduleView);
   }

  }

  public function edit_permission($id, $role_id) {
   $aData['module_header'] = "Edit permission for role";
   $aData['module_url'] = "role";

   $this->load->model('role/model_role_permission');

   $aData['roleItem'] = $this->role_model->get_role_by_id($role_id);
   $aData['role_id'] = $role_id;

   $this->validate_permission();


   if ($this->form_validation->run()) {
    $this->model_role_permission->update($id);

    $aData['aCurrent'] = $this->role_model->get_all_roles();

    $this->session->set_userdata( array( 'messageType' => 'confirm', 'messageValue' => $this->lang->line( 'hotcms_created_item' ) ) );
    $aData['message'] = self::setMessage(false);

    $moduleView = $this->load->view('role', $aData, true);
    self::loadView($moduleView);

   }else {
    $aData['aCurrentItem'] =  $this->model_role_permission->get_role_permission_by_id($id);
    $aData['form'] = self::_edit_form_permission($aData['aCurrentItem']);
    $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );
    $aData['message'] = self::setMessage(false);

    $moduleView = $this->load->view('permission_edit', $aData, true);
    self::loadView($moduleView);

   }
  }

  private function _edit_form_permission($currentItem)
  {
     $aData['permission_input'] = $this->_create_text_input('permission', $currentItem->permission ,100,20,'text');
     $aData['description_permission_input'] = $this->_create_text_input('description_permission', $currentItem->description ,100,20,'text');

     return $aData;
  }
*/

}
?>
