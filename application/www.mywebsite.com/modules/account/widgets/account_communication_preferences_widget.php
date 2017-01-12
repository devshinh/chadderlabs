<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Account_communication_preferences_widget extends Widget {

    public function run($args = array()) {
        $this->load->config('account/account', TRUE);
        $this->load->library('account/CmsAccount');
        $data = array();
        $data['environment'] = $this->config->item('environment');
        $data['js'] = $this->config->item('js', 'account');
        $data['css'] = $this->config->item('css', 'account');
        $module_title = 'Communication Preferences';

        //load campaing monitor list ids
        $this->cm_lists = $this->config->item('cm_lists', 'account');

        // check permission
        $data['userid'] = (int) ($this->session->userdata("user_id"));
        
        $user = $this->account_model->get_user($data['userid']);
        $data['user'] = $user;

        if ($data['userid'] == 0) {
            $this->session->set_flashdata('message','Please login.');
          redirect('/login');
        }

        if (is_array($args) && count($args) > 0 && array_key_exists('title', $args)) {
            if (array_key_exists('title', $args)) {
                $data['title'] = $args['title'];
            } else {
                $data['title'] = '';
            }
            if (array_key_exists('welcome_text', $args)) {
                $data['welcome_text'] = $args['welcome_text'];
            } else {
                $data['welcome_text'] = '';
            }

            $data['error'] = $this->session->flashdata('error');

            //capmaign monitor subscriptions
            $monthly_list_id = $this->cm_lists['monthly'];
            $new_swag_list_id = $this->cm_lists['swag'];
            $new_labs_list_id = $this->cm_lists['labs'];
            $survey_list_id = $this->cm_lists['survey'];

            $result = $this->cmonitor->get_request('subscribers/' . $monthly_list_id . '.json?email=' . urlencode($user->email));
            if (isset($result->response->State) && $result->response->State == 'Active') {
                $newsletter['monthly']['active'] = true;
            } else {
                $newsletter['monthly']['active'] = false;
            }

            $result = $this->cmonitor->get_request('subscribers/' . $new_swag_list_id . '.json?email=' . urlencode($user->email));
            if (isset($result->response->State) && $result->response->State == 'Active') {
                $newsletter['swag']['active'] = true;
            } else {
                $newsletter['swag']['active'] = false;
            }

            $result = $this->cmonitor->get_request('subscribers/' . $new_labs_list_id . '.json?email=' . urlencode($user->email));
            if (isset($result->response->State) && $result->response->State == 'Active') {
                $newsletter['labs']['active'] = true;
            } else {
                $newsletter['labs']['active'] = false;
            }

            $result = $this->cmonitor->get_request('subscribers/' . $survey_list_id . '.json?email=' . urlencode($user->email));
            if (isset($result->response->State) && $result->response->State == 'Active') {
                $newsletter['survey']['active'] = true;
            } else {
                $newsletter['survey']['active'] = false;
            }

            $data['newsletters'] = $newsletter;

            $data['message'] = $this->session->flashdata('message');
            // load widget view         

            return array('content' => $this->render('widget_account_communication_preferences', $data));


            // if anything goes wrong, return 404
            $this->output->set_status_header('404');
            redirect('page-not-found');
            return '<p>Widget not found.</p>';
        }

        if ($data['environment'] == 'admin_panel') {
            return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
        }
    }

}

?>
