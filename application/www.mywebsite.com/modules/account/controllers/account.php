<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Account extends HotCMS_Controller {

    // module information
    protected $aModuleInfo;

    /**
     * Constructor method
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->config('account/account', TRUE);
        $this->load->model('account/account_model');
        $this->load->model('verification/verification_model');
        $this->load->helper('account/account');
        $this->load->helper('quiz/quiz');
        $this->load->helper('user/user');
        
        /**
         * prepare module information
         * can be overriden in each function
         */
        $this->aModuleInfo = array(
            'name' => 'account',
            'title' => $this->config->item('module_title', 'account'),
            'meta_title' => $this->config->item('module_title', 'account'),
            'url' => $this->config->item('module_url', 'account'),
            'meta_description' => $this->config->item('meta_description', 'account'),
            'meta_keyword' => $this->config->item('meta_keyword', 'account'),
            'css' => $this->config->item('css', 'account'),
            'javascript' => $this->config->item('js', 'account')
        );
        
        //load campaing monitor list ids
        $this->cm_lists = $this->config->item('cm_lists', 'account');
    }

    public function index() {
        if (!$this->ion_auth->logged_in()) {
            $this->session->set_userdata('redirect_to', $this->uri->uri_string());
            redirect($this->config->item('login_page'));
        } else {
            redirect($this->config->item('landing_page'));
        }
    }

    /**
     * user login
     */
    public function login() {
        $data = array();
        $data['sTitle'] = $this->aModuleInfo['meta_title'] = "User Login";
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
                   
        if ($this->ion_auth->logged_in()) {
            redirect('/');
        }

        //validate form input
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run()) { //check to see if the user is logging in
            $remember_me = $this->input->post('remember') == 1;
            if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember_me)) {
                $user_id = $this->session->userdata('user_id');
                $this->account_model->log_login();
                // backend only: the users are required to have permission to access the admin panel before they can do anything
                if ($this->environment == 'admin_panel') {
                    // list all sites that they have permisison to
                    $admin_sites = $this->permission->get_admin_sites($user_id);
                    //if (in_array('admin_area', $permissions) || in_array('super_admin', $permissions)) {
                    if (count($admin_sites) > 0) {
                        $default_site = array_shift(array_values($admin_sites));
                        $this->session->set_userdata('siteID', $default_site->id);
                        $this->session->set_userdata('siteName', $default_site->name);
                        $this->session->set_userdata('siteURL', $default_site->domain);
                        $this->session->set_userdata('sitePath', $default_site->path);
                        // check user permission
                        //$permissions = $this->permission->get_user_permissions($user_id, $default_site->id);
                        //$this->session->set_userdata('permissions', $permissions);
                    } else {
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
                    $default_landing_page = 'dashboard';
                } else {
                    // front-end only
                    // check user permission
                    //$permissions = $this->permission->get_user_permissions($user_id);
                    //$this->session->set_userdata('permissions', $permissions);
                    // load up points for user
                    //$user_points = $this->account_model->get_user_points($user_id);
                    //$this->session->set_userdata('user_points', $user_points);

                    $user_info = $this->account_model->get_user($user_id);
                    $this->session->set_userdata('user_info', $user_info);

                    //$retailer_role = $this->account_model->get_user_retailer_role($user_id);
                    //$this->session->set_userdata('retailer_role', $retailer_role->role_id);                     
                    
                    //logged in first time? has no badge? add rookie badge
                    $badges = account_get_badges($user_id);
                    if (empty($badges)) {
                        account_add_badge($user_id, 'rookie');
                    }

                    // assign site values
//          $this->session->set_userdata( 'siteName', $this->config->item('site_name'));
//          $this->session->set_userdata( 'siteURL', $this->config->item('site_domain'));
//          $this->session->set_userdata( 'sitePath', $this->config->item('site_domain'));
                    //profile for main site

                    $aHost = explode('.', $_SERVER['HTTP_HOST']);
                    //if ($aHost[0] == 'earetailprofessionals') {
                        $default_landing_page = '/';
                    //} else {
                    //    $default_landing_page = '/profile';
                    //}
                    $this->load->model("target/target_model");
                    $targets = $this->target_model->get_target_by_account($user_id);
                    if ( !empty($targets)) {
                      $this->session->set_userdata("targets", implode(",", $targets));
                      $this->load->model("quiz/quiz_model");
                      $trainings = $this->quiz_model->get_trainings_by_targets($targets);
                      if ( !empty($trainings)) {
                        $this->session->set_userdata("trainings", implode(",", $trainings));
                      }
                    }
                }

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                //redirect them back to the landing page
                $redirect_to = $this->session->userdata('redirect_to');
                
                //$redirect_to = $default_landing_page;
                if ($redirect_to > '') {
                    $redirect_to = ltrim($redirect_to, '/');
                    if ($redirect_to == 'hotcms') {
                        $redirect_to = $default_landing_page;
                    } elseif (substr($redirect_to, 0, 7) == 'hotcms/') {
                        $redirect_to = substr($redirect_to, 7);
                    }
                    $this->session->set_userdata('redirect_to', '');
                } else {
                    $redirect_to = $default_landing_page;
                }
                redirect($redirect_to);
            } else { //if the login was un-successful
                //TODO: log the attempts and check against the max attempts one can make
                //redirect them back to the login page
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                //redirect($this->config->item('login_page')); //use redirects instead of loading views for compatibility with MY_Controller libraries
                redirect('/login');
            }
        } else {  //the user is not logging in so display the login page
            //set the flash data error or notice messages if any    
            if (validation_errors() > '') {
                $data['error'] .= validation_errors();
            }

            $data['username'] = array('name' => 'username',
                'id' => 'username',
                'type' => 'text',
                'value' => $this->form_validation->set_value('username'),
                'class' => 'input-xlarge'
            );
            $data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'class' => 'input-xlarge'
            );
            //keep redirect_to in session
            $redirect_to = $this->session->userdata('redirect_to');
            $this->session->set_userdata('redirect_to', $redirect_to);
            if ($this->environment == 'admin_panel') {
                $data['leftbar'] = '';
                $data['main_area'] = $this->load->view('login', $data, TRUE);
                $this->load->view('global', $data);
            } else {
                self::loadModuleView($this->aModuleInfo, $data, 'login');
            }
        }
    }

    /**
     * log user out
     */
    public function logout() {
        //log the user out
        $this->ion_auth->logout();
        //redirect them back to the page they came from
        if ($this->aData['sTheme'] == 'cheddarLabs') {
            redirect($this->config->item('/'));
        }
        $this->session->unset_userdata("targets");
        $this->session->unset_userdata("trainings");
        redirect($this->config->item('login_page'));
    }

    /**
     * Confirm register displaying method
     * @access public
     * @return void
     */
    public function confirm_register() {
        $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Sign up";
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error'] = $this->session->flashdata('error');
        $this->data['account_detail'] = $this->session->flashdata('account_detail');
        // load module view
        self::loadModuleView($this->aModuleInfo, $this->data, 'register_success');
    }
    
    //get email
    public function get_email() {
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

        $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Change Email";
        //set the flash data error or notice messages if any
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error'] = $this->session->flashdata('error');

        $this->form_validation->set_rules('email', 'New Email', 'required|valid_email|callback__email_check');
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
            } else {
                //$this->session->set_flashdata('error', $this->ion_auth->errors());
                //redirect('/my-account/change-password');
                $this->data['error'] = $this->ion_auth->errors();
            }
        } else {
            //set the validation error if any
            if (validation_errors() > '') {
                $this->data['error'] = validation_errors();
            }
        }

        //display the form
        $this->data['email'] = array('name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
        );
        $this->data['email_confirm'] = array('name' => 'email_confirm',
            'id' => 'email_confirm',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email_confirm'),
        );
        // load module view
        self::loadModuleView($this->aModuleInfo, $this->data, 'change_password');
        //$this->load->view('change_email', $this->data);
    }

    //verify a new email address
    public function verify_email($id, $code = false) {
        $activated = $this->ion_auth->change_email($id, $code);


        if ($changed) { //if the password was successfully changed
            //$this->session->set_flashdata('message', $this->ion_auth->messages());
            //redirect('my-account/switch-phone');
            //$this->logout();
            $this->data['message'] = 'Password changed successfully.';
            //$this->data['link'] = '<a class="triangle" href="#" onclick="return switchPhone();">Select Number</a>';
            //$this->load->view('plain_message', $this->data);
            self::loadModuleView($this->aModuleInfo, $this->data, 'plain_message');
            return true;
        } else {
            //$this->session->set_flashdata('error', $this->ion_auth->errors());
            //redirect('/my-account/change-password');
            $this->data['error'] = $this->ion_auth->errors();
        }
        /*
          } else {
          //set the validation error if any
          if (validation_errors() > '') {
          $this->data['error'] = validation_errors();
          }

          if ($activated) {
          //redirect them to the login page
          $this->session->set_flashdata('message', 'Your email has been changed successfully.');
          }
          else {
          //redirect them to the register page
          $this->session->set_flashdata('error', 'Failed to change the email');
          }
          redirect("my-account");
         */
    }

    //change password
    public function change_password() {
        if (!$this->ion_auth->logged_in()) {
            redirect($this->config->item('login_page'));
        }

        $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Change Password";
        //set the flash data error or notice messages if any
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error'] = $this->session->flashdata('error');

        $this->form_validation->set_rules('password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']');
        $this->form_validation->set_rules('new_password_confirm', 'Confirm New Password', 'required|matches[new_password]');

        $user = $this->ion_auth->get_user($this->session->userdata('user_id'));

        if ($this->form_validation->run()) {
            $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

            $changed = $this->ion_auth->change_password($identity, $this->input->post('password'), $this->input->post('new_password'));

            if ($changed) { //if the password was successfully changed
                //$this->session->set_flashdata('message', $this->ion_auth->messages());
                //redirect('my-account/switch-phone');
                //$this->logout();
                $this->data['message'] = '<b>Password changed successfully.</b>';
                //$this->load->view('plain_message', $this->data);
                self::loadModuleView($this->aModuleInfo, $this->data, 'change_password');
                return true;
            } else {
                //$this->session->set_flashdata('error', $this->ion_auth->errors());
                //redirect('/my-account/change-password');
                $this->data['error'] = $this->ion_auth->errors();
            }
        } else {
            //set the validation error if any
            if (validation_errors() > '') {
                $this->data['error'] = validation_errors();
            }
        }

        //display the form
        $this->data['password'] = array('name' => 'password',
            'id' => 'password',
            'type' => 'password',
        );
        $this->data['new_password'] = array('name' => 'new_password',
            'id' => 'new_password',
            'type' => 'password',
        );
        $this->data['new_password_confirm'] = array('name' => 'new_password_confirm',
            'id' => 'new_password_confirm',
            'type' => 'password',
        );
        $this->data['user_id'] = array('name' => 'user_id',
            'id' => 'user_id',
            'type' => 'hidden',
            'value' => $user->id,
        );
        // load module view
        self::loadModuleView($this->aModuleInfo, $this->data, 'change_password');
        //$this->load->view('change_password', $this->data);
    }

    //forgot password
    public function forgot_password() {
        $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Forgot Password";
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error'] = $this->session->flashdata('error');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        if ($this->form_validation->run()) {
            //run the forgotten password method to email an activation code to the user
            $mailsent = $this->ion_auth->forgotten_password($this->input->post('email'));

            if ($mailsent) { //if there were no errors
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("login"); //we should display a confirmation page here instead of the login page
            } else {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect("forgot-password");
            }
        } else {
            //set any errors and display the form
            if (validation_errors() > '') {
                $this->data['error'] = validation_errors();
            }
            //setup the input
            $this->data['email'] = array('name' => 'email',
                'id' => 'email',
            );
            // load module view
            self::loadModuleView($this->aModuleInfo, $this->data, 'forgot_password');
        }
    }

    //reset password - final step for forgotten password
    public function reset_password($code) {
        $reset = $this->ion_auth->forgotten_password_complete($code);

        if ($reset) {  //if the reset worked then send them to the login page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("login");
        } else { //if the reset didnt work then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("forgot-password");
        }
    }

    //activate a user
    public function activate($id, $code = false) {
        $activation = $this->ion_auth->activate($id, $code);

        if ($activation) {
            //redirect them to the login page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("login");
        } else {
            //redirect them to the register page
            $this->session->set_flashdata('error', $this->ion_auth->errors());
            redirect("register");
        }
    }

    //deactivate a user
    private function deactivate($id = NULL) {
        $this->data['sTitle'] = $this->aModuleInfo['meta_title'] = "Deactivate User";
        // no funny business, force to integer
        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', 'confirmation', 'required');
        $this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

        if ($this->form_validation->run() == FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->get_user($id);
            // load module view
            self::loadModuleView($this->aModuleInfo, $this->data, 'deactivate_user');
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    redirect('page-not-found');
                    show_404();
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('login', 'refresh');
        }
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

    // email check
    public function _email_check($email) {
        if ($this->ion_auth->email_check($email)) {
            $this->form_validation->set_message('_email_check', $this->lang->line('user_error_email'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function _validate_terms() {
        $isValid = $this->input->post('terms');
        // if terms not checked / accepted
        if (!$isValid) {
            // assign error message
            $this->form_validation->set_message('_validate_terms', 'You must read and accept the privacy policy and website terms and conditions of use.');
        }
        return $isValid;
    }

    private function _get_csrf_nonce() {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    private function _valid_csrf_nonce() {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
                $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function profile() {

        $this->data['message'] = $this->session->flashdata('message');
        

        $this->data['title'] = "User Profile";

        $userid = $this->session->userdata('user_id');
        if ($userid < 1) {
            //redirect them to the login page
            redirect($this->config->item('login_page'));
        } else {

            $user = $this->account_model->get_user($userid);
            $this->data['user'] = $user;
            $user_default_contact = $this->account_model->get_user_default_contact($userid);
            $this->data['user_default_contact'] = $user_default_contact;
            $user_points_current = $this->account_model->get_user_points($userid);
            $user_points_lifetime = $this->account_model->get_user_points($userid, 'lifetime');

            $user_points_ea = $this->account_model->get_user_points($userid, 'ea');

            $this->data['user_points']['current'] = $user_points_current;
            $this->data['user_points']['lifetime'] = $user_points_lifetime;
            $this->data['user_points']['ea'] = $user_points_ea;

            $user_draws = $this->account_model->get_user_draws($userid);
            $this->data['user_draws']['current'] = $user_draws;
            $user_draws_lifetime = $this->account_model->get_user_draws($userid, 'lifetime');
            $this->data['user_draws']['lifetime'] = $user_draws_lifetime;
          
            $orders = $this->account_model->get_user_orders($userid);
            $this->data['orders'] = $orders;
            $args_leaderboard['widget_type'] = 'home';
            $args_leaderboard['title'] = 'Leaderboard';
            $leaderboard = widget::run('user/user_leaderboard_widget', $args_leaderboard);
            $this->data['leaderboard_widget'] = $leaderboard;
            $args['title'] = 'Activity feed';
            $activity_feed = widget::run('user/user_activity_widget', $args);
            $this->data['activity_feed'] = $activity_feed;

            //load badges
            $badges = account_get_badges($userid);
            $this->data['badges'] = $badges;

            //# of quizzes
            $this->data['quiz_number'] = quiz_get_number_of_user_quizzes($userid);
             
            //verification
            $verifications = $this->verification_model->verification_load_by_user_id($userid); 
            $this->load->model('retailer/retailer_model');
            if(!empty($verifications)){
                
                foreach($verifications as $verification){
                    //load asset img
                    $verification->image = asset_load_item($verification->asset_id);
                    //load retailer info
                    $verification->retailer_name = $this->retailer_model->retailer_load($verification->retailer_id, FALSE)->name;
                }
                
                $this->data['verifications'] = $verifications;
            }else{
                $this->data['verifications'] = '';
            }
            //retailers
            $args['title'] = 'Retailers';
            $args['user_id'] = $userid;
            $retailers_info = widget::run('retailer/retailer_user_widget', $args);
            
            //load sites (brands)
            $sites = account_get_sites($userid,false);
            $this->data['sites'] = $sites;         
//            $this->data['trainings'] = $this->session->userdata("trainings");
//            $this->data['targets'] = $this->session->userdata("targets");

            $this->data['retailers_info'] = $retailers_info;     
            
            //load ref colleague widget
            $args_refer = array();
            if(!empty($_POST)){
              $args_refer['postback']= $_POST;
            }
            $refer_colleague = widget::run('refer_colleague/refer_colleague_form_widget', $args_refer);
            $this->data['refer_colleague_widget'] = $refer_colleague; 
            
            //refer colleague history
            $args['title'] = 'Refer Colleague History';
            $refer_colleague_history = widget::run('refer_colleague/refer_colleague_history_widget', $args);            
            $this->data['refer_colleague_history'] = $refer_colleague_history; 
            
            //reports info
            if (!has_permission('view_report')) {
              $this->data['retailers_reports'] = '';
            }else{
              $admin_site_id = $this->account_model->get_admin_site($this->session->userdata("user_id"));
              $this->data['retailers_reports'] = '';
              if ($admin_site_id > 0) {
                $admin_sites = array();
                $this->load->model("site/site_model");
                if (((int) $admin_site_id) === 1) {
                  $all_sites = $this->site_model->get_all_sites();
                  foreach ($all_sites as $a_site) {
                    if ($a_site->id > 1) {
                      $admin_sites[$a_site->id] = $this->site_model->get_site_by_id($a_site->id);
                    }
                  }
                } elseif (((int) $admin_site_id) > 1) {
                  $admin_sites[$admin_site_id] = $this->site_model->get_site_by_id($admin_site_id);
                }
                $this->data['retailers_reports'] .= '<div class="hero-unit">';
                if (count($admin_sites) == 1) {
                  $this->data['retailers_reports'] .= "<h2>Report</h2>";
                } else {
                  $this->data['retailers_reports'] .= "<h2>Reports</h2>";
                }
                foreach ($admin_sites as $admin_site) {
                  $this->data['retailers_reports'] .= "<p>";
                  $this->data['retailers_reports'] .= '<a href="report/site--'.$admin_site->id.'">'.$admin_site->name."</a>";
                  $this->data['retailers_reports'] .= "</p>";
                }
                $this->data['retailers_reports'] .= "</div>";
              }
            }
            $certificates = $this->account_model->get_user_certificates($userid);
            $this->data['certificates'] = $certificates;

                    //$this->session->set_userdata('permissions', $permissions);            
            
            self::loadModuleView($this->aModuleInfo, $this->data, 'profile');
//      $this->load->view('profile', $this->data);
        }
    }

    public function public_profile($screenname) {

        $user = $this->account_model->get_user_public_data(urldecode($screenname));
        if (empty($user)) {
            redirect('page-not-found');
            show_404();
        }
        $this->data['user'] = $user;

        $user_points_current = $this->account_model->get_user_points($user->user_id);

        $user_points_lifetime = $this->account_model->get_user_points($user->user_id, 'lifetime');
        $this->data['user_points']['current'] = $user_points_current;
        $this->data['user_points']['lifetime'] = $user_points_lifetime;
        
        $user_draws = $this->account_model->get_user_draws($user->user_id);
        $this->data['user_draws']['current'] = $user_draws;

        $args['title'] = 'User\'s Activity feed';
        $args['user_info'] = $user;
        $activity_feed = widget::run('user/user_activity_widget', $args);
        $this->data['activity_feed'] = $activity_feed;
        
        //load badges
        $badges = account_get_badges($user->user_id);
        $this->data['badges'] = $badges;
        
        //# of quizzes
        $this->data['quiz_number'] = quiz_get_number_of_user_quizzes($user->user_id);
        
        //retailers
        $args['title'] = 'Retailers';
        $args['user_id'] = $user->user_id;
        $retailers_info = widget::run('retailer/retailer_user_widget', $args);

        // Accessable brand list
        $this->data['sites'] = account_get_sites($user->user_id, TRUE);

        $this->data['retailers_info'] = $retailers_info;       
        
        $this->aModuleInfo['meta_title'] =  sprintf("%s's Public Profile ",  ucfirst($user->screen_name));

        self::loadModuleView($this->aModuleInfo, $this->data, 'profile_public');
    }

    public function edit_profile() {
        $this->load->model('contact/model_contact');
        $this->data['message'] = $this->session->flashdata('message');
        $this->data['error'] = $this->session->flashdata('error');
        $this->data['title'] = "Edit user profile";
        $this->aModuleInfo['javascript'] .= ' account_edit.js';
        if ($this->ion_auth->logged_in()) {
            $userid = $this->session->userdata('user_id');
            $user = $this->account_model->get_user($userid);
            $orig_retailer_id = $user->retailer_id;
            $orig_screenname = $user->screen_name;
            if ($this->input->post() && $this->input->post('profile_edit')) {
                $user->first_name = $this->input->post('first_name');
                $user->last_name = $this->input->post('last_name');
                $user->screen_name = $this->input->post('screen_name');
                $user->country_code = $this->input->post('country_code');
                $user->province_code = $this->input->post('province');
                $user->retailer_id = $this->input->post('retailer');
                $user->store_id = $this->input->post('store');
                $user->employment = $this->input->post('employment');
                $user->job_title = $this->input->post('job_title');
                $user->hire_date = $this->input->post('hire_date');
                $user->newsletter_monthly = $this->input->post('newsletter_monthly');
                $user->newsletter_newlab = $this->input->post('newsletter_newlab');
                $user->newsletter_newswag = $this->input->post('newsletter_newswag');
                $user->newsletter_survey = $this->input->post('newsletter_survey');
            }
            if ($this->input->post('profile_edit') || $this->input->get('profile_edit')) {
                $this->form_validation->set_rules('first_name', 'First Name', 'required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'required');
                
               
                if($user->screen_name !=$orig_screenname){
                  $this->form_validation->set_rules('screen_name', 'Screen Name', 'alpha_dash|required|callback__screen_check|is_unique[user_profile.screen_name]');
                }else{
                    $this->form_validation->set_rules('screen_name', 'Screen Name', 'alpha_dash|required|callback__screen_check');
                }
                if ($this->form_validation->run() == FALSE) {
                    if (validation_errors() > '') {
                        $this->data['error'] = validation_errors();
                    }

                    $this->data['user'] = $user;

                    $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

                    // load dropdown options
                    $this->data['country_options'] = array('' => 'Please select country' ,'US' => 'USA', 'CA' => 'Canada');
                    $this->data['selected_country']= $user->country_code;
                    $this->data['retailers'] = array('' => '');
                    $retailers = account_retailers($user->country_code);
                    foreach ($retailers as $v) {
                        $this->data['retailers'][$v->id] = $v->name;
                    }

                    $this->data['provinces'] = array('' => '');
                    $provinces = account_provinces($user->country_code);
                    foreach ($provinces as $v) {
                        $this->data['provinces'][$v->province_code] = $v->province_name;
                    }

                    $this->data['retailers'][99999] = 'Other';
                    $this->data['stores'] = array('' => '');
                    $stores = account_stores($user->retailer_id);
                    foreach ($stores as $v) {
                        $this->data['stores'][$v->id] = $v->store_name . ' (' . $v->store_num . ')';
                    }
                    $this->data['stores'][99999] = 'Other';
                    $this->data['employments'] = array('' => '') + account_employments();
                    $this->data['job_titles'] = array('' => '') + account_job_titles();

//        $this->data['selected_country'] = $country_code;
//        $this->data['selected_retailer'] = $selected_retailer;
//        $this->data['selected_store'] = $selected_store;
//        $this->data['selected_employment'] = $selected_employment;
//        $this->data['selected_job_title'] = $selected_job_title;
//        $this->data['selected_newsletter_monthly'] = $this->input->post('newsletter_monthly');
//        $this->data['selected_newsletter_newlab'] = $this->input->post('newsletter_newlab');
//        $this->data['selected_newsletter_newswag'] = $this->input->post('newsletter_newswag');
//        $this->data['selected_newsletter_survey'] = $this->input->post('newsletter_survey');

                    $this->data['first_name'] = array('name' => 'first_name',
                        'id' => 'first_name',
                        'type' => 'text',
                        'value' => $user->first_name,
                    );
                    $this->data['last_name'] = array('name' => 'last_name',
                        'id' => 'last_name',
                        'type' => 'text',
                        'value' => $user->last_name,
                    );
                    $this->data['screen_name'] = array('name' => 'screen_name',
                        'id' => 'screen_name',
                        'type' => 'text',
                        'value' => $user->screen_name,
                    );
                    $this->data['hire_date'] = array('name' => 'hire_date',
                        'id' => 'hire_date',
                        'type' => 'text',
                        'value' => $user->hire_date,
                    );
                    //$this->data['form_contacts'] = modules::run('contact/controller/get_edit_forms', 'user', $userid);

                    self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
                } else {
                    //verifiried user changed retailer id?
                    $user = $this->account_model->get_user($userid);
                    
                if($user->verified == 1){
                    //retailer id change -> unverify user
                    $message = '';
                    if ($this->input->post('retailer') != $orig_retailer_id){
                        
                        $this->verification_model->unverify_user($userid);
                        $message = '<b>Profile was unverified.</b><br />';
                        //send email to user
                        $this->load->model('retailer/retailer_model');

                        $old_retailer = $this->retailer_model->retailer_load($orig_retailer_id);
                        $new_retailer = $this->retailer_model->retailer_load($this->input->post('retailer'));
                        
                        

                        $this->account_model->email_unverify($user,$old_retailer, $new_retailer);

                    }
                }
                    $this->account_model->update_user($userid);
                    $this->session->set_flashdata('message',$message.'<strong>Your profile was updated.</strong>');
                    redirect('/profile');
                    //self::loadModuleView($this->aModuleInfo, $this->data, 'profile');
                }
            } else {
                $this->data['user'] = $user;

                $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

                // load dropdown options
                    $this->data['country_options'] = array('' => 'Please select country' ,'US' => 'USA', 'CA' => 'Canada');
                    $this->data['selected_country']= $user->country_code;                

                $retailers = account_retailers($user->country_code);
                $retailer_verified = FALSE;
                foreach ($retailers as $r) {
                    if ($r->id == $user->retailer_id) {
                        $retailer_verified = TRUE;
                    }
                }
                if ($retailer_verified) {
                    //$this->data['retailers'] = array('' => '');
                    foreach ($retailers as $v) {
                        $this->data['retailers'][$v->id] = $v->name;
                    }
                    $this->data['retailers'][99999] = 'Other';
                } else {
                    $this->data['retailers'][$user->retailer_id] = 'Pending retailer';
                }

                $this->data['provinces'] = array('' => '');
                $provinces = account_provinces($user->country_code);
                foreach ($provinces as $v) {
                    $this->data['provinces'][$v->province_code] = $v->province_name;
                }
                $stores = account_stores($user->retailer_id, $user->province_code);
                $store_verified = FALSE;
                foreach ($stores as $s) {
                    if ($s->id == $user->store_id) {
                        $store_verified = TRUE;
                    }
                }
                if ($store_verified) {
                    //$this->data['stores'] = array('' => '');
                    foreach ($stores as $v) {
                        $this->data['stores'][$v->id] = $v->store_name . ' (' . $v->store_num . ')';
                        $this->data['stores'][99999] = 'Other';
                    }
                } else {
                    $this->data['stores'][$user->store_id] = 'Pending location';
                }
//                $employments = account_employments();
//                if (in_array($user->employment, $employments)) {
                    $this->data['employments'] = array('' => '') + account_employments();
//                } else {
//                    $this->data['employments'] = array($user->employment => $user->employment);
//                }
//                $job_titles = account_job_titles();
//                if (in_array($user->job_title, $job_titles)) {
                    $this->data['job_titles'] = array('' => '') + account_job_titles();
//                } else {
//                    $this->data['job_titles'] = array($user->job_title => $user->job_title);
//                }
                $this->data['first_name'] = array('name' => 'first_name',
                    'id' => 'first_name',
                    'type' => 'text',
                    'value' => $user->first_name,
                );
                $this->data['last_name'] = array('name' => 'last_name',
                    'id' => 'last_name',
                    'type' => 'text',
                    'value' => $user->last_name,
                );
                $this->data['screen_name'] = array('name' => 'screen_name',
                    'id' => 'screen_name',
                    'type' => 'text',
                    'value' => $user->screen_name,
                );
                $this->data['hire_date'] = array('name' => 'hire_date',
                    'id' => 'hire_date',
                    'type' => 'text',
                    'value' => $user->hire_date,
                );

                self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
            }
        } else {
            redirect($this->config->item('login_page'));
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
                'default' => $this->input->post('default_address'),
            );
            $this->model_contact->update_single($item_id, $contact_data_array);
            //$this->edit_profile();
            redirect('profile-update');
        } else {
            $user = $this->account_model->get_user($userid);

            $this->data['user'] = $user;

            $this->data['contacts'] = $this->model_contact->get_contact_by_connection('user', $userid);

            $this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));

            //self::loadModuleView($this->aModuleInfo, $this->data, 'profile_edit');
            $this->edit_profile();
            redirect('profile-update');
        }
    }

    /**
     * Set validation rules
     */
    private function _validate_contact_name() {
        // assign validation rules
        $this->form_validation->set_rules('contact_name', strtolower(lang('hotcms_contact_type')), 'trim|required');
    }

    /**
     * Function to add new contact to user
     *
     * @param id of user
     */
    public function add_new_contact($user_id) {

        // TODO: after adding new contact, keep the Contact tab open
        $contact_name = $this->input->post('contact_name');

        $this->load->model('contact/model_contact');

        $this->_validate_contact_name();

        if ($this->form_validation->run()) {
            $this->model_contact->insert($user_id, 'user', $contact_name);

            $message = array();
            $message['type'] = 'confirm';
            $message['value'] = $this->lang->line('hotcms_updated_item');
            $this->edit_profile();
        } else {
            $message = array();
            $message['type'] = 'error';
            $message['value'] = validation_errors();
            $this->edit_profile();
        }
    }

    /**
     * Function for delete contact for user
     *
     * @param id of user
     */
    public function delete_contact($contact_id) {


        $this->load->model('contact/model_contact');


        $this->model_contact->delete_by_id($contact_id);

        $message = array();
        $message['type'] = 'confirm';
        $message['value'] = $this->lang->line('hotcms_updated_item');
        $this->edit_profile();
    }

    /**
     * order_detail($order_id) 
     * get user's order details
     * 
     * @param int order_id
     * @retun object
     */
    public function order_detail($order_id) {
        //load order detils with all items
        $this->load->model('order/order_model');
        $order = $this->order_model->order_load($order_id, FALSE);
        $orderItems = $this->order_model->order_load_items($order_id, FALSE);
        $order->items = $orderItems;
        $this->data['orderDetails'] = $order;
        self::loadModuleView($this->aModuleInfo, $this->data, 'profile_order_details');
    }
    
    /**
     * order_detail($order_id) 
     * get user's order details
     * 
     * @param int order_id
     * @retun object
     */
    public function refer_colleague_history() {
        
        //load ref colleague widget
        if(!empty($_POST)){
          $args_refer['postback']= $_POST;
        }
        $refer_colleague = widget::run('refer_colleague/refer_colleague_form_widget', $args_refer);
        $this->data['refer_colleague_widget'] = $refer_colleague; 

        //refer colleague history
        $args['title'] = 'Refer Colleague History';
        $refer_colleague_history = widget::run('refer_colleague/refer_colleague_history_widget', $args);            
        $this->data['refer_colleague_history'] = $refer_colleague_history; 
        

        self::loadModuleView($this->aModuleInfo, $this->data, 'profile_refer_history');
    }    
    
    public function newsletter_update(){
        
        $userid = $this->session->userdata('user_id');
        if ($userid < 1) {
            //redirect them to the login page
            redirect($this->config->item('login_page'));
        } else {
            $user = $this->account_model->get_user($userid);
        }
        //capmaign monitor subscriptions
        $monthly_list_id = $this->cm_lists['monthly'];
        $new_swag_list_id = $this->cm_lists['swag'];
        $new_labs_list_id = $this->cm_lists['labs'];
        $survey_list_id = $this->cm_lists['survey'];
        
        $subsciber = array('EmailAddress' => $user->email, 'Resubscribe' => true);
        $subsciber_un = array('EmailAddress' => $user->email);
        
        $monthly = $this->input->post('newsletter-monthly');
$msg = '';
        if($monthly === 'on'){
            $result = $this->cmonitor->post_request('subscribers/'.$monthly_list_id.'.json', $subsciber); 
            if($result->was_successful()){
                $msg .= 'Monthly Newsletters - Subscribed. <br />';
            }
        }else{
            $result = $this->cmonitor->post_request('subscribers/'.$monthly_list_id.'/unsubscribe.json', $subsciber_un);
            if($result->was_successful()){
                $msg .= 'Monthly Newsletters - Unsubscribed. <br />';
            }     
        }
        
        $swag = $this->input->post('newsletter-new-swag');
        if($swag  === 'on'){
            $result = $this->cmonitor->post_request('subscribers/'.$new_swag_list_id.'.json', $subsciber); 
            if($result->was_successful()){
                $msg .= 'Alerts about new SWAG - Subscribed. <br />';
            }            
        }else{
            $result = $this->cmonitor->post_request('subscribers/'.$new_swag_list_id.'/unsubscribe.json', $subsciber_un);
            if($result->was_successful()){
                $msg .= 'Alerts about new SWAG - Unsubscribed. <br />';
            }  
        }  
 
        $lab = $this->input->post('newsletter-new-lab');
        if($lab  === 'on'){
            $result = $this->cmonitor->post_request('subscribers/'.$new_labs_list_id.'.json', $subsciber); 
            if($result->was_successful()){
                $msg .= 'Alerts about new Labs - Subscribed. <br />';
            }              
        }else{
            $result = $this->cmonitor->post_request('subscribers/'.$new_labs_list_id.'/unsubscribe.json', $subsciber_un);
            if($result->was_successful()){
                $msg .= 'Alerts about new Labs - Unsubscribed. <br />';
            }               
        }  
        
        $survey = $this->input->post('newsletter-survey');
        if($survey  === 'on'){
            $result = $this->cmonitor->post_request('subscribers/'.$survey_list_id.'.json', $subsciber); 
            if($result->was_successful()){
                $msg .= 'Survey Invitations - Subscribed. <br />';
            }               
        }else{
            $result = $this->cmonitor->post_request('subscribers/'.$survey_list_id.'/unsubscribe.json', $subsciber_un);
            if($result->was_successful()){
                $msg .= 'Survey Invitations - Unsubscribed. <br />';
            }            
        }     

        $this->session->set_flashdata('message',$msg);
        
        redirect('/profile/communication-preferences');
    }    
    
    
/// PATCHes functions 
    public function retailer_store_fix(){
        
        //get all duplicated shop id's
        $shops = $this->account_model->get_shops();
        
        $same_shops = array();
        $i = 0;
        foreach($shops as $s1){
                $i++;            
            foreach($shops as $s2){
                if(($s1->id != $s2->id) &&($s1->retailer_id == $s2->retailer_id) && ($s1->store_name == $s2->store_name) && ($s1->store_num == $s2->store_num)&& ($s1->province == $s2->province)){
                    
                    if(!array_key_exists($s2->id, $same_shops)){
                    $same_shops[$s1->id] = (int) $s2->id;
  //                  $same_shops[$s1->id] = (int) $s2->id;
                    }
                }
            }
//            if(!empty($same_shops)){
//335
//            $res[$i] = $same_shops;
            //}
        }
        
//        var_dump($same_shops);
//       die();
        $users = '';
        foreach($same_shops as $k => $v){
            //get affected usernames

            foreach($this->account_model->get_users($k) as $u){

              $users .= $u->username .', ';
            }
            //update user with $v store id to $k
            $this->account_model->update_shop_id($k, $v);
            
            //delete $v store
            $this->account_model->delete_shop_id($k);
        }
        //var_dump($users);
        return $users;
    }
    //addind memeber user role imported ea users
    public function ea_user_role_fix(){
      $query = $this->db->select(' u.id')
       ->where('ip_address','1.1.1.1')
      ->get('user u');
      
      foreach ($query->result() as $user){
          //var_dump($user->id);
          $this->db->set('role_id', 9);
          $this->db->set('user_id', $user->id);
          $this->db->insert('user_role');  
      } 
    }
    
    //addind memeber user role imported ea users
    public function removepointsfromnonbestbuyusers(){
      $query = $this->db->select('*')
       ->where('retailer_id !=','1')
      ->get('user_profile u');
      
      foreach ($query->result() as $user){
          var_dump($user->user_id);
          
          $query1 = $this->db->set('points', 0)
          ->where('user_id', $user->user_id)
          ->update('user_profile');  
          var_dump($this->db->last_query());

          $query2 = $this->db->where('user_id', $user->user_id)
          ->delete('user_points');  
          var_dump($this->db->last_query());

      } 
    }    
    
    public function fix_screen_name(){
      $query = $this->db->select('id,user_id,screen_name,points,draws')
       ->like('screen_name', '_@2')
       ->get('user_profile u');
      
            foreach ($query->result() as $user){
          //var_dump($user->user_id);
          //var_dump($user->screen_name);
          //var_dump($user->points);
          $org_screenname = substr($user->screen_name, 0, -3);
          //var_dump($org_screenname);
          
           $query = $this->db->select('id,user_id,screen_name,points,draws')
       ->where('screen_name', $org_screenname)
       ->get('user_profile u');
         $user2 = $query->row();
         //var_dump($user2);
         //if ($user->points != $user2->points){
             var_dump($user);
             echo '<br>';
             var_dump($user2);
             $scren_name = $user2->screen_name;
             
             $query = $this->db->where('id', $user2->id)
             ->delete('user_profile');   
             
             $query = $this->db->set('screen_name',$scren_name)
                     ->where('id', $user->id)
             ->update('user_profile');
             
             
             //$user2_table = $query2->row();   
             //var_dump($user2_table);
             echo '<br>';
             echo '<br>';
         //}
          //die();
            }
    }
    
    public function add_swagger(){
        die('aaaaa');   
//load all orders        
            $this->load->helper('badge/badge');
    
        
       $query = $this->db->select('id,user_id,create_timestamp')
       ->get('order');
       foreach ($query->result() as $order){
           $data = array();
           var_dump($order);
           $has_swagger = check_user_badge($order->user_id,'swagger');
           var_dump($has_swagger);
           
          if(!$has_swagger){
                $data = array(
                    'user_id' => $order->user_id,
                    'draws' => 10,
                    'point_type' => 'badge',
                    'ref_table' => 'badge',
                    'ref_id' => '2',
                    'create_timestamp' => $order->create_timestamp
                );                
                
                $data['description'] = 'placed first Swag order and received the Swagger Badge, plus 10 contest entries!';
                $this->db->insert('user_draws', $data);
          }
           echo '<br/>';
           
           
       }
        die('a');
    }
    
    public function remove_points(){
        die('old');
       $query = $this->db->select('id,points,user_id')
               ->where('point_type !=','order')
               ->where('points !=','0')
                ->get('user_points');

       foreach ($query->result() as $points_row){
           
           $ts = time();
           $data = array();

                $data = array(
                    'points_reversed' => -$points_row->points,
                    'reverse_timestamp' => $ts
                );                                
                var_dump($points_row);
                var_dump($data);
                $this->db->where('id', $points_row->id);   
                $this->db->update('user_points', $data);
                $this->_sync_user_points($points_row->user_id);
       
var_dump($this->db->last_query());
                echo '<br/>';
          }
           echo '<br/>';
           
           
       }
       
    public function points_fix(){
       //die('a'); 
       $query = $this->db->select('id,points,user_id')
               //->where('point_type !=','order')
               ->where('points !=','0')
                ->get('user_profile');

       foreach ($query->result() as $profile_row){
           
           //find EA transfer line
                  $query2 = $this->db->select('id')
               ->where('user_id =', $profile_row->user_id)
                          ->where('point_type =','EA')
                ->get('user_points');
           $row_id = $query2->row();
           if (empty($row_id)){
               echo '<br/>';
               $profile_row->user_id;
               echo '<br/>';
           }else{
           $ts = time();
           $data = array();

                $data = array(
                    'points_reversed' => -$profile_row->points,
                    'reverse_timestamp' => $ts
                );       
                
                var_dump($profile_row);
                var_dump($data);
                $this->db->where('id', $row_id->id);   
                $this->db->update('user_points', $data);
                $this->_sync_user_points($profile_row->user_id);
       

                echo '<br/>';
          }
           echo '<br/>';
       }
                      
       }       
   
  /**
   * Recalculate user points and sync with the user's profile
   * @param  int  $user_id
   * @return bool
   */
  private function _sync_user_points($user_id)
  {
    $query = $this->db->select('SUM(points) AS total, SUM(points_reversed) AS reversed')
      ->where('user_id', $user_id)
      ->get('user_points');
    $row = $query->row();
    $points = $row->total + $row->reversed;
    $this->db->set('points', $points);
    $this->db->where('user_id', $user_id);
    $result = $this->db->update('user_profile');

    return $result;
  }       
  
    public function fix_quiz_points(){
         
//      $query = $this->db->select('r.*')        
//        ->get('retailer r');             
//       foreach($query->result() as $retailer){
//           //var_dump($retailer->name);
//            //$this->db->set('slug', strtolower(url_title($retailer->name)));
//            //$this->db->where('id', $retailer->id);
//            //$this->db->update('retailer');
//          //create contact for each retailer
//          //  $this->db->set('connection_id', $retailer->id);
//          //  $this->db->set('connection_name', 'organization');
//          //  $this->db->set('name', 'Main Office');
//          //  $this->db->insert('contact');
//       }
      $query = $this->db->select('s.*')        
        ->get('retailer_store s');             
       foreach($query->result() as $store){
           //var_dump($store->store_name);
            //$this->db->set('store_name', trim($store->store_name));
//            $this->db->set('slug', strtolower(url_title($store->store_name)));
  //          $this->db->where('id', $store->id);
    //        $this->db->update('retailer_store');
          //create contact for each retailer
          //  $this->db->set('connection_id', $retailer->id);
          //  $this->db->set('connection_name', 'organization');
          //  $this->db->set('name', 'Main Office');
          //  $this->db->insert('contact');
       }        
      
       
        die('b');
       $query = $this->db->select('*')
               ->where('points >','0')
               ->where('point_type', 'quiz')
                ->get('user_points');
var_dump($this->db->last_query());
//die();
       foreach ($query->result() as $points_row){
           var_dump($points_row->user_id);
           echo '<br/>';
        /*   echo '<br/>';
                  $query2 = $this->db->select('*')
               ->where('user_id =', $profile_row->user_id)
                ->get('user_points');
                  var_dump($this->db->last_query());
                  $order_sum = 0;
                  $points_EA = 0;
       foreach ($query2->result() as $points_row){
           
           if($points_row->point_type == 'order'){
               $order_sum = $order_sum + $points_row->points;
           }
           if($points_row->point_type == 'EA'){
               $points_EA = $points_EA + $points_row->points;
           }
           var_dump($points_row);
           echo '<br/>';
           echo '<br/>';
           echo '<br/>';
       }
       //var_dump($points_EA, $order_sum);
       
       $real_reserved = ($points_EA + $order_sum);
                         $query3 = $this->db->select('*')
                                 ->where('point_type =','EA')
               ->where('user_id =', $profile_row->user_id)
                ->get('user_points');
                         
                $ea_points_row = $query3->row();
        */                 
           $ts = time();
           $data = array();

                $data = array(
                    'points_reversed' => -$points_row->points,
                    'reverse_timestamp' => $ts
                );   
                $this->db->where('id', $points_row->id);   
                $this->db->update('user_points', $data);
                $this->_sync_user_points($points_row->user_id);
                
        //var_dump($ea_points_row);
           //die();
//var_dump($this->db->last_query());
                echo '<br/>';
          }
           echo '<br/>';
           
           
       } 
       
       public function gamestop_import(){
die('aa');
           //$file = file_get_contents('/stores-gamestop.txt');
           $fh = fopen('http://www.cheddarlabs.com/stores-gamestop.txt','r');
           //var_dump($fh);
            while ($line = fgets($fh)) {
               if(strpos($line,'||',2)){
                   $store_info = explode('>>', $line);
                   //var_dump($store_info);
                   echo('</br>');
                   $store_number = substr($store_info[1],strpos($store_info[1],'#')+1,4);
                   echo($store_number);
                   echo('</br>');
                   $a_store_name = explode('#',$store_info[1]);
                   $store_name_tmp = substr($a_store_name[1], 8);
                   //var_dump($store_name_tmp);
                   $a_store_name = explode(' (',$store_name_tmp);
                   $store_name = $a_store_name[0];
                   echo($store_name);
                   echo('</br>');
                   //address
                   $a_address = explode('---',$store_info[2]);
                   if(count($a_address)==4){
                    $street_1 = trim($a_address[0]);
                    echo $street_1;
                    echo('</br>');
                    if(trim($a_address[1])!='.'){
                    $street_2 = trim($a_address[1]);
                    }else{
                    $street_2 = '';}
                    echo $street_2;
                    echo('</br>');
                    $city_state_postal = explode(',',$a_address[2]);
                    $city = $city_state_postal[0];
                    echo $city;
                    echo('</br>');
                    $state_postal = explode(' ',$city_state_postal[1]);
                    //TODO get state shotcut
                    $state = $state_postal[1];
                    echo $state;
                    echo('</br>');
                    $postal = $state_postal[2];
                    echo $postal;
                    echo('</br>');
                   }else{
                    $street_1 = trim($a_address[0]);
                    echo $street_1;
                    $street_2 ='';
                    echo('</br>');
                    $city_state_postal = explode(',',$a_address[1]);
                    $city = $city_state_postal[0];
                    echo $city;
                    echo('</br>');
                    $state_postal = explode(' ',$city_state_postal[1]);
                    //TODO get state shotcut
                    $state = $state_postal[1];
                    echo $state;
                    echo('</br>');
                    $postal = $state_postal[2];
                    echo $postal;
                    echo('</br>');                      
                       
                   }
                   //phone
                   //var_dump($store_info[3]);
                   $phone_tmp= str_replace(' Phone: (','', $store_info[3]); 
                   $phone_tmp = str_replace(') ','-',$phone_tmp);
                   $phone = str_replace('--- ','',$phone_tmp);
                   echo $phone;
                   echo('</br>');
                   
                                 //check if store # is in DB
               $query = $this->db->select('id')
               ->where('store_num =', $store_number)
               ->where('retailer_id =',5)
               ->get('retailer_store');  
               $store_id = $query->row()->id;
                if(!empty($store_id)){
                    echo 'UPDATE '.$store_id ;
                    //load store from DB
//                    $query = $this->db->select()
//                    ->where('store_num =', $store_number)
//                    ->where('retailer_id =',5)
//                    ->get('retailer_store');  
//                    $old_store = $query->row();
                    
                    $data = array(
                      'street_1' => $street_1,
                      'street_2' => $street_2,
                      'city' => $city,
                      'province' => $this->getStateCode($state),
                      'postal_code' => $postal,
                      'country_code' => 'US',
                      'status' => 1,
                      'phone' => $phone 
                    );
                    $this->db->where('id', $store_id); 
                    $this->db->update('retailer_store', $data);
                }else{
                    echo 'INSERT';
                    echo ($this->getStateCode($state));
                    $data = array(
                      'retailer_id' => 5, 
                      'store_name' => $store_name,
                      'slug' => url_title(strtolower($store_name)),
                      'store_num' => $store_number,
                      'street_1' => $street_1,
                      'street_2' => $street_2,
                      'city' => $city,
                      'province' => $this->getStateCode($state),
                      'postal_code' => $postal,
                      'country_code' => 'US',
                      'phone' => $phone,  
                      'status' => 1
                    );
                    $this->db->insert('retailer_store', $data);
                }
               }
            }
            fclose($fh);

           die('0');
       }
       
       private function getStateCode($province_name){
                $query = $this->db->select('province_code')
               ->where('country_code =', 'US')
               ->where('province_name =',$province_name)
               ->get('province');  
                $code = $query->row()->province_code;
                if(!empty($code)){
                  return $code;
                }else{
                    return 'unknown';
                }
       }
       
       function fix_uk_counties_slug(){
       //    fix province table
        $query = $this->db->select()    
        ->where('country_code =', 'UK')
        ->get('province');             
       foreach($query->result() as $province){
           //var_dump($province->province_code);
          $query1 = $this->db->set('province_code', url_title(strtolower($province->province_code)))
          ->where('province_name', $province->province_name)
          ->update('province');  
          //var_dump($this->db->last_query());
       }
       
       //fix created stores
        $query = $this->db->select()    
        ->where('retailer_id =', '339')
        ->get('retailer_store');             
       foreach($query->result() as $store){
           //var_dump($province->province_code);
          $query1 = $this->db->set('province', url_title(strtolower($store->province)))
          ->where('id', $store->id)
          ->update('retailer_store');  
          //var_dump($this->db->last_query());
       }
       
       }
       
       
       public function check_sphero_badge_test(){
           $this->load->helper('quiz/quiz');
        
           var_dump(check_sphero_badge(46));
       }
       
       public function pdf_create(){
           
               $this->load->helper('pdf_helper');
      if($this->session->userdata("user_id") != false){
          
          $user_info = $this->account_model->get_info($this->session->userdata("user_id"));

              $data['first_name'] = $user_info->first_name;
              $data['last_name'] = $user_info->last_name;
             
              $data['screen_name'] = $user_info->screen_name;    
              $data['date_issued'] =  date('m-d-Y', time());
           
              $cert['sphero']  = $this->load->view('sphero_cert', $data, true);
  
$this->load->view('pdf/pdfreport', $cert);
      }else{
          print 'pLs LOg In.';
      }
       }     
}
