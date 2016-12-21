<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Refer_colleague_history_widget extends Widget {

    public function run($args = array()) {
        
        $this->load->config('refer_colleague/refer_colleague', TRUE);
        $this->lang->load('hotcms');
        $this->load->model('refer_colleague/refer_colleague_model');
        $data = array();
        $data['environment'] = $this->config->item('environment');
        $data['js'] = $this->config->item('js', 'refer_colleague');
        $data['css'] = $this->config->item('css', 'refer_colleague');
        $data['dropdown_hint'] = $this->config->item('dropdown_hint', 'refer_colleague');
        $data['lines_per_column'] = $this->config->item('lines_per_column', 'refer_colleague');
        $module_title = 'Refer Colleague History';

        $data['userid'] = (int) ($this->session->userdata("user_id"));
        if (!empty($data['userid'])) {
            $this->load->helper('account/account');
            $user_info = account_get_user($data['userid']);
            $user_fullname = $user_info->first_name . ' ' . $user_info->last_name;
        }

        if ($data['userid'] < 1) {
            return array('content' => '');
        }

        if (array_key_exists('title', $args)) {
            $data['title'] = $args['title'];
        }

        $ref_history = $this->refer_colleague_model->referal_user_history($data['userid']);
         
        foreach ($ref_history as $r){

            //check state of refferal
            $history_state = $this->refer_colleague_model->referal_user_check($r->email);
            switch ($history_state) {
                case 0:
                    $r->state = '<span class="red">Referred</span>';
                    break;
                case 1:
                    $r->state = '<span class="red">Registered</span>';
                    $r->screen_name = $this->refer_colleague_model->get_user_screen_name($r->email);
                    break;
                case 2:
                    $r->state = '<span class="green">Verified</span>';
                    $r->screen_name = $this->refer_colleague_model->get_user_screen_name($r->email);
                    break;                
                default:
                    break;    
            }
        }
        $data['history'] = $ref_history;

        // load widget view
        //$content = '<div id="refercoleagueform"><form method="post" id="refer_colleague_form">';
        $content = $this->render('refer_colleague_history', $data);
        //$content .= "</form></div>\n";
        return array(
            'meta_title' => $module_title,
            'content' => $content,
        );
   


        // if anything goes wrong, return 404
        $this->output->set_status_header('404');
        return array('content' => '<p>Form not found.</p>');
        //}

        if ($data['environment'] == 'admin_panel') {
            return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
        }
    }

}

?>