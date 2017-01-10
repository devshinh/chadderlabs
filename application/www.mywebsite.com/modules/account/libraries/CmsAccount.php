<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->model('account/account_model');
        $this->ci->load->helper('account/account');
        $this->ci->load->helper('retailer/retailer');
    }

    /**
     * Acts as a simple way to call model methods without loads of alias
     */
    public function __call($method, $arguments) {
        if (!method_exists($this->ci->account_model, $method)) {
            throw new Exception('Undefined method CmsAccount::' . $method . '()');
        }
        return call_user_func_array(array($this->ci->account_model, $method), $arguments);
    }

    /**
     * Property getter
     */
    public function __get($property) {
        $method = 'get_' . strtolower($property);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     * Set a message
     * @return void
     */
    public function set_message($message) {
        if (!in_array($message, $this->messages)) {
            $this->messages[] = $message;
        }
        return $message;
    }

    /**
     * Get the messages
     * @return string
     */
    public function messages() {
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
    public function set_error($error) {
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }
        return $error;
    }

    /**
     * Get the error message
     * @return string
     */
    public function errors() {
        $_output = '';
        foreach ($this->errors as $error) {
            $_output .= $error . "\n";
        }

        return $_output;
    }

    /**
     * user login
     */
    public function login() {
       // die('login function form cmsAccount library');
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
                  } */
                //die(var_dump($default_landing_page));
                redirect('overview');
            } else { //if the login was un-successful
                //redirect them back to the login page
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect($this->config->item('login_page')); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        } else {  //the user is not logging in so display the login page
            //set the flash data error or notice messages if any
            if (validation_errors() > '') {
                $data['error'] .= validation_errors();
            }

            $data['username'] = array('name' => 'username',
                'id' => 'username',
                'type' => 'email',
                'value' => $this->form_validation->set_value('username'),
                'class' => 'required'
            );
            $data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class' => 'required'
            );
            return $data;
        }
    }

    /**
     * register a new user
     * @access public
     * @return void
     */
    public function register() {
        $data['sTitle'] = "Sign up";
        $data['message'] = $this->session->flashdata('message');
        $data['error'] = $this->session->flashdata('error');

        if ($this->ion_auth->logged_in()) {
            //redirect('my-account');
        }
        $email = $this->input->post('email');
        $password = $this->input->post('password2');
        $ref_site_id = (int) ($this->input->post('ref')) > 0 ? (int) ($this->input->post('ref')) : 1;

        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $screen_name = $this->input->post('screen_name');
        $country_code = $this->input->post('country_code');
        $province_code = $this->input->post('province');
        $selected_retailer = $this->input->post('retailer');
        $selected_store = $this->input->post('store');
        $selected_employment = $this->input->post('employment');
        $selected_job_title = $this->input->post('job_title');
        $no_error = $this->input->post('noerror'); // if post from homepage, no need to display errors

        $promo_code = $this->input->get('promo_code');

        $user_data_array = array(
            'first_name' => $this->input->post('first_name'),
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
            'retailer_name' => $this->input->post('retailer_name'),
            'retailer_location_name' => $this->input->post('retailer_location_name'),
        );

//        if ($selected_employment == 'Other'){
//             $user_data_array['employment'] = $this->input->post('employment_type');
//        }
//        if ($selected_job_title == 'Other'){
//             $user_data_array['job_title'] = $this->input->post('job_title_name');
//        }

        if ($this->input->post('form') == 'register') {
            // Validation rules
            $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
            $this->form_validation->set_rules('screen_name', 'Screen Name', 'alpha_dash|required|callback__screen_check|is_unique[user_profile.screen_name]');
            //$this->form_validation->set_rules('postal', 'Your Postal Code', 'xss_clean');
            $this->form_validation->set_rules('email', 'Email Address', 'required|filter_var|callback__email_check');
            $this->form_validation->set_rules('email_confirm', 'Confirm Email Address', 'required|matches[email]');
            $this->form_validation->set_rules('password2', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']');
            $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password2]');
            //$this->form_validation->set_rules('captcha', 'Verification Code', 'required|callback__captcha_check');
            //$this->form_validation->set_rules('terms',   'Privacy Policy',      'callback__validate_terms' );
            $this->form_validation->set_rules('retailer', 'Retailer', 'required|xss_clean');
            $this->form_validation->set_rules('store', 'Location', 'required|xss_clean');
            $this->form_validation->set_rules('employment', 'Employment', 'required|xss_clean');
            $this->form_validation->set_rules('job_title', 'Job Title', 'required|xss_clean');
            $this->form_validation->set_rules('country_code', 'Country', 'required|xss_clean');
            $this->form_validation->set_rules('province', 'Province', 'required|xss_clean');

            if ($user_data_array['retailer_id'] == 99999) {
                $this->form_validation->set_rules('retailer_name', 'Retailer\'s Name', 'required|xss_clean');
            }
            if ($user_data_array['store_id'] == 99999) {
                $this->form_validation->set_rules('retailer_location_name', 'Retailer\'s Location Name', 'required|xss_clean');
            }

//            if ($selected_employment == 'Other') {
//                $this->form_validation->set_rules('employment_type', 'Employment Type', 'required|xss_clean');
//            }
//            if ($selected_job_title == 'Other') {
//                $this->form_validation->set_rules('job_title_name', 'Job Title Name', 'required|xss_clean');
//            }


//    $contact_data_array = array(
//      'postal_code' => strtoupper($this->input->post('postal')),
//      'cell' => $this->input->post('cell_phone'),
//    );

            if ($this->form_validation->run()) {

                //campaign monitor
                //load campaing monitor list ids
                $this->cm_lists = $this->config->item('cm_lists', 'account');

                $monthly_list_id = $this->cm_lists['monthly'];
                $new_swag_list_id = $this->cm_lists['swag'];
                $new_labs_list_id = $this->cm_lists['labs'];
                $survey_list_id = $this->cm_lists['survey'];

                if($this->input->post('newsletter_monthly')){
                    $subscriber = array(
			'EmailAddress' => $email,
			'Name' => $first_name.' '.$last_name
                    );
		    $result = $this->cmonitor->post_request('subscribers/'.$monthly_list_id.'.json', $subscriber);
                }

                if($this->input->post('newsletter_newswag')){
                    $subscriber = array(
			'EmailAddress' => $email,
			'Name' => $first_name.' '.$last_name
                    );
		   $result = $this->cmonitor->post_request('subscribers/'.$new_swag_list_id.'.json', $subscriber);
                }

                if($this->input->post('newsletter_newlab')){
                    $subscriber = array(
			'EmailAddress' => $email,
			'Name' => $first_name.' '.$last_name
                    );
		   $result = $this->cmonitor->post_request('subscribers/'.$new_labs_list_id.'.json', $subscriber);
                }

                if($this->input->post('newsletter_survey')){
                    $subscriber = array(
			'EmailAddress' => $email,
			'Name' => $first_name.' '.$last_name
                    );
		   $result = $this->cmonitor->post_request('subscribers/'.$survey_list_id.'.json', $subscriber);
                }

                $id = $this->ion_auth->register($email, $password, $email, $user_data_array, $ref_site_id);
                if ($id) {
                    //set basic user role
                    $new_user = $this->ion_auth->get_newest_users(1);
                    $user_id = $new_user[0]->id;
                    $this->account_model->set_member_role($user_id, $ref_site_id);
                    //create contact
                    $this->load->model('contact/model_contact');
                    $this->model_contact->insert($user_id, 'user', 'personal');
                    $contact_id = $this->db->insert_id();


                    //new retailer? new store?
                    if (!empty($user_data_array['retailer_name'])) {
                        $this->load->model('retailer/retailer_model');
                        $data = array(
                            'name' => $user_data_array['retailer_name'],
                            'country_code' => $user_data_array['country_code']
                        );
                        $new_retailer_id = $this->retailer_model->retailer_insert($data, $user_id);
                        $user_data_array['retailer_id'] = $new_retailer_id;
                        //update user_profile with new retailer id
                        $this->account_model->update_user_retailer($user_id, $new_retailer_id);
                    }
                    if (!empty($user_data_array['retailer_location_name'])) {
                        $this->load->model('retailer/retailer_model');
                        $data = array(
                            'store_name' => $user_data_array['retailer_location_name'],
                            'country_code' => $user_data_array['country_code'],
                            'province' => $user_data_array['province_code'],
                            'store_num' => '',
                            'street_1' => '',
                            'street_2' => '',
                            'city' => '',
                            'postal_code' => '',
                            'phone' => '',
                            'author_id' => $user_id
                        );

                        if (isset($new_retailer_id)) {
                            $new_store_id = $this->retailer_model->store_insert($new_retailer_id, $data);
                        } else {
                            $new_store_id = $this->retailer_model->store_insert($user_data_array['retailer_id'], $data);
                        }

                        $user_data_array['store_id'] = $new_store_id;
                        $this->account_model->update_user_store($user_id, $new_store_id);
                    }

                    //PROMO CODES
                    //@todo rename input
                    if(!empty($user_data_array['referral_code'])){
                        //log the user attempt
                        $this->account_model->log_user_promo_code($user_id, $user_data_array['referral_code']);
                        //is the code active?
                        //we have just one now
                        if(strtolower($user_data_array['referral_code']) == 'morecheddar'){
                            //add 1000 points to user account
                             account_add_points($user_id, 1000, 'award', $ref_table = 'redeem_code_morecheddar', $user_id, 'received 1,000 points for using secret promo code.');
                        }

                        if(strtolower($user_data_array['referral_code']) == 'newuser'){
                            //add 1000 points to user account
                             account_add_points($user_id, 1000, 'award', $ref_table = 'redeem_code_morecheddar', $user_id, 'received 1,000 points for registering a new account.');
                        }
                    }

  account_add_points($user_id, 1000, 'award', $ref_table = 'new_registration', $user_id, 'received 1,000 points for registering a new account.');

                    //account_add_points($user_id, 1000, 'award', $ref_table = 'new_registration', $user_id, 'received 1,000 points for registering a new account.');

                    //$this->model_contact->update_single($contact_id, $contact_data_array);
                    //$this->session->set_flashdata('message', $this->ion_auth->messages());
                    $data['message'] = $this->ion_auth->messages();
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
                } else {
                    //$this->session->set_flashdata('error', $this->ion_auth->errors());
                    $data['error'] = $this->ion_auth->errors();
                }
            }
            // Return the validation error
            elseif (validation_errors() > '' && $no_error != "1") {
                $data['error'] = validation_errors();
            }
        }


        if (is_array($_REQUEST) && array_key_exists('ref', $_REQUEST)) {
            $ref_id = $_REQUEST['ref'];
        } else {
            $ref_id = 1;
        }
        $ref_site = $this->account_model->get_site_name($ref_id);
        if ($ref_site && $ref_site->primary == 0) {
            $data['ref_site_name'] = $ref_site->name;
        } else {
            $data['ref_site_name'] = '';
        }
        // load dropdown options
        $data['retailers'] = array('' => '');
        $retailers = account_retailers($country_code);
        foreach ($retailers as $v) {
            $data['retailers'][$v->id] = $v->name;
        }
        $data['retailers'][99999] = 'Other';
        $data['stores'] = array('' => '');
        $stores = account_stores($selected_retailer);
        foreach ($stores as $v) {
            if(empty($v->store_num)){
              $data['stores'][$v->id] = $v->store_name;
            }else{
              $data['stores'][$v->id] = $v->store_name . ' (' . $v->store_num . ')';
            }
        }
        $data['stores'][99999] = 'Other';
        if ($country_code > '') {
            $data['province_options'] = array('' => '') + list_province_array($country_code);
        } else {
            $data['province_options'] = array('' => '');
        }
        $data['country_options'] = array('' => 'Please select country' ,'US' => 'USA', 'CA' => 'Canada');

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

        $data['first_name'] = array('name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name'),
        );
        $data['last_name'] = array('name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name'),
        );
        $data['screen_name'] = array('name' => 'screen_name',
            'id' => 'screen_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('screen_name'),
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
        $data['email'] = array('name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
        );
        $data['email_confirm'] = array('name' => 'email_confirm',
            'id' => 'email_confirm',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email_confirm'),
        );
        $data['password'] = array('name' => 'password2',
            'id' => 'password2',
            'type' => 'password',
        );
        $data['password_confirm'] = array('name' => 'password_confirm',
            'id' => 'password_confirm',
            'type' => 'password',
        );
        $data['referral_code'] = array('name' => 'referral_code',
            'id' => 'referral_code',
            'type' => 'text',
            'value' => $this->input->post('referral_code'),
        );
        $data['hire_date'] = array('name' => 'hire_date',
            'id' => 'hire_date',
            'type' => 'text',
            'value' => $this->input->post('hire_date'),
        );
        $data['retailer_name'] = array(
            'name' => 'retailer_name',
            'id' => 'retailer_name',
            'type' => 'text',
            'value' => $this->input->post('retailer_name'),
        );
        $data['retailer_location_name'] = array(
            'name' => 'retailer_location_name',
            'id' => 'retailer_location_name',
            'type' => 'text',
            'value' => $this->input->post('retailer_location_name'),
        );
        $data['employment_type'] = array(
            'name' => 'employment_type',
            'id' => 'employment_type',
            'type' => 'text',
            'value' => $this->input->post('employment_type'),
        );
        $data['job_title_name'] = array(
            'name' => 'job_title_name',
            'id' => 'job_title_name',
            'type' => 'text',
            'value' => $this->input->post('job_title_name'),
        );

        if(!empty($promo_code)){
            $data['referral_code'] = array(
            'name' => 'referral_code',
            'id' => 'referral_code',
            'type' => 'text',
            'value' => $promo_code,
        );


        }
        return $data;
    }

    // screen name check
    public function _screen_check($screen_name) {
        $email = $this->input->post('email');
        $last_name = $this->input->post('last_name');
        if ($screen_name == $email || $screen_name == $last_name) {
            $this->form_validation->set_message('_screen_check', 'Your screen name can not be the same as your last name or your email address');
            return FALSE;
        } else {
            return TRUE;
        }
    }
        //check if screen name is used
    public function screen_name_check($screen_name) {
        var_dump($screen_name);die('a');
        if ($this->account_model->is_screenname_used($screen_name)) {
            $this->form_validation->set_message('_screenname_check', 'Screen name already in use.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // email check
    public function _email_check($email) {
        if ($this->ion_auth->email_check($email)) {
            //$this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
            $this->form_validation->set_message('_email_check', 'This email address is already registered.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * register a new brand
     * @access public
     * @return void
     */
    public function register_brand() {

        $data['sTitle'] = "Brand Sign up";
        $data['message'] = $this->session->flashdata('message');
        $data['error'] = $this->session->flashdata('error');


        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email');
        $email_confirm = $this->input->post('email_confirm');
        $company = $this->input->post('company');
        $phone = $this->input->post('phone');
        $comments = $this->input->post('comments');


        // Validation rules
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
        $this->form_validation->set_rules('email_confirm', 'Email Confirm', 'required|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');


        if ($this->form_validation->run()) {
              //save data to db
	      $response_recaptcha=JSON_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfAhwITAAAAAJtE_-1cPQwBfMHCp-exmiwZeL7t&response=".$this->input->post('g-recaptcha-response')."&remoteip=".$_SERVER['REMOTE_ADDR']));
if(isset($response_recaptcha->success) && $response_recaptcha->success == false) {

	  $data['error'] = 'Are You A Robot?';

	         $data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name'),
        );
        $data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name'),
        );
        $data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
        );
        $data['email_confirm'] = array(
            'name' => 'email_confirm',
            'id' => 'email_confirm',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email_confirm'),
        );
        $data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'value' => $this->form_validation->set_value('phone'),
        );
        $data['company'] = array(
            'name' => 'company',
            'id' => 'company',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company'),
        );
        $data['comments'] = array(
            'name' => 'comments',
            'id' => 'comments',
            'type' => 'text',
            'value' => $this->form_validation->set_value('comments'),
        );


	  return $data;
}


            $brand_info = $this->input->post();
              //save to database
              $this->account_model->save_brand_info($brand_info);
              //send email to cheddar team
                         $message_to_cheddarlabs =
                   '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Cheddar Labs</title>
  </head>
  <body style="font: 13px/18px arial, sans-serif; background-color: white; width:100%; margin: 0; padding: 0; color:#4e4e4e">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
     <tr><td>First Name:</td><td>'.$brand_info['first_name'].'</td></tr>
     <tr><td>Last Name:</td><td>'.$brand_info['last_name'].'</td></tr>
     <tr><td>Company:</td><td>'.$brand_info['company'].'</td></tr>
     <tr><td>Phone:</td><td>'.$brand_info['phone'].'</td></tr>
     <tr><td>Email:</td><td>'.$brand_info['email'].'</td></tr>
     <tr><td>Comments:</td><td>'.$brand_info['comments'].'</td></tr>
    </table>
  </body>
</html>
';


            $this->postmark->clear();
            $config['mailtype'] = "html";
            $this->postmark->initialize($config);
            $this->postmark->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->postmark->to('thomas@hottomali.com');
            $this->postmark->bcc('jan@hottomali.com');
            $this->postmark->subject('CheddarLabs - New brand contant info');

            $this->postmark->message_html($message_to_cheddarlabs);

            if ($this->postmark->send()) {
                //echo TRUE;
            } else {
                //echo FALSE;
            }

              //send email to user
            $first_name_string = ucfirst($brand_info['first_name']);

            $message_user = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Cheddar Labs Brand Sign-Up Received</title>
  </head>
  <body style="font: 13px/18px arial, sans-serif; background-color: #e5e5e5; width:100%; margin: 0; padding: 0; color:#4e4e4e">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
	<td style="padding-top: 30px; padding-left: 20px; padding-bottom: 30px;">
	  <table cellspacing="0" cellpadding="0" border="0" style="width: 750px; background: white; border-right: 2px solid #DDD; border-bottom: 2px solid #BBB">
	    <tr>
	      <td width="730px" style="padding-top: 12px; padding-left: 10px">
		<a href="http://www.cheddarlabs.com"><img width="730px" height="123px" src="http://{$_SERVER['HTTP_HOST']}/asset/images/email/email-header-w1.jpg" alt="Thank you" /></a>
	      </td>
	    </tr>
	    <tr>
	      <td style="padding-left: 10px; padding-top: 10px; padding-right: 10px; valign="top">
		<table cellpadding="0" cellspacing=0" align="left" width="730px;" style="background-color: white;">
		  <tr>
		    <td>
		      <table width="730px" border="0" align="left" cellspacing="0" cellpadding="0" style="padding-right: 20px; padding-left: 10px; border-right: solid 4px #FFF; background-color: white">
			<tr>
			  <td style="padding-top: 20px"><h2 style="font-size: 1.25em; color: #b39451">We received you request.</h2></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;"><p>Dear {$first_name_string}</p></td>
			</tr>

   	                <tr>
			  <td style="padding-bottom: 5px;"><p>We’re super excited about your interest in Cheddar Labs! We'll review your information and get back to you as soon as possible.</p></td>
			</tr>

   	                <tr>
			  <td style="padding-bottom: 5px;"><p>If you aren’t into the whole waiting for someone to read your email thing, give us a shout at 604-893-8347. We’d love to chat about how we can elevate your brand’s impact at retail.</p></td>
			</tr>

			<tr>
			  <td style="padding-bottom: 5px;"><p>Sincerely,</p></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;"><p style="line-height:20px; height: 20px;display:block;"><img height='20px' width='18px' src="http://{$_SERVER['HTTP_HOST']}/asset/images/email/icon-cheddar-signature-atom.png" alt="Cheddar Atom" /> The Dept. of Cheddar</p></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 20px;"></td>
			</tr>
		      </table>
		    </td>
		    <td valign="top" style="padding-left: 20px; padding-right: 20px; padding-top: 20px; background-color: white">

		    </td>
		  </tr>
		</table>
	      </td>
	    </tr>
	  </table>
	</td>
      </tr>
      <tr>
	<td style="border-top: 1px dotted #CCC; padding-top; 5px; padding-left: 20px; padding-right: 20px; background: white; font-size: 10pt; width: 730px;"></td>
      </tr>
    </table>
  </body>
</html>
EOF;

            $this->postmark->clear();
            $config['mailtype'] = "html";
            $this->postmark->initialize($config);
            $this->postmark->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->postmark->to($brand_info['email']);
            $this->postmark->bcc('jan@hottomali.com');
            $this->postmark->subject('Cheddar Labs Brand Sign-Up Received');
            $this->postmark->message_html($message_user);

            if ($this->postmark->send()) {
                //echo TRUE;
            } else {
               // echo FALSE;
            }



                redirect('/register-confirm-brand');
            } else {
                //$this->session->set_flashdata('error', $this->ion_auth->errors());
                $data['error'] = validation_errors();
            }

        $data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name'),
        );
        $data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name'),
        );
        $data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
        );
        $data['email_confirm'] = array(
            'name' => 'email_confirm',
            'id' => 'email_confirm',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email_confirm'),
        );
        $data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'value' => $this->form_validation->set_value('phone'),
        );
        $data['company'] = array(
            'name' => 'company',
            'id' => 'company',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company'),
        );
        $data['comments'] = array(
            'name' => 'comments',
            'id' => 'comments',
            'type' => 'text',
            'value' => $this->form_validation->set_value('comments'),
        );
        return $data;
    }

}
