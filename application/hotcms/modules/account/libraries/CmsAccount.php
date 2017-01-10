<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class CmsAccount {

  /**
   * CodeIgniter global, messages and errors
   * @var string
   */
  protected $ci;
  public $messages = array();
  public $errors = array();
  private $_draft;     // draft object. to access this attribute, use $training->draft
  private $_revisions; // array of revision objects. to access this attribute, use $training->revisions

  /**
   * __construct
   * @param  str  item ID or slug
   * @param  bool  only load live/published item
   * @return void
   */
  public function __construct()
  {
    $this->ci =& get_instance();
    $this->ci->load->model('account/account_model');
    $this->ci->load->helper('account/account');
  }

  /**
   * Acts as a simple way to call model methods without loads of alias
   */
  public function __call($method, $arguments)
  {
    if (!method_exists($this->ci->account_model, $method)) {
      throw new Exception('Undefined method CmsAccount::' . $method . '()');
    }
    return call_user_func_array(array($this->ci->account_model, $method), $arguments);
  }

  /**
   * Property getter
   */
  public function __get($property)
  {
    $method = 'get_' . strtolower($property);
    if (method_exists($this, $method)) {
      return $this->$method();
    }
  }

  /**
   * Set a message
   * @return void
   */
  public function set_message($message)
  {
    if (!in_array($message, $this->messages)) {
      $this->messages[] = $message;
    }
    return $message;
  }

  /**
   * Get the messages
   * @return string
   */
  public function messages()
  {
    $_output = '';
    foreach ($this->messages as $message) {
      $_output .= $message . "\n";
    }
    return $_output;
  }

  /**
   * Set an error message
   * @return void
   */
  public function set_error($error)
  {
    if (!in_array($error, $this->errors)) {
      $this->errors[] = $error;
    }
    return $error;
  }

  /**
   * Get the error message
   * @return string
   */
  public function errors()
  {
    $_output = '';
    foreach ($this->errors as $error) {
      $_output .= $error . "\n";
    }

    return $_output;
  }

  /**
   * user login
   */
  public function login()
  {
    $data = array();
    $data['sTitle'] = "User Login";
    $data['error'] = $this->session->flashdata('error');
    $data['message'] = $this->session->flashdata('message');

    //$data['java_script'] = $this->aModuleInfo['js'];

    //validate form input
    $this->form_validation->set_rules('username', strtolower('lang:hotcms_name_user'), 'trim|required');
	  $this->form_validation->set_rules('password', strtolower('lang:hotcms_password'), 'required');

    if ($this->form_validation->run()) { //check to see if the user is logging in
      $remember_me = $this->input->post('remember') == 1;
      if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember_me)) {
        $user_id = $this->session->userdata('user_id');
        $user_points = $this->account_model->get_user_points($user_id);
        $this->session->set_userdata('user_points', $user_points);
        $default_landing_page = 'overview';

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        //redirect them back to the landing page
 /*       $redirect_to = $this->session->userdata('redirect_to');
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
        }*/
        //die(var_dump($default_landing_page));
        redirect('overview');
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
        $data['error'] .= validation_errors();
      }

      $data['username']  = array('name'    => 'username',
        'id'      => 'username',
        'type'    => 'email',
        'value'   => $this->form_validation->set_value('username'),
        'class'   => 'required'
      );
      $data['password']  = array('name'    => 'password',
        'id'      => 'password',
        'type'    => 'password',
        'class'   => 'required'
      );
      return $data;
    }
  }

  /**
   * register a new user
   * @access public
   * @return void
   */
  public function register()
  {
    $data['sTitle'] = "Sign up";
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    if ($this->ion_auth->logged_in()) {
      //redirect('my-account');
    }

    // Validation rules
    $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
    $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
    $this->form_validation->set_rules('screen_name', 'Screen Name', 'required|callback__screen_check');
    //$this->form_validation->set_rules('postal', 'Your Postal Code', 'xss_clean');
    $this->form_validation->set_rules('email', 'Email Address', 'required|filter_var|callback__email_check');
    $this->form_validation->set_rules('email_confirm', 'Confirm Email Address', 'required|matches[email]');
    $this->form_validation->set_rules('password2', 'Password', 'required|min_length['.$this->config->item('min_password_length', 'ion_auth').']|max_length['.$this->config->item('max_password_length', 'ion_auth').']');
    $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password2]');
    //$this->form_validation->set_rules('captcha', 'Verification Code', 'required|callback__captcha_check');
    //$this->form_validation->set_rules('terms',   'Privacy Policy',      'callback__validate_terms' );
    $this->form_validation->set_rules('retailer', 'Retailer', 'required|xss_clean');
    $this->form_validation->set_rules('store', 'Location', 'required|xss_clean');
    $this->form_validation->set_rules('employment', 'Employment', 'required|xss_clean');
    $this->form_validation->set_rules('job_title', 'Job Title', 'required|xss_clean');
    $this->form_validation->set_rules('country_code', 'Country', 'required|xss_clean');
    $this->form_validation->set_rules('province', 'Province', 'required|xss_clean');

    $email         = $this->input->post('email');
    $password      = $this->input->post('password2');
    $ref_site_id = (int)($this->input->post('ref')) > 0 ? (int)($this->input->post('ref')) : 1;

    $first_name    = $this->input->post('first_name');
    $last_name     = $this->input->post('last_name');
    $screen_name   = $this->input->post('screen_name');
    $country_code   = $this->input->post('country_code');
    $province_code   = $this->input->post('province');
    $selected_retailer = $this->input->post('retailer');
    $selected_store = $this->input->post('store');
    $selected_employment = $this->input->post('employment');
    $selected_job_title = $this->input->post('job_title');

    $user_data_array = array(
      'first_name'=> $this->input->post('first_name'),
      'last_name' => $this->input->post('last_name'),
      'screen_name' => $this->input->post('screen_name'),
      'country_code' => $country_code,
      'province_code' => $province_code,
      'retailer_id' => $selected_retailer,
      'store_id' => $selected_store,
      'referral_code' => $this->input->post('referral_code'),
      'employment' => $selected_employment,
      'job_title' => $selected_job_title,
      'hire_date' => $this->input->post('hire_date'),
      'newsletter_monthly' => $this->input->post('newsletter_monthly'),
      'newsletter_newlab' => $this->input->post('newsletter_newlab'),
      'newsletter_newswag' => $this->input->post('newsletter_newswag'),
      'newsletter_survey' => $this->input->post('newsletter_survey'),
    );
//    $contact_data_array = array(
//      'postal_code' => strtoupper($this->input->post('postal')),
//      'cell' => $this->input->post('cell_phone'),
//    );

    if ($this->form_validation->run()) {
      $id = $this->ion_auth->register($email, $password, $email, $user_data_array, $ref_site_id);
      if ($id) {
        //set basic user role
        $new_user = $this->ion_auth->get_newest_users(1);
        $user_id = $new_user[0]->id;
        $this->account_model->set_member_role($user_id);
        //create contact
        $this->load->model('contact/model_contact');
        $this->model_contact->insert($user_id, 'user', 'personal');
        $contact_id = $this->db->insert_id();
        //$this->model_contact->update_single($contact_id, $contact_data_array);

        $this->session->set_flashdata('message', $this->ion_auth->messages());
        //if ($email>'' && $newsletter==1){
        //  $result = $this->model->register_newsletter($first_name, $last_name, $email, $postal, '', '', 'member');
        //}
        //return $data;

        //$account_detail = str_replace(' ', '', $postal);
        //if ($newsletter==1){
        //	if ($account_detail>''){
        //	  $account_detail .= "-";
        //	}
        //	$account_detail .= "Email_Subscriber";
        //}
        //$this->session->set_flashdata('account_detail', $account_detail);
        redirect('/register-confirm');
      }
      else {
        $this->session->set_flashdata('error', $this->ion_auth->errors());
      }
    }
    // Return the validation error
    elseif (validation_errors()>'') {
      $data['error'] = validation_errors();
    }

    if (is_array($_REQUEST) && array_key_exists('ref', $_REQUEST)) {
      $ref_id = $_REQUEST['ref'];
    }
    else {
      $ref_id = 1;
    }
    $ref_site = $this->account_model->get_site_name($ref_id);
    if ($ref_site && $ref_site->primary == 0) {
      $data['ref_site_name'] = $ref_site->name;
    }
    else {
      $data['ref_site_name'] = '';
    }
    // load dropdown options
    $data['retailers'] = array('' => '');
    $retailers = account_retailers();
    foreach ($retailers as $v) {
      $data['retailers'][$v->id] = $v->name;
    }
    $data['retailers'][99999] = 'Other';
    $data['stores'] = array('' => '');
    $stores = account_stores();
    foreach ($stores as $v) {
      $data['stores'][$v->id] = $v->store_name . ' (' . $v->store_num . ')';
    }
    $data['stores'][99999] = 'Other';
    $data['employments'] = array('' => '') + account_employments();
    $data['job_titles'] = array('' => '') + account_job_titles();
    $data['selected_country'] = $country_code;
    $data['selected_province'] = $province_code;
    $data['selected_retailer'] = $selected_retailer;
    $data['selected_store'] = $selected_store;
    $data['selected_employment'] = $selected_employment;
    $data['selected_job_title'] = $selected_job_title;
    $data['selected_newsletter_monthly'] = $this->input->post('newsletter_monthly');
    $data['selected_newsletter_newlab'] = $this->input->post('newsletter_newlab');
    $data['selected_newsletter_newswag'] = $this->input->post('newsletter_newswag');
    $data['selected_newsletter_survey'] = $this->input->post('newsletter_survey');

    $data['first_name'] = array('name'    => 'first_name',
      'id'      => 'first_name',
      'type'    => 'text',
      'value'   => $this->form_validation->set_value('first_name'),
    );
    $data['last_name']  = array('name'    => 'last_name',
      'id'      => 'last_name',
      'type'    => 'text',
      'value'   => $this->form_validation->set_value('last_name'),
    );
    $data['screen_name']  = array('name'    => 'screen_name',
      'id'      => 'screen_name',
      'type'    => 'text',
      'value'   => $this->form_validation->set_value('screen_name'),
    );
//    $data['postal']  = array('name'    => 'postal',
//      'id'      => 'postal',
//      'type'    => 'text',
//      'value'   => $this->form_validation->set_value('postal'),
//    );
//    $data['cell_phone']  = array('name'    => 'cell_phone',
//      'id'      => 'cell_phone',
//      'type'    => 'text',
//      'value'   => $this->form_validation->set_value('cell_phone'),
//    );
    $data['email']      = array('name'    => 'email',
      'id'      => 'email',
      'type'    => 'text',
      'value'   => $this->form_validation->set_value('email'),
    );
    $data['email_confirm']      = array('name'    => 'email_confirm',
      'id'      => 'email_confirm',
      'type'    => 'text',
      'value'   => $this->form_validation->set_value('email_confirm'),
    );
    $data['password']   = array(      'name'    => 'password2',
      'id'      => 'password2',
      'type'    => 'password',
    );
    $data['password_confirm'] = array('name'    => 'password_confirm',
      'id'      => 'password_confirm',
      'type'    => 'password',
    );
    $data['referral_code']     = array('name' => 'referral_code',
      'id'      => 'referral_code',
      'type'    => 'text',
      'value'   => $this->input->post('referral_code'),
    );
    $data['hire_date']     = array('name' => 'hire_date',
      'id'      => 'hire_date',
      'type'    => 'text',
      'value'   => $this->input->post('hire_date'),
    );
    return $data;
  }

  // screen name check
  public function _screen_check($screen_name)
  {
    $email = $this->input->post('email');
    $last_name = $this->input->post('last_name');
    if ($screen_name == $email || $screen_name == $last_name) {
      $this->form_validation->set_message('_screen_check', 'Your screen name can not be the same as your last name or your email address');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  // email check
  public function _email_check($email)
  {
    if ($this->ion_auth->email_check($email)) {
      $this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

}
