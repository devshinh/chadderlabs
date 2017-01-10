<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends HotCMS_Controller {

  // module information
  protected $aModuleInfo;

  /**
   * Constructor method
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    $this->load->config('account/account', TRUE);
    $this->load->model('account/account_model');

    /**
     * prepare module information
     * can be overriden in each function
     */
    $this->module_url = $this->config->item('module_url', 'account');
    $this->aModuleInfo = array(
      'name'            => 'account',
      'title'           => $this->config->item('module_title', 'account'),
      'url'             => $this->config->item('module_url', 'account'),
      'meta_description' => $this->config->item('meta_description', 'account'),
      'meta_keyword'     => $this->config->item('meta_keyword', 'account'),
      'css'      => $this->config->item('css', 'account'),
      'js'      => 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'account')
    );
  }

  public function index()
  {
    if (!$this->ion_auth->logged_in()) {
      redirect($this->config->item('login_page'));
    }
    else {
      redirect($this->config->item('landing_page'));
    }
  }

  /**
   * user login
   */
  public function login()
  {
    if ($this->ion_auth->logged_in()) {
      redirect($this->config->item('landing_page'));
      exit;
    }
    $data = array();
    $data['sTitle'] = "User Login";
    $data['error'] = $this->session->flashdata('error');
    $data['message'] = $this->session->flashdata('message');

    $data['java_script'] = $this->aModuleInfo['js'];

    //validate form input
    $this->form_validation->set_rules('username', strtolower('lang:hotcms_name_user'), 'trim|required');
	  $this->form_validation->set_rules('password', strtolower('lang:hotcms_password'), 'required');

    if ($this->form_validation->run()) { //check to see if the user is logging in
      $remember_me = $this->input->post('remember') == 1;
      if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember_me)) {
        $user_id = $this->session->userdata('user_id');
        // backend only: the users are required to have permission to access the admin panel before they can do anything
        if ($this->environment == 'admin_panel') {
          // list all sites that they have permisison to
          $admin_sites = $this->permission->get_admin_sites($user_id);
          //if (in_array('admin_area', $permissions) || in_array('super_admin', $permissions)) {
          if (count($admin_sites) > 0) {
            $default_site = array_shift(array_values($admin_sites));
            $this->session->set_userdata('siteID', $default_site->id);
            $this->session->set_userdata('siteName', $default_site->name);
            $this->session->set_userdata('siteURL',  $default_site->domain);
            $this->session->set_userdata('sitePath', $default_site->path);
            // check user permission
            //$permissions = $this->permission->get_user_permissions($user_id, $default_site->id);
            //$this->session->set_userdata('permissions', $permissions);
          }
          else {
            $this->ion_auth->logout();
            show_error($this->lang->line('hotcms_error_insufficient_privilege'));
          }
          // load active sites
          //$this->load->model('dashboard/model_site');
          //foreach ($this->model_site->get_all_sites() as $row) {
          //  $aData['aSite'][$row->id] = $row;
          //}
          // assign site values
          //$this->session->set_userdata( 'siteName', $aData['aSite'][$this->input->post( 'cboSite' ) ? $this->input->post( 'cboSite' ) : key( $aData['aSite'] )]->name );
          //$this->session->set_userdata( 'siteURL',  $aData['aSite'][$this->input->post( 'cboSite' ) ? $this->input->post( 'cboSite' ) : key( $aData['aSite'] )]->domain );
          //$this->session->set_userdata( 'sitePath', $aData['aSite'][$this->input->post( 'cboSite' ) ? $this->input->post( 'cboSite' ) : key( $aData['aSite'] )]->path);
          $default_landing_page = 'dashboard/analysis';
        }
        else {
          // front-end only
          // check user permission
          //$permissions = $this->permission->get_user_permissions($user_id);
          //$this->session->set_userdata('permissions', $permissions);
          // load up points for user
          $user_points = $this->account_model->get_user_points($user_id);
          $this->session->set_userdata('user_points', $user_points);
          // assign site values
//          $this->session->set_userdata( 'siteName', $this->config->item('site_name'));
//          $this->session->set_userdata( 'siteURL', $this->config->item('site_domain'));
//          $this->session->set_userdata( 'sitePath', $this->config->item('site_domain'));
          $default_landing_page = '/';
        }

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        //redirect them back to the landing page
        $redirect_to = $this->session->userdata('redirect_to');
        if ($redirect_to > '') {
          $redirect_to = ltrim($redirect_to, '/');
          if ($redirect_to == 'hotcms') {
            $redirect_to = $default_landing_page;
          }
          elseif (substr($redirect_to, 0, 7) == 'hotcms/') {
            $redirect_to = substr($redirect_to, 7);
          }
          $this->session->set_userdata('redirect_to', '');
        }
        else {
          $redirect_to = $default_landing_page;
        }
        redirect($redirect_to);
	    }
	    else { //if the login was un-successful
        //TODO: log the attempts and check against the max attempts one can make
	      //redirect them back to the login page
	      $this->session->set_flashdata('error', $this->ion_auth->errors());
	      redirect($this->config->item('login_page')); //use redirects instead of loading views for compatibility with MY_Controller libraries
	    }
    }
	  else {  //the user is not logging in so display the login page
	    //set the flash data error or notice messages if any
      if (validation_errors()>''){
        $data['error'] .= validation_errors();
      }

      $data['username']  = array('name'    => 'username',
        'id'      => 'username',
        'type'    => 'text',
        'value'   => $this->form_validation->set_value('username'),
        'class'   => 'required'
      );
      $data['password']  = array('name'    => 'password',
        'id'      => 'password',
        'type'    => 'password',
        'class'   => 'required'
      );
      if ($this->environment == 'admin_panel') {
        $data['leftbar'] = '';
        $data['main_area'] = $this->load->view('login', $data, TRUE);
        $this->load->view('global', $data);
      }
      else {
        self::loadModuleView($this->aModuleInfo, $data, 'login');
      }
    }
  }

  /**
   * log user out
   */
	public function logout()
  {
    //log the user out
    $this->ion_auth->logout();
    //redirect to the login page
    redirect($this->config->item('login_page'));
  }

  /**
   * register a new user
   * @access public
   * @return void
   */
  public function register()
  {
    if ($this->ion_auth->logged_in()) {
      redirect($this->config->item('landing_page'));
      exit;
    }

    $this->data['sTitle'] = "Sign up";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    // Validation rules
    $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
    $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
    //$this->form_validation->set_rules('postal', 'Your Postal Code', 'xss_clean');
    $this->form_validation->set_rules('email', 'Email Address', 'required|filter_var|callback__email_check');
    $this->form_validation->set_rules('email_confirm', 'Confirm Email Address', 'required|matches[email]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length['.$this->config->item('min_password_length', 'ion_auth').']|max_length['.$this->config->item('max_password_length', 'ion_auth').']');
    $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');
    //$this->form_validation->set_rules('captcha', 'Verification Code', 'required|callback__captcha_check');
    //$this->form_validation->set_rules('terms',   'Privacy Policy',      'callback__validate_terms' );

    $email         = $this->input->post('email');
    $password      = $this->input->post('password');
    $first_name    = $this->input->post('first_name');
    $last_name     = $this->input->post('last_name');
    //$postal        = strtoupper($this->input->post('postal'));
    //$newsletter    = $this->input->post('newsletter');
    $user_data_array = array(
      'first_name' => $this->input->post('first_name'),
      'last_name'  => $this->input->post('last_name'),
    );
    $contact_data_array = array(
      'postal_code' => strtoupper($this->input->post('postal')),
      'cell' => $this->input->post('cell_phone'),
    );

    if ($this->form_validation->run()) {
      $id = $this->ion_auth->register($email, $password, $email, $user_data_array);

      if ($id) {
        //set basic user role
        $new_user = $this->ion_auth->get_newest_users(1);
        $user_id = $new_user[0]->id;
        $this->account_model->set_member_role($user_id);
        //create contact
        $this->load->model('contact/model_contact');
        $this->model_contact->insert($user_id, 'user', 'personal');
        $contact_id = $this->db->insert_id();
        $this->model_contact->update_single($contact_id, $contact_data_array);

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        //if ($email>'' && $newsletter==1){
        //  $result = $this->model->register_newsletter($first_name, $last_name, $email, $postal, '', '', 'member');
        //}
        // load module view
        self::loadModuleView( $this->aModuleInfo, $this->data, 'register_success' );
        $account_detail = str_replace(' ','',$postal);
        //if ($newsletter==1){
        //	if ($account_detail>''){
        //	  $account_detail .= "-";
        //	}
        //	$account_detail .= "Email_Subscriber";
        //}
        $this->session->set_flashdata('account_detail', $account_detail);
        redirect('/register-confirm');
      }
      else {
        $this->session->set_flashdata('error', $this->ion_auth->errors());
        redirect('register');
      }
    }
    else {
      // Return the validation error
      if (validation_errors()>'') {
        $this->data['error'] = validation_errors();
      }

      $this->data['first_name'] = array('name'    => 'first_name',
                                        'id'      => 'first_name',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('first_name'),
                                       );
      $this->data['last_name']  = array('name'    => 'last_name',
                                        'id'      => 'last_name',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('last_name'),
                                       );
      $this->data['postal']  = array('name'    => 'postal',
                                        'id'      => 'postal',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('postal'),
                                       );
      $this->data['cell_phone']  = array('name'    => 'cell_phone',
                                        'id'      => 'cell_phone',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('cell_phone'),
                                       );
      $this->data['email']      = array('name'    => 'email',
                                        'id'      => 'email',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('email'),
                                       );
      $this->data['email_confirm']      = array('name'    => 'email_confirm',
                                        'id'      => 'email_confirm',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('email_confirm'),
                                       );
      $this->data['password']   = array('name'    => 'password',
                                        'id'      => 'password',
                                        'type'    => 'password',
                                       );
      $this->data['password_confirm'] = array('name'    => 'password_confirm',
                                        'id'      => 'password_confirm',
                                        'type'    => 'password',
                                       );
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'register' );
    }
  }

  /**
    * Confirm register displaying method
    * @access public
    * @return void
    */
  public function confirm_register()
  {
    $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Sign up";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    $this->data['account_detail'] = $this->session->flashdata('account_detail');
    // load module view
    self::loadModuleView($this->aModuleInfo, $this->data, 'register_success');
  }

  //get email
  public function get_email()
  {
    if (!$this->ion_auth->logged_in()) {
      redirect($this->config->item('login_page'));
    }
    $user = $this->ion_auth->get_user($this->session->userdata('user_id'));
    echo $user->email;
    return true;
  }

  //change email
  public function change_email() {
    if (!$this->ion_auth->logged_in()) {
      redirect($this->config->item('login_page'));
    }

    $this->data['sTitle'] = "Change Email";
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    $this->form_validation->set_rules('email', 'New Email', 'required|filter_var|callback__email_check');
    $this->form_validation->set_rules('email_confirm', 'Confirm Email', 'required|matches[email]');

    $user = $this->ion_auth->get_user($this->session->userdata('user_id'));

    if ($this->form_validation->run()) {
      //$changed = $this->ion_auth->change_email($this->session->userdata('user_id'), $this->input->post('email'));
      $changed = $this->ion_auth->change_email_request($this->session->userdata('user_id'), $this->input->post('email'));

      if ($changed) { //if the email was successfully changed
        //$this->data['message'] = 'Email successfully changed.';
        $this->data['message'] = 'A verification email has been sent to your new email address.';
        $this->data['link'] = '<a class="triangle" href="#" onclick="return switchPhone();">Select Number</a>';
        $this->load->view('plain_message', $this->data);
        return true;
      }
      else {
        //$this->session->set_flashdata('error', $this->ion_auth->errors());
        //redirect('/my-account/change-password');
        $this->data['error'] = $this->ion_auth->errors();
      }
    }
    else {
      //set the validation error if any
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }
    }

    //display the form
    $this->data['email']           = array('name'    => 'email',
                                           'id'      => 'email',
                                           'type'    => 'text',
                                           'value'   => $this->form_validation->set_value('email'),
                                           );
    $this->data['email_confirm']   = array('name'    => 'email_confirm',
                                           'id'      => 'email_confirm',
                                           'type'    => 'text',
                                           'value'   => $this->form_validation->set_value('email_confirm'),
                                           );
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'change_password' );
    //$this->load->view('change_email', $this->data);
  }

  //verify a new email address
  public function verify_email($id, $code=false) {
    $activated = $this->ion_auth->change_email($id, $code);

    if ($activated) {
      //redirect them to the login page
      $this->session->set_flashdata('message', 'Your email has been changed successfully.');
    }
    else {
      //redirect them to the register page
      $this->session->set_flashdata('error', 'Failed to change the email');
    }
    redirect("my-account");
  }

  //change password
	public function change_password()	{
    if (!$this->ion_auth->logged_in()) {
      redirect($this->config->item('login_page'));
    }

    $this->data['sTitle'] = "Change Password";
    //set the flash data error or notice messages if any
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

	  $this->form_validation->set_rules('password', 'Current Password', 'required');
	  $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length['.$this->config->item('min_password_length', 'ion_auth').']|max_length['.$this->config->item('max_password_length', 'ion_auth').']');
	  $this->form_validation->set_rules('new_password_confirm', 'Confirm New Password', 'required|matches[new_password]');

	  $user = $this->ion_auth->get_user($this->session->userdata('user_id'));

	  if ($this->form_validation->run()) {
	    $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

	    $changed = $this->ion_auth->change_password($identity, $this->input->post('password'), $this->input->post('new_password'));

    	if ($changed) { //if the password was successfully changed
        $this->data['message'] = 'Password changed successfully.';
        $this->data['link'] = '<a class="triangle" href="#" onclick="return switchPhone();">Select Number</a>';
        //$this->load->view('plain_message', $this->data);
        self::loadModuleView( $this->aModuleInfo, $this->data, 'plain_message' );
        return true;
    	}
    	else {
    		//$this->session->set_flashdata('error', $this->ion_auth->errors());
    		//redirect('/my-account/change-password');
        $this->data['error'] = $this->ion_auth->errors();
    	}
	  }
    else {
      //set the validation error if any
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }
    }

    //display the form
    $this->data['password']           = array('name'    => 'password',
                                                'id'      => 'password',
                                                'type'    => 'password',
                                               );
    $this->data['new_password']           = array('name'    => 'new_password',
                                                'id'      => 'new_password',
                                                'type'    => 'password',
                                               );
    $this->data['new_password_confirm']   = array('name'    => 'new_password_confirm',
                                                    'id'      => 'new_password_confirm',
                                                    'type'    => 'password',
                                                    );
    $this->data['user_id']                = array('name'    => 'user_id',
                                                    'id'      => 'user_id',
                                                    'type'    => 'hidden',
                                                    'value'   => $user->id,
                                                   );
    // load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'change_password' );
    //$this->load->view('change_password', $this->data);
	}

	//forgot password
	public function forgot_password()	{
    $this->data['sTitle'] = "Forgot Password";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
		$this->form_validation->set_rules('email', 'Email Address', 'required|filter_var');
	  if ($this->form_validation->run()) {
      //run the forgotten password method to email an activation code to the user
      $mailsent = $this->ion_auth->forgotten_password($this->input->post('email'));

      if ($mailsent) { //if there were no errors
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect("login"); //we should display a confirmation page here instead of the login page
      }
      else {
        $this->session->set_flashdata('error', $this->ion_auth->errors());
        redirect("forgot-password");
      }
	  }
	  else {
      //set any errors and display the form
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }
      //setup the input
      $this->data['email'] = array('name'    => 'email',
                                     'id'    => 'email',
                         );
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'forgot_password' );
	  }
	}

	//reset password - final step for forgotten password
	public function reset_password($code)	{
		$reset = $this->ion_auth->forgotten_password_complete($code);

		if ($reset) {  //if the reset worked then send them to the login page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
      redirect("login");
		}
		else { //if the reset didnt work then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
      redirect("forgot-password");
		}
	}

	//activate a user
	public function activate($id, $code=false) {
		$activation = $this->ion_auth->activate($id, $code);

    if ($activation) {
	    //redirect them to the login page
	    $this->session->set_flashdata('message', $this->ion_auth->messages());
	    redirect("login");
    }
    else {
	    //redirect them to the register page
	    $this->session->set_flashdata('error', $this->ion_auth->errors());
	    redirect("register");
    }
  }

  //deactivate a user
	private function deactivate($id = NULL) {
    $this->data['sTitle'] = "Deactivate User";
		// no funny business, force to integer
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', 'confirmation', 'required');
		$this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

		if ( $this->form_validation->run() == FALSE ) {
			// insert csrf check
			$this->data['csrf']	=	$this->_get_csrf_nonce();
			$this->data['user']	=	$this->ion_auth->get_user($id);
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'deactivate_user' );
		}
		else {
			// do we really want to deactivate?
			if ( $this->input->post('confirm') == 'yes' ) {
				// do we have a valid request?
				if ( $this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id') ) {
					show_404();
				}

				// do we have the right userlevel?
				if ( $this->ion_auth->logged_in() && $this->ion_auth->is_admin() ) {
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			redirect('login', 'refresh');
		}
  }

  //create a new user
	private function create_user() {
    $this->data['sTitle'] = "Create User";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect($this->config->item('login_page'));
		}

    //validate form input
    $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
    $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
    $this->form_validation->set_rules('email', 'Email Address', 'required|filter_var');
    $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
    $this->form_validation->set_rules('province', 'Province', 'required|xss_clean');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length['.$this->config->item('min_password_length', 'ion_auth').']|max_length['.$this->config->item('max_password_length', 'ion_auth').']|matches[password_confirm]');
    $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

    if ($this->form_validation->run()) {
      $username  = $this->input->post('email');
      $email     = $this->input->post('email');
      $password  = $this->input->post('password');

      $additional_data = array('first_name' => $this->input->post('first_name'),
            'last_name'  => $this->input->post('last_name'),
        		'city'       => $this->input->post('city'),
        		'province'   => $this->input->post('province'),
      );
      if ( $this->ion_auth->register($username,$password,$email,$additional_data)) { //check to see if we are creating the user
        //redirect them back to the admin page
        $this->session->set_flashdata('message', "User Created");
        redirect($this->config->item('login_page'));
	    }else{
        $this->data['error'] = $this->ion_auth->errors();
      }
    }
		else { //display the create user form
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }

			$this->data['first_name']          = array('name'   => 'first_name',
		                                        'id'      => 'first_name',
		                                        'type'    => 'text',
		                                        'value'   => $this->form_validation->set_value('first_name'),
		                                       );
      $this->data['last_name']           = array('name'   => 'last_name',
		                                        'id'      => 'last_name',
		                                        'type'    => 'text',
		                                        'value'   => $this->form_validation->set_value('last_name'),
		                                       );
      $this->data['email']              = array('name'    => 'email',
		                                        'id'      => 'email',
		                                        'type'    => 'text',
		                                        'value'   => $this->form_validation->set_value('email'),
		                                       );
      $this->data['city']               = array('name'    => 'city',
		                                        'id'      => 'city',
		                                        'type'    => 'text',
		                                        'value'   => $this->form_validation->set_value('city'),
		                                       );
      $this->data['province']             = array('name'    => 'province',
		                                        'id'      => 'province',
		                                        'type'    => 'text',
		                                        'value'   => $this->form_validation->set_value('province'),
		                                       );
		  $this->data['password']           = array('name'    => 'password',
		                                        'id'      => 'password',
		                                        'type'    => 'password',
		                                        'value'   => $this->form_validation->set_value('password'),
		                                       );
      $this->data['password_confirm']   = array('name'    => 'password_confirm',
                                            'id'      => 'password_confirm',
                                            'type'    => 'password',
                                            'value'   => $this->form_validation->set_value('password_confirm'),
                                           );
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'create_user' );
		}
  }

  // email check
  public function _email_check($email) {
    if ($this->ion_auth->email_check($email)) {
      $this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  public function _validate_terms() {
    $isValid = $this->input->post( 'terms' );
    // if terms not checked / accepted
    if ( !$isValid ) {
      // assign error message
      $this->form_validation->set_message( '_validate_terms', 'You must read and accept the privacy policy and website terms and conditions of use.' );
    }
    return $isValid;
  }

  private function _get_csrf_nonce() {
	  $this->load->helper('string');
		$key	= random_string('alnum', 8);
		$value	= random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key=>$value);
	}

	private function _valid_csrf_nonce() {
		if ( $this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			 $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

  public function profile() {

    //$this->load->helper('auction/auction');

    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    $this->data['title'] = "User Profile";

    $userid = $this->session->userdata('user_id');
    //TODO get proper user ID
    if ($userid<1) {
      //redirect them to the login page
      redirect($this->config->item('login_page'));
    }
    else {

      $user = $this->account_model->get_user($userid);
      $this->data['user'] = $user;

      $section_html = widget::run('auction/auction_user_bids_widget', array());

      $this->data['items_list'] = $section_html;

      self::loadModuleView( $this->aModuleInfo, $this->data, 'profile' );
//      $this->load->view('profile', $this->data);
    }
  }

  public function edit_profile() {
    $this->load->model('contact/model_contact');
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    $userid = $this->session->userdata('user_id');

    $this->data['title'] = "Edit user profile";

		$this->form_validation->set_rules('first_name', 'first name', 'required');
		$this->form_validation->set_rules('last_name', 'last name', 'required');

		if ( $this->form_validation->run() == FALSE ){
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }
      if (($this->input->post('email')!='')){
        $user->first_name = $this->input->post('first_name');
        $user->last_name = $this->input->post('last_name');
        $user->email = $this->input->post('email');
      }
      else {
        $user = $this->account_model->get_user($userid);
      }
      $this->data['user'] = $user;


      $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

      //$this->data['form_contacts'] = modules::run('contact/controller/get_edit_forms', 'user', $userid);

      self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
    }
    else {
      $this->account_model->update_user($userid);

      $user = $this->account_model->get_user($userid);

      $this->data['user'] = $user;
      self::loadModuleView( $this->aModuleInfo, $this->data, 'profile' );
    }
  }

  public function edit_contact($item_id) {
    $this->load->model('contact/model_contact');
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    $userid = $this->session->userdata('user_id');

    $this->data['title'] = "Edit user profile";

    $this->form_validation->set_rules('email', 'email', 'required');

    if ($this->form_validation->run()) {
      $contact_data_array = array(
          'address_1' => $this->input->post('address_1'),
          'address_2' => $this->input->post('address_2'),
          'city' => $this->input->post('city'),
          'province' => $this->input->post('province'),
          'postal_code' => $this->input->post('postal_code'),
          'cell' => $this->input->post('cell'),
          'phone' => $this->input->post('phone'),
          'fax' => $this->input->post('fax'),
          'email' => $this->input->post('email'),
          'twitter' => $this->input->post('twitter'),
          'website' => $this->input->post('website'),
      );

      $this->model_contact->update_single($item_id,$contact_data_array);

      $user = $this->account_model->get_user($userid);
      $this->data['user'] = $user;
      $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

      $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => 'Personal contact updated' ) );

      self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
    }
    else {
      $user = $this->account_model->get_user($userid);

      $this->data['user'] = $user;

      $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

      $this->session->set_userdata( array( 'messageType' => 'error', 'messageValue' => validation_errors() ) );

      self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
    }
  }

}
