<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * User Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class User extends HotCMS_Controller {

    public function __construct() {
        parent::__construct();

        // check permission
        if (!($this->ion_auth->logged_in())) {
            $this->session->set_userdata('redirect_to', $this->uri->uri_string());
            redirect($this->config->item('login_page'));
        }
        if (!has_permission('manage_user')) {
            show_error($this->lang->line('hotcms_error_insufficient_privilege'));
        }

        $this->load->config('user', TRUE);
        $this->load->model('user_model');
        $this->load->model('role/role_model');
        $this->load->model('refer_colleague/refer_colleague_model');
        $this->load->helper('badge/badge');
        $this->load->helper('account/account');

        $this->module_url = $this->config->item('module_url', 'user');
        $this->module_header = $this->lang->line('hotcms_address_book');
        $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_user'));

        $this->salutation = array(
            'Mr.' => 'Mr.',
            'Mrs.' => 'Mrs.',
            'Miss' => 'Miss'
        );
        $this->key_prefix = 'manage_user_';

        $this->search_options = array('email' => 'Email','screenname' => 'Screen name');
    }

    /**
     * list all users
     * @param  int  page number
     */
    public function index($page_num = 1) {
        $data = array();
        $data['module_url'] = $this->module_url;
        $data['module_header'] = $this->module_header;
        $data['add_new_text'] = $this->add_new_text;

        // paginate configuration
        $this->load->library('pagination');
        //$pagination_config = pagination_configuration();
        //$pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
        $default_per_page = $pagination_config['per_page'] = 10;


        if ($this->input->post()) {

          $filters = array(
            'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
            'keyword' => $this->input->post('keyword'),
            'keyword_column' => $this->input->post('search_options'),
            'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
          );
          $this->session->set_userdata('user_filters', $filters);
          redirect('user');
        }

        $filters = $this->session->userdata('user_filters');
        if (!is_array($filters)) {
          $filters = array(
            'per_page' => $default_per_page,
            'keyword' => '',
            'keyword_column' => 'email',
            'per_page' => $default_per_page,
          );
        }
        $data['filters'] = $filters;
        $data['form']['keyword_column_options'] = $this->search_options;
        $data['form']['keyword_column'] = $filters['keyword_column'];
        $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
        $data['form']['hidden'] = array('per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
        $data['form']['per_page_options'] = list_page_options();

        //$users = $this->user_model->user_list($filters, TRUE, $page_num, $pagination_config['per_page']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->user_model->user_count($filters);

    //var_dump($pagination_config['total_rows']);
    $users = $this->user_model->user_list($filters, TRUE, $page_num, $pagination_config['per_page']);


    $right_data['aCurrent'] = $data['users'] = $users;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('user_index_page_num', $page_num);

        // paginate
        //$this->pagination->initialize($pagination_config);
        //$right_data['pagination'] = $this->pagination->create_links();
        self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user', $right_data);
    }

    /**
     * Set validation rules
     */
    private function _validate() {
        $this->form_validation->set_rules('first_name', strtolower(lang('hotcms_name_first')), 'trim|required');
        $this->form_validation->set_rules('last_name', strtolower(lang('hotcms_name_last')), 'trim|required');
        $this->form_validation->set_rules('screen_name', strtolower(lang('hotcms_user_name')), 'trim|required');
        $this->form_validation->set_rules('email', strtolower(lang('hotcms_email_address')), 'trim|required|filter_var');
        /* $this->form_validation->set_rules('username', strtolower(lang('hotcms_name_user')), 'trim|required|callback__validator_user');
          $this->form_validation->set_rules( 'roles', strtolower(lang( 'hotcms_role' )),  'required' ); */
        // if no password is present...
        if ($this->input->post('hdnMode') == 'insert') {
            $this->form_validation->set_rules('password', strtolower(lang('hotcms_password')), 'trim|required|matches[password_retype]');
            $this->form_validation->set_rules('password_retype', strtolower(lang('hotcms_password_retype')), 'trim|required');
        } elseif (($this->input->post('password') != '') && $this->input->post('hdnMode') == 'edit') {
            $this->form_validation->set_rules('password', strtolower(lang('hotcms_password_new')), 'trim|matches[password_retype]');
            $this->form_validation->set_rules('password_retype', strtolower(lang('hotcms_password_retype')), 'trim|required');
        }
    }

    /**
     * Calling create function from model class.
     *
     * @param id of item
     */
    public function create() {
        $this->load->model('contact/model_contact');

        $data = array();
        $data['module_url'] = $this->module_url;
        $data['module_header'] = $this->module_header;
        $data['add_new_text'] = 'User ' . $this->add_new_text;

        $this->_validate();

        if ($this->form_validation->run()) {
            if (!empty($_FILES['asset_file'])) {
                //TODO get avatars asset category ID
                $asset_id = modules::run('asset/controllers/upload_picture_external', $_FILES['asset_file'], 9);
            }
            //assing default avatar id when nothing is uploaded
            //TODO create default avatar
            //$this->user_model->insert();
            $username = $this->input->post('email');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $picture_id = $asset_id;

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                //'middle_name' => $this->input->post('middle_name'),
                'last_name' => $this->input->post('last_name'),
                'salutation' => $this->input->post('salutation'),
                'avatar_id' => $picture_id,
                    //'position' => $this->input->post('position')
            );
            if ($this->ion_auth->register($username, $password, $email, $additional_data)) { //check to see if we are creating the user
                //redirect them back to the admin page
                //$this->session->set_flashdata('message', "User Created");
                $this->add_message('confirm', $this->ion_auth->messages());
                //redirect("/hotcms/user");
            } else {
                $this->add_message('error', $this->ion_auth->errors());
            }

            //$user_id = $this->db->insert_id();
            $new_user = $this->ion_auth->get_newest_users(1);
            $user_id = $new_user[0]->id;
            if ($user_id > 0) {
                $this->model_contact->insert($user_id, 'user', 'personal');
                $this->model_contact->insert($user_id, 'user', 'work');

                $selected_roles = $this->input->post('roles');
                if (is_array($selected_roles) && !empty($selected_roles)) {
                    foreach ($this->role_model->get_all_active_roles() as $role) {
                        // check individual permission against each role
                        // to prevent injection
                        if (!has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                            continue;
                        }
                        if (in_array($role->name, $selected_roles)) {
                            $this->role_model->insert_user_role($user_id, $role->id);
                        }
                    }
                }

                //$this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_created_item')));
                $this->add_message('confirm', lang('hotcms_created_item'));
                redirect('user/edit/' . $user_id);
            }
        } else {
            if (validation_errors() > '') {
                $this->add_message('error', validation_errors());
            }
        }
        // set form data
        $right_data = array();
        $right_data['first_name_input'] = $this->_create_text_input('first_name', $this->input->post('first_name'), 100, 20, 'text');
        //$right_data['middle_name_input'] = $this->_create_text_input('middle_name', $this->input->post('middle_name'), 100, 20, 'text');
        $right_data['last_name_input'] = $this->_create_text_input('last_name', $this->input->post('last_name'), 100, 20, 'text');
        $right_data['username_input'] = $this->_create_text_input('username', $this->input->post('username'), 100, 20, 'text');
        $right_data['position_input'] = $this->_create_text_input('position', $this->input->post('position'), 100, 20, 'text');
        $right_data['salutation'] = $this->salutation;
        $right_data['password_input'] = array(
            'name' => 'password',
            'id' => 'password',
            'value' => set_value('password', $this->input->post('password')),
            'maxlength' => '50',
            'size' => '20',
            'class' => 'text'
        );
        $right_data['password_retype_input'] = array(
            'name' => 'password_retype',
            'id' => 'password_retype',
            'value' => set_value('password_retype', $this->input->post('password_retype')),
            'maxlength' => '50',
            'size' => '20',
            'class' => 'text'
        );
        $right_data['email_input'] = $this->_create_text_input('email', $this->input->post('email'), 50, 20, 'text');
        $right_data['active_input'] = $this->_create_checkbox_input('active', 'active', 'active', 'accept', false, 'margin:10px');
        $right_data['asset_file_input'] = $this->_create_text_input('asset_file', $this->input->post('asset_file'), 100, 20, '');

        foreach ($this->role_model->get_all_active_roles() as $role) {
            // check individual permission against each role
            if (!has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                continue;
            }
            if ($this->input->post('roles')) {
                $checked = in_array($role->name, $this->input->post('roles'));
            } else {
                $checked = false;
            }
            $data['roles'][$role->name . '_role_checkbox'] = $this->_create_checkbox_input('roles[]', $role->name, $role->name, $checked, 'margin:10px');
        }

        $this->load_messages();
        self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user_create', $right_data);
    }

    /**
     * Edit user profile and roles
     * @param type $id
     */
    public function edit($id, $message = '') {
        if(empty($id)){
            redirect('/'.$this->module_url);
        }
        $this->load->model('role/role_model');
        $this->load->model('contact/model_contact');
        $this->load->helper('account/account');

        $data = array();
        $data['module_url'] = $this->module_url;
        $data['module_header'] = "Edit " . $this->module_header;
        $data['user_id'] = $id;
        $right_data = array();
        $right_data['java_script'] = 'modules/' . $this->module_url . '/js/user.js';

        $data['current_item'] = $this->user_model->get_user_by_id($id);
        $right_data['form'] = self::set_edit_form($data['current_item']);
        // check permission
        if (!$this->_has_access_to_user($id)) {
            show_error("You do not have privileges to edit this user.");
        }

        $this->_validate();


        if ($this->form_validation->run()) {
            $selected_roles = $this->input->post('roles');
            $post_data = $this->input->post();
            // fix the post data for updates
            $checkboxe_fields = array('newsletter_monthly', 'newsletter_newlab', 'newsletter_newswag', 'newsletter_survey', 'active', 'verified');
            foreach ($checkboxe_fields as $cf) {
                if (!array_key_exists($cf, $post_data)) {
                    $post_data[$cf] = 0;
                }
            }
            if (array_key_exists('roles', $post_data)) {
                unset($post_data['roles']);
            }
            if (array_key_exists('password_retype', $post_data)) {
                unset($post_data['password_retype']);
            }
            if (array_key_exists('password', $post_data) && $post_data['password'] == '') {
                unset($post_data['password']);
            }
            $this->ion_auth->update_user($id, $post_data);
            //$update_msg = $this->user_model->update($id);
            //$data['avatar_picture'] = $this->user_model->get_user_avatar($asset_id);
            if (!empty($_FILES['asset_file']['name'])) {
                //TODO get avatars asset category ID
                //TODO - dispaly assets error messages
                $asset_id = modules::run('asset/controllers/upload_picture_external', $_FILES['asset_file'], 9);
                $this->user_model->update_avatar($id, $asset_id);
            }
            if ($data['current_item']->active == 0 && $post_data['active'] == '1') {
                $this->ion_auth->activate($id);
            } elseif ($data['current_item']->active == 1 && (!isset($post_data['active']) || $post_data['active'] == '')) {
                $this->ion_auth->deactivate($id);
            }
            if ($data['current_item']->verified == 0 && $post_data['verified'] == 1) {
                $this->user_model->update_verified_date($id);
                if(!check_user_badge($id, 'cred')){
                  account_add_badge($id, 'cred');
                }
                //check if there was an referal for this user
                $user_info = $this->refer_colleague_model->check_referral($data['current_item']->email);
                if(!empty($user_info) && $user_info->id > 0){
//                    $user_id, $ce, $type, $ref_table = '', $ref_id = 0, $description = ''
                  account_add_contest_entries($user_info->referal_user_id, 50,'reffer_veri','refer_colleague',$user_info->id,' earned 50 contest entries because their <a href="http://earetailprofessionals.cheddarlabs.com/overview/refer-a-colleague">referral</a> verified themselves!');
                //check for cred badge (5 successfull referrals for user)
                  $num_of_ref = $this->refer_colleague_model->get_number_of_referrals($user_info->referal_user_id);

                  if($num_of_ref == 5){
                      account_add_badge($user_info->referal_user_id, 'advocate');
                  }
                }
                //send verificaiton email
                $user = $this->user_model->get_user_by_id($id);
                $this->user_model->email_verify($user);
            }
            $this->add_message('confirm', $this->ion_auth->messages());
            $this->add_message('error', $this->ion_auth->errors());

            // delete user-role combinations
            foreach ($this->role_model->get_all_active_roles() as $role) {
                // check individual permission against each role
                //if (has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                // can only delete roles that the current user has access to
                $this->role_model->delete_user_roles($id, $role->id);
                //}
            }
            if (is_array($selected_roles) && !empty($selected_roles)) {
                foreach ($this->role_model->get_all_active_roles() as $role) {
                    // check individual permission against each role
                    // to prevent injection
                    if (!has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                        continue;
                    }
                    if (in_array($role->name, $selected_roles)) {
                        $this->role_model->insert_user_role($id, $role->id);
                    }
                }
            }
//      if (empty($update_msg)) {
//        //$this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
//        $this->add_message('confirm', 'User was updated.');
//      }
//      elseif ($update_msg =='pwd_updated') {
//        //$this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => 'Password was updated.'));
//        $this->add_message('confirm', 'Password was updated.');
//      }
//      elseif ($update_msg =='pwd_not_updated') {
//        //$this->session->set_userdata(array('messageType' => 'error', 'messageValue' => 'Password was NOT updated.'));
//        $this->add_message('error', 'Password was NOT updated.');
//      }
            $data['current_item'] = $this->user_model->get_user_by_id($id);
        } elseif (validation_errors() > '') {
            //$this->session->set_userdata(array('messageType' => 'error', 'messageValue' => validation_errors()));
            $this->add_message('error', validation_errors());
        }

        //set message
        if (!empty($message)) {
            $this->session->set_userdata(array('messageType' => $message['type'], 'messageValue' => $message['value']));
            $data['message'] = self::setMessage(false);
        }

        // load dropdown options
        $right_data['retailers'] = array('' => '');
        $retailers = account_retailers();
        foreach ($retailers as $v) {
            $right_data['retailers'][$v->id] = $v->name;
        }
        //$right_data['retailers'][99999] = 'Other';
        $right_data['stores'] = array('' => '');
        $stores = account_stores();
        foreach ($stores as $v) {
            $right_data['stores'][$v->id] = $v->store_name . ' (' . $v->store_num . ')';
        }
        //$right_data['stores'][99999] = 'Other';
        $right_data['employments'] = array('' => '') + account_employments();
        $right_data['job_titles'] = array('' => '') + account_job_titles();
        $right_data['selected_country'] = $this->input->post('country_code') > '' ? $this->input->post('country_code') : $data['current_item']->country_code;
        $right_data['selected_province'] = $this->input->post('province') > '' ? $this->input->post('province') : $data['current_item']->province_code;
        $right_data['selected_retailer'] = $this->input->post('retailer_id') > '' ? $this->input->post('retailer_id') : $data['current_item']->retailer_id;
        $right_data['selected_store'] = $this->input->post('store_id') > '' ? $this->input->post('store_id') : $data['current_item']->store_id;
        $right_data['selected_employment'] = $this->input->post('employment') > '' ? $this->input->post('employment') : $data['current_item']->employment;
        $right_data['selected_job_title'] = $this->input->post('job_title') > '' ? $this->input->post('job_title') : $data['current_item']->job_title;
        $right_data['selected_newsletter_monthly'] = $this->input->post('newsletter_monthly') > '' ? $this->input->post('newsletter_monthly') : $data['current_item']->newsletter_monthly;
        $right_data['selected_newsletter_newlab'] = $this->input->post('newsletter_nvewlab') > '' ? $this->input->post('newsletter_newlab') : $data['current_item']->newsletter_newlab;
        $right_data['selected_newsletter_newswag'] = $this->input->post('newsletter_newswag') > '' ? $this->input->post('newsletter_newswag') : $data['current_item']->newsletter_newswag;
        $right_data['selected_newsletter_survey'] = $this->input->post('newsletter_survey') > '' ? $this->input->post('newsletter_survey') : $data['current_item']->newsletter_survey;

        $right_data['form'] = self::set_edit_form($data['current_item']);

        $right_data['avatar_picture'] = $this->user_model->get_user_avatar($data['current_item']->avatar_id);

        $right_data['form_contacts'] = modules::run('contact/controller/get_edit_forms', 'user', $id);

        foreach ($this->role_model->get_role_names_by_user_id($id) as $role_name) {
            $role_names[$role_name->name] = $role_name->name;
        }
        foreach ($this->role_model->get_all_active_roles() as $role) {
            // check individual permission against each role
            if (!has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                continue;
            }
            if (!empty($role_names)) {
                $checked = in_array($role->name, $role_names);
            } else {
                $checked = false;
            }
            $data['roles'][$role->name . '_role_checkbox'] = $this->_create_checkbox_input('roles[]', $role->name, $role->name, $checked, 'margin:10px');
        }
        //get info about user refferal
        $referral_info = $this->refer_colleague_model->check_referral($data['current_item']->email,TRUE);
        if(!empty($referral_info)){
            $referral_user = $this->user_model->get_user_by_id($referral_info->referal_user_id);
            $data['ref_user'] = $referral_user;

            $referral_award = $this->refer_colleague_model->check_previous_referral($referral_info->id,$referral_info->referal_user_id);
            if(!$referral_award){
                $data['ref_award'] = $referral_user;
            }
        }
        //get data for activity feed
            $args['title'] = 'Activity feed';
            $args['user_id'] = $id;
            $activity_feed = widget::run('user/user_activity_widget', $args);
        //var_dump($activity_feed);
        $data['activity_feed'] = $activity_feed;
        //get data for user stats
            $user_points_current = $this->account_model->get_user_points($id);
            $user_points_lifetime = $this->account_model->get_user_points($id, 'lifetime');

            $user_points_ea = $this->account_model->get_user_points($id, 'ea');

            $data['user_points']['current'] = $user_points_current;
            $data['user_points']['lifetime'] = $user_points_lifetime;
            $data['user_points']['ea'] = $user_points_ea;

            //$user_draws = $this->account_model->get_user_draws($id);
            //$data['user_draws']['current'] = $user_draws;
            //$user_draws_lifetime = $this->account_model->get_user_draws($id, 'lifetime');
            //$data['user_draws']['lifetime'] = $user_draws_lifetime;

            //# of quizzes
            $data['quiz_number'] = quiz_get_number_of_user_quizzes($id);

        //$right_data['message'] = self::setMessage(false);
        $this->load_messages();
        self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user_edit', $right_data);
    }

    private function set_edit_form($current_item) {
        $data = array();
        $data['salutation'] = $this->salutation;
        $data['first_name_input'] = $this->_create_text_input('first_name', $current_item->first_name, 100, 20, 'text');
        //$data['middle_name_input'] = $this->_create_text_input('middle_name', $current_item->middle_name, 100, 20, 'text');
        $data['last_name_input'] = $this->_create_text_input('last_name', $current_item->last_name, 100, 20, 'text');
        //$data['username_input'] = $this->_create_text_input('username', $current_item->username, 50, 20, 'text');
        $data['screen_name_input'] = $this->_create_text_input('screen_name', $current_item->screen_name, 50, 20, 'text');
        $data['email_input'] = $this->_create_text_input('email', $current_item->email, 50, 20, 'text');
        $data['password_input'] = array(
            'name' => 'password',
            'id' => 'password',
            'value' => set_value('password', ''),
            'maxlength' => '50',
            'size' => '20',
            'class' => 'password'
        );
        $data['password_retype_input'] = array(
            'name' => 'password_retype',
            'id' => 'password_retype',
            'value' => set_value('password_retype', ''),
            'maxlength' => '50',
            'size' => '20',
            'class' => 'password'
        );
        $data['referral_code'] = array('name' => 'referral_code',
            'id' => 'referral_code',
            'type' => 'text',
            'value' => $current_item->referral_code,
        );
        $data['hire_date'] = array('name' => 'hire_date',
            'id' => 'hire_date',
            'type' => 'text',
            'value' => $current_item->hire_date,
        );
        //$data['position_input'] = $this->_create_text_input('position', $current_item->position, 50, 20, 'text');
        $data['active_input'] = $this->_create_checkbox_input('active', 'active', '1', $current_item->active == 1, 'margin:0 0 5px 0');
        $data['verified_input'] = $this->_create_checkbox_input('verified', 'verified', '1', $current_item->verified == 1, 'margin:0 0 5px 0');
        $data['asset_file_input'] = $this->_create_text_input('asset_file', $this->input->post('asset_file'), 100, 20, '');
        return $data;
    }

    /**
     * Calling delete function from model class
     *
     * @param id of item
     */
    public function delete($id) {
        // check permission
        if (!$this->_has_access_to_user($id)) {
            show_error("You do not have privileges to delete this user.");
        }

        $this->role_model->delete_user_roles($id);

        $this->load->model('contact/model_contact');
        $this->model_contact->delete_by_user_id($id);

        $this->ion_auth->delete_user($id);
        $this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_deleted_item')));

        redirect('user');
    }

    /**
     * Function for activate user
     *
     * @param id of user
     */
    public function activate($id, $code) {
        // check permission
        if (!$this->_has_access_to_user($id)) {
            show_error("You do not have privileges to activate this user.");
        }
        $this->ion_auth->activate($id, $code);
        // TODO: change to use Ajax
        return $this->index();
    }

    /**
     * Function for deactivate user
     *
     * @param id of user
     */
    public function deactivate($id) {
        // check permission
        if (!$this->_has_access_to_user($id)) {
            show_error("You do not have privileges to deactivate this user.");
        }
        $this->ion_auth->deactivate($id);
        // TODO: change to use Ajax
        return $this->index();
    }

    /**
     * Delete contact from user module
     *
     * @param id of contact
     * @param user_id of user
     */
    public function delete_contact($id, $user_id) {
        // check permission
        if (!$this->_has_access_to_user($user_id)) {
            show_error("You do not have privileges to edit this user.");
        }
        $this->load->model('contact/model_contact');

        $this->model_contact->delete_by_id($id);

        $message = array();
        $message['type'] = 'confirm';
        $message['value'] = $this->lang->line('hotcms_updated_item');
        //self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user_edit', $right_data);
        $this->edit($user_id, $message);
    }

    /**
     * Function to add new contact to user
     *
     * @param id of user
     */
    public function add_new_contact($user_id) {
        // check permission
        if (!$this->_has_access_to_user($user_id)) {
            show_error("You do not have privileges to edit this user.");
        }
        // TODO: after adding new contact, keep the Contact tab open
        $contact_name = $this->input->post('contact_name');

        $this->load->model('contact/model_contact');
        //$right_data['java_script'] = 'modules/' . $this->module_url . '/js/user.js';
        //$data['module_url'] = $this->module_url;
        //$data['module_header'] = $this->module_header;
        //$data['add_new_text'] = $this->add_new_text;


        $this->_validate_contact_name();

        if ($this->form_validation->run()) {
            $this->model_contact->insert($user_id, 'user', $contact_name);

            //$data['current_item'] = $this->user_model->get_user_by_id($user_id);
            //$data['user_id'] = $user_id;
            //$right_data['form'] = self::set_edit_form($data['current_item']);
            // $contacts = $this->model_contact->get_contact_by_connection('user', $user_id);
            //$right_data['form_contacts'] = modules::run('contact/controller/get_edit_forms', 'user', $user_id);
            //$this->session->set_userdata(array('messageType' => 'confirm', 'messageValue' => lang('hotcms_updated_item')));
            //$data['message'] = self::setMessage(false);
            //$this->load_messages();
            //self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user_edit', $right_data);
            $message = array();
            $message['type'] = 'confirm';
            $message['value'] = $this->lang->line('hotcms_updated_item');
            $this->edit($user_id, $message);
        } else {
            //$data['current_item'] = $this->user_model->get_user_by_id($user_id);

            //$right_data['form'] = self::set_edit_form($data['current_item']);

            //$contacts = $this->model_contact->get_contact_by_connection('user', $user_id);
            //$right_data['form_contacts'] = modules::run('contact/controller/get_edit_forms', 'user', $user_id);

            //if (validation_errors() > '') {
            //    $this->add_message('error', validation_errors());
            //}

            //$this->load_messages();
            //self::loadBackendView($data, 'user/user_submenu', NULL, 'user/user_edit', $right_data);
            $message = array();
            $message['type'] = 'error';
            $message['value'] = validation_errors();
            $this->edit($user_id, $message);
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
     * check to see if the current user has permission to edit/delete/activate a user
     * @param  int  user id to check against
     * @return bool
     */
    private function _has_access_to_user($user_id) {
        $has_access = TRUE;
        if (!has_permission('super_admin')) {
            $roles = $this->role_model->get_role_names_by_user_id($id);
            foreach ($roles as $role) {
                if (!has_permission($this->key_prefix . str_replace(' ', '_', $role->name))) {
                    $has_access = FALSE;
                    break;
                }
            }
        }
        return $has_access;
    }

    /*     * *
     * get list of users by role
     * @param int role id
     * @return select box of users
     *
     * TODO - role by string not ID
     */

    public function get_users_location_select($role_id, $org_id) {

        $this->load->module('location', 'location_module');
        $users = $this->user_model->lists_users_by_role($role_id);
        $select_options = array();
        foreach ($users as $user) {
            $select_options[$user->user_id] = $user->first_name . ' ' . $user->last_name;
        }
        $selected_array = $this->location_model->get_users_for_location($org_id);

        $selected = array();
        foreach ($selected_array as $s) {
            $selected[$s->user_id] = $s->user_id;
        }
        $data['select'] = $select_options;
        $data['selected'] = $selected;
        $user_select = $this->load->view('user_select', $data, true);
        return $user_select;
    }

    /*     * *
     * get list of users by role
     * @param int role id
     * @return checkboxes of users
     *
     * TODO - role by string not ID
     */

    public function get_users_checkboxes($role_id, $org_id) {

        $this->load->module('organization', 'organization_module');
        $users = $this->user_model->lists_users_by_role($role_id);
        /* $select_options = array();
          foreach ($users as $user) {
          $select_options[$user->user_id] = $user->first_name . ' ' . $user->last_name;
          }
          $selected_array = $this->organization_model->get_users_for_organization($org_id);

          $selected = array();
          foreach ($selected_array as $s) {
          $selected[$s->user_id] = $s->user_id;
          }
          $data['select'] = $select_options;
          $data['selected'] = $selected;
          $user_select = $this->load->view('user_select', $data, true);
          return $user_select;
         */

        foreach ($users as $u) {
            //if (count($active_role_permissions)) {
            //  $checked = array_key_exists($permission->id, $active_role_permissions);
            //}else{
            //  $checked = FALSE;
            //}
            $data['users'][$u->id] = $this->_create_checkbox_input('users[' . $u->id . ']', 'fuu', 1, FALSE, 'margin-right:10px');
            return $data;
        }
    }

}

?>
