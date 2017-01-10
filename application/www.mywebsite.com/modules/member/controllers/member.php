<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends HotCMS_Controller {

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
    $this->load->config('member', TRUE);
    $this->load->model('member_model');
    $this->load->database();
    //$this->load->spark('ion_auth');
    $this->load->library('ion_auth');
    //$this->load->library('session');
    //$this->load->library('form_validation');
    //$this->load->helper('url');

    /**
    *  prepare module information
    *  they can be overriden in each function by using the same attribute
    */
    $this->aModuleInfo = array(
      'name'            => 'member',
      'title'           => $this->config->item('module_title', 'member'),
      'url'             => $this->config->item('module_url', 'member'),
      'meta_description' => $this->config->item('meta_description', 'member'),
      'meta_keyword'     => $this->config->item('meta_keyword', 'member'),
      'css'      => $this->config->item('css', 'member'),
      'js'      => $this->config->item('js', 'member')
    );

    @include(APPPATH.'config/routes.php');
  }

  //redirect if needed, otherwise display the user list
  public function index()
  {
    //if (empty($_SERVER['HTTPS'])) {
    //  header("Location:https://".$_SERVER['HTTP_HOST']."/my-account");
    //}
    if (!$this->ion_auth->logged_in()) {
      $this->data['sTitle'] = "My Account";
      //set the flash data error or notice messages if any
      $this->data['message'] = $this->session->flashdata('message');
      $this->data['error'] = $this->session->flashdata('error');
      // load both register and login view files
      //$this->firephp->fb($this->data);
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data );
    }
    //elseif ($this->ion_auth->is_admin()) {
    //	//redirect admin to hotcms
		//  redirect('/hotcms');
    //}
    else {
      redirect('/my-account/profile');
    }
  }

  //log the user in
  public function login()
  {
    // TODO: add a setting to the config file for HTTPS manditory links
    //if (empty($_SERVER['HTTPS'])) {
    //  header("Location:https://".$_SERVER['HTTP_HOST']."/login");
    //}
    $this->data['sTitle'] = "Member Login";
    $this->data['error'] = $this->session->flashdata('error');
    $this->data['message'] = $this->session->flashdata('message');

    //validate form input
    $this->form_validation->set_rules('email', 'Email Address', 'required|filter_var');
	  $this->form_validation->set_rules('password', 'Password', 'required');

    if ($this->form_validation->run()) { //check to see if the user is logging in
      //check for "remember me"
      if ($this->input->post('remember') == 1) {
        $remember = true;
      }
      else {
        $remember = false;
      }

      $redirect_to = $this->session->userdata('redirect_to') ? $this->session->userdata('redirect_to') : '/my-account/profile';
      if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), $remember)) {
        //if the login is successful
	      //redirect them back to the home page
	      $this->session->set_flashdata('message', $this->ion_auth->messages());
	      //$this->session->set_flashdata('seo_event', "user_logged_in");
        //var_dump($redirect_to);
        //var_dump($this->session->all_userdata());
        //die('a');
        redirect($redirect_to);
        //header("location: $redirect_to");
	    }
	    else { //if the login was un-successful
	      //redirect them back to the login page
	      $this->session->set_flashdata('error', $this->ion_auth->errors());
	      redirect($this->config->item('login_page')); //use redirects instead of loading views for compatibility with MY_Controller libraries
	    }
    }
	  else {  //the user is not logging in so display the login page
	    //set the flash data error or notice messages if any
      if (validation_errors()>''){
        $this->data['error'] = validation_errors();
      }

		  $this->data['email']      = array('name'    => 'email',
                                        'id'      => 'email',
                                        'type'    => 'text',
                                        'value'   => $this->form_validation->set_value('email'),
                                       );
      $this->data['password']   = array('name'    => 'password',
                                        'id'      => 'password',
                                        'type'    => 'password',
                                       );
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'login' );
	  }
  }

  //log the user out
	public function logout()
	{
    $this->data['title'] = "Member Logout";

    //log the user out
    $logout = $this->ion_auth->logout();

    //redirect them back to the page they came from
    redirect($this->config->item('login_page'));
    //header("location: $redirect_to");
  }

  /**
   * register a new user
   * @access public
   * @return void
   */
  public function register()
  {
    //if (empty($_SERVER['HTTPS'])) {
    //  header("Location:https://".$_SERVER['HTTP_HOST']."/register");
    //}
    $this->data['sTitle'] = "Sign up";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

    if ($this->ion_auth->logged_in()) {
      redirect('my-account');
    }

    // Validation rules
    $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
    $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
    $this->form_validation->set_rules('postal', 'Your Postal Code', 'xss_clean');
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
    $postal        = strtoupper($this->input->post('postal'));
    //$newsletter    = $this->input->post('newsletter');
    $user_data_array = array(
      'first_name' => $this->input->post('first_name'),
      'last_name'  => $this->input->post('last_name'),
      'postal'     => $postal,
    );

    if ($this->form_validation->run())
    {
     $id = $this->ion_auth->register($email, $password, $email, $user_data_array);

      if($id)
      {
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
      else
      {
        $this->session->set_flashdata('error', $this->ion_auth->errors());
        redirect('register');
      }
    }
    else
    {
      // Return the validation error
      if (validation_errors()>''){
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
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');
    $this->data['account_detail'] = $this->session->flashdata('account_detail');
		// load module view
    self::loadModuleView( $this->aModuleInfo, $this->data, 'register_success' );
  }

  //change email
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
  public function change_email()
  {
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
    }else{
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
  public function verify_email($id, $code=false)
  {
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
	public function change_password()
	{
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
    		//$this->session->set_flashdata('message', $this->ion_auth->messages());
        //redirect('my-account/switch-phone');
    		//$this->logout();
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
	  }else{
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
	public function forgot_password()
	{
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
	public function reset_password($code)
	{
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
	public function activate($id, $code=false)
	{
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
	private function deactivate($id = NULL)
	{
    $this->data['sTitle'] = "Deactivate User";
		// no funny business, force to integer
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', 'confirmation', 'required');
		$this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

		if ( $this->form_validation->run() == FALSE )
		{
			// insert csrf check
			$this->data['csrf']	=	$this->_get_csrf_nonce();
			$this->data['user']	=	$this->ion_auth->get_user($id);
      // load module view
      self::loadModuleView( $this->aModuleInfo, $this->data, 'deactivate_user' );
		}
		else
		{
			// do we really want to deactivate?
			if ( $this->input->post('confirm') == 'yes' )
			{
				// do we have a valid request?
				if ( $this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id') )
				{
                                        redirect('page-not-found');
					show_404();
				}

				// do we have the right userlevel?
				if ( $this->ion_auth->logged_in() && $this->ion_auth->is_admin() )
				{
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			redirect('member','refresh');
		}
  }

  //create a new user
	private function create_user()
	{
    $this->data['sTitle'] = "Create User";
    $this->data['message'] = $this->session->flashdata('message');
    $this->data['error'] = $this->session->flashdata('error');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
			redirect('member');
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
        redirect("member");
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
  public function _email_check($email)
  {
    if ($this->ion_auth->email_check($email))
    {
      $this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
      return FALSE;
    }
    else
    {
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

  private function _get_csrf_nonce()
  {
	  $this->load->helper('string');
		$key	= random_string('alnum', 8);
		$value	= random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key=>$value);
	}

	private function _valid_csrf_nonce()
	{
		if ( $this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			 $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

  public function profile()
  {

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

      $user = $this->member_model->get_user($userid);
      $this->data['user'] = $user;

      $section_html = widget::run('auction/auction_user_bids_widget', array());;

      $this->data['items_list'] = $section_html;

      self::loadModuleView( $this->aModuleInfo, $this->data, 'profile' );
//      $this->load->view('profile', $this->data);

    }

  }

  public function edit_profile() {

    //die(var_dump($this->input->post('first_name')));
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
      $user->postal = $this->input->post('postal');
      $user->email = $this->input->post('email');
     }else{
      $user = $this->member_model->get_user($userid);
     }
     $this->data['user'] = $user;

     self::loadModuleView( $this->aModuleInfo, $this->data, 'profile_edit' );

    }
    else{
      $this->member_model->update_user($userid);

      $user = $this->member_model->get_user($userid);

      $this->data['user'] = $user;
      self::loadModuleView( $this->aModuleInfo, $this->data, 'profile' );
    }

    //self::loadModuleView( $this->aModuleInfo, $this->data, 'profile_edit' );

  }

}
