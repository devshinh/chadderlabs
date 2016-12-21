<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class HotCMS_Controller extends MX_Controller {

  protected $user_id;
  protected $environment;

  public function __construct()
  {
    parent::__construct();

    $this->load->library('permission');
    $this->lang->load('hotcms');
    
    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset');    

		$this->load->config('fireignition');
		if ($this->config->item('fireignition_enabled')) {
			if (floor(phpversion()) < 5) {
				log_message('error', 'PHP 5 is required to run fireignition');
			}
      else {
				$this->load->library('FirePHP');
			}
		}
		else {
			$this->load->library('Firephp_fake');
			$this->firephp =& $this->firephp_fake;
		}

    $this->user_id = (int)($this->session->userdata('user_id'));
    $this->environment = $this->config->item('environment');
  }

  // deprecated, use $this->ion_auth->logged_in() instead
  private function _check_if_user_logged_in()
  {
    if ($this->session->userdata( 'user_id' ) < 1) {
      $data['moduleView'] = $this->load->view('dashboard/dashboard',NULL,TRUE);
      $this->load->view('login',$data);
      return false;
    }
    return true;
  }

  public function loadView($moduleView, $data = array()) {
    //if($this->_check_if_user_logged_in()) {
      $data['moduleView'] = $moduleView;

      //ToDo - make it  better (JA)
      //if((uri_string()=='hotcms')or(uri_string()=='hotcms/dashboard')or(uri_string()=='hotcms/hotcms/dashboard')){
      //  $this->load->view('login', $data);
      //}else{
        $this->load->view('global', $data);
        //$this->output->enable_profiler( true );
      //}
    //}
  }

  public function loadBackendView($data, $left_view, $left_data, $right_view, $right_data = NULL)
  {
    /*
    //if($this->_check_if_user_logged_in()) {
      $data['left_view'] = $left_view;
      $data['left_data'] = $left_data;
      $data['right_view'] = $right_view;
      $data['right_data'] = $right_data;
      $this->load->view('hotcms', $data);
      */
      $admin_menu_options = array(
        'active_class' => 'active',
        'first_class' => 'first',
        'last_class' => 'last',
        'use_titles' => TRUE,
        'render_type' => 'collapsible',
        'container_tag_class' => 'top_menu',
      );
      $this->load->model('global_model');
      $this->load->library('menubuilder', $admin_menu_options);
      $this->load->vars($data);

      // load the current admin user's avatar picture
      $this->load->library('HotCMS_Model');
      $user = $this->hotcms_model->get_user_profile($this->user_id);
      $data['user'] = $user;
      if (!empty($user->avatar_id)) {
        $data['avatar_picture'] = $this->hotcms_model->get_user_avatar($user->avatar_id);
      }

      // load admin menu links
      $admin_menus = array();
      $modules = $this->global_model->list_modules();
      foreach ($modules as $mdl) {
        $mc = $mdl->module_code;
        if (!file_exists(APPPATH . 'modules/' . $mc . '/config/' . $mc . '.php')) {
          continue;
        }
        $this->load->config($mc . '/' . $mc, TRUE);
        $module_menu_array = $this->config->item('admin_menu', $mc);
        if (is_array($module_menu_array)) {   

          foreach ($module_menu_array as $path => $mm) {
            if ($mm['access'] > '' && has_permission($mm['access'])) {              
                 if(isset($mm['submenu']) && $mm['submenu'] == true ){
                     $path_array= explode('/', $path);
                     $admin_menus[$path] = array('label' => $mm['label'], 'parent_id' => $path_array[0]);
                 }else{
                     $admin_menus[$path] = $mm['label'];
                 }
            }
          }
        }
      }
      //echo '<pre>';
      //var_dump($admin_menus);
      //echo '</pre>';
      $active_path = $data['module_url'];
      $data['admin_menu'] = $this->menubuilder->render($admin_menus, $active_path, NULL, 'basic');

      $data['leftbar'] = $this->load->view($left_view, $left_data, TRUE);
      $data['main_area'] = $this->load->view($right_view, $right_data, TRUE);
      $sites = $this->permission->get_admin_sites($this->user_id);    
      foreach($sites as $site){
          if($site->site_image_id!=0){
                $site->site_image = asset_load_item($site->site_image_id);                
          }
      }
      //var_dump($sites);
      $data['aSite'] = $sites;
      $data['hidden_cur_url'] = $this->_create_hidden_input('back_url',uri_string());
      $this->load->view('global', $data);
    //}
  }

  // deprecated. use functions add_message() and load_messages() instead.
  protected function setMessage( $isAlert = true ) {
    $aMessage = '';
    // if message...
    if ($this->session->userdata( 'messageType' ) && $this->session->userdata( 'messageValue' )) {

      // assign message
      $aMessage = array( 'type'  => $this->session->userdata( 'messageType' ),
                         'value' => $this->session->userdata( 'messageValue' ) );

      // remove message
      $this->session->unset_userdata( array( 'messageType' => '', 'messageValue' => '' ) );
    }

    // if no current items...
    /*
    if (empty( $this->aData['aMessage'] ) && empty( $this->aData['aCurrent'] ) && $isAlert) {

      // assign values
      $aPlaceholder = array( '%sp', '%s' );
      $aValue       = array( strtolower( $this->oModule->sItemPlural ), strtolower( $this->oModule->sItemSingular ) );

      // assign alert message
      $this->aData['aMessage'] = array( 'type'  => 'alert',
                                        'value' => '<p>' . str_replace( $aPlaceholder, $aValue, $this->lang->line( 'hotcms__message__alert' ) ) . '</p>' );
    }
    */
    return $aMessage;
  }

  /**
   * Add messages to session
   * @param  str  message type
   * @param  array or string  message(s)
   */
  protected function add_message($type, $message = array())
  {
    $messages = $this->session->userdata('messages');
    if (!is_array($messages)) {
      $messages = array();
    }
		if (is_string($message)) {
      $messages[] = array('type' => $type, 'message' => $message);
		}
    else {
      foreach ($message as $msg) {
        $messages[] = array('type' => $type, 'message' => $msg);
      }
    }
    $this->session->set_userdata( 'messages', $messages );
  }

  /**
   * Load messages from session
   */
  protected function load_messages()
  {
    $messages = (array)$this->session->userdata('messages');
    if (count($messages)>0) {
      $data = array('messages' => $messages);
      $this->load->vars($data);
    }
    //remove messages from session
    $this->session->unset_userdata('messages');
    return $messages;
  }

  /* function for models to generate varible for CI function form_input - text input field */
  protected function _create_text_input($id, $default_value, $maxlength = 999, $size = 20, $css_class = ""){

      return array(
       'name'        => $id,
       'id'          => $id,
       'value'       => set_value( $id, $default_value ),
       'maxlength'   => $maxlength,
       'size'        => $size,
       'class'       => $css_class

     );

  }
 /* function for models to generate varible for CI function form_input - checkbox*/
  protected function _create_checkbox_input($name, $id, $default_value = 'accept', $checked = false, $style = "", $css_class = ""){

      return array(
       'name'        => $name,
       'id'          => $id,
       'value'       => $default_value,
       'checked'     => $checked,
       'class'       => $css_class,
       'style'       => $style
     );
  }

 /* function for models to generate varible for CI function form_input - hidden input*/
  protected function _create_hidden_input($name, $value){

      return array(
       $name         => $value,
     );

  }

  /*custom validators */
  public function _validator_date( $date ) {

    // if invalid format...
    if (!empty( $date ) && preg_match( '/^2[0-9]{3}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/', $date ) == 0) {

      // assign error message
      $this->form_validation->set_message( '_validator_date', $this->lang->line( 'hotcms__validator_date' ) );
      return false;

    } else { return true; }
  }

  public function _validator_password() {

    $isValid = true;

    // if embedded user name...
    if (stristr( $this->input->post( 'password' ), $this->input->post( 'txtUser' ) ) !== false ||
        stristr( $this->input->post( 'password_retype' ), $this->input->post( 'txtUser' ) ) !== false) {

      // assign error message
      $this->form_validation->set_message( '_validator_password', $this->lang->line( 'hotcms__validator_password_0' ) );
      $isValid = false;
    }

    // if password do not match...
    if (!empty( $isValid ) && $this->input->post( 'password' ) != $this->input->post( 'password_retype' )) {

      // assign error message
      $this->form_validation->set_message( '_validator_password', $this->lang->line( 'hotcms__validator_password_1' ) );
      $isValid = false;
    }

    return $isValid;
  }

}