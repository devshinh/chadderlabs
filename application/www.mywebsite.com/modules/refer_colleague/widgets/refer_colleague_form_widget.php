<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Refer_colleague_form_widget extends Widget {

    public function run($args = array()) {
      if (empty($args)) {
        $args = array();
      }
        $this->load->config('refer_colleague/refer_colleague', TRUE);
        $this->lang->load('hotcms');
        $this->load->model('refer_colleague/refer_colleague_model');
        $data = array();
        $data['environment'] = $this->config->item('environment');
        $data['js'] = $this->config->item('js', 'refer_colleague');
        $data['css'] = $this->config->item('css', 'refer_colleague');
        $data['dropdown_hint'] = $this->config->item('dropdown_hint', 'refer_colleague');
        $data['lines_per_column'] = $this->config->item('lines_per_column', 'refer_colleague');
        $module_title = 'Refer Colleague';

        // check permission
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

        $data['refer_colleague_hidden'] = array('refer_colleague_hidden' => '');
        $data['refer_modal_content'] = '';
        $result = FALSE;
        // process postback
        if (array_key_exists('postback', $args) && $this->input->post('email')) {
                $refer_info = $this->input->post();
                //check if refered user is not in DB
                $pre_check = $this->refer_colleague_model->referal_pre_check($refer_info['email']);
                //check if refered user is not in users alerady
                $pre_user_check = $this->refer_colleague_model->referal_user_pre_check($refer_info['email']);                
              if($pre_check && $pre_user_check){
                try {
                    $result = $this->refer_colleague_model->email_request($refer_info, $user_fullname);
                    //$result = TRUE;
                } catch (Exception $e) {
                    $messages = 'There was an error when trying to send out notice email: ' . $e->getMessage();
                }                
              }  
            if ($result && $pre_check && $pre_user_check) {
                //insert to the DB
                $this->refer_colleague_model->process_refer($refer_info, $data['userid']);
                //add to activity feed (points table)
                
                $this->refer_colleague_model->add_to_feed($data['userid'], $this->db->insert_id());
            }
            //set value for display the modal
            $data['refer_colleague_hidden'] = array('refer_colleague_hidden' => 'show_response');
            $content = '<div id="refercoleagueform"><form method="post" id="refer_colleague_form">';
            if($pre_check == FALSE){
                 $data['refer_modal_content'] = sprintf('Your colleague on <b>%s</b> was already notified about this program. Thank you!',$refer_info['email']);
            }elseif($pre_user_check == FALSE){
                $data['refer_modal_content'] = sprintf('Your colleague on <b>%s</b> is already registered to this program. Thank you!',$refer_info['email']);
            }else{
                if ($result) {
                    //done
                    $data['refer_modal_content'] = 'Your request has been sent. Thank you for your submission!';
                } else {
                    $data['refer_modal_content'] = 'Sorry but there was an error processing your submission.';

                }
            }
            $content .= $this->render('refer_colleague_form', $data);
            $content .= "</form></div>\n";
            return array('meta_title' => $module_title, 'content' => $content);
        }


        // load widget view
        $content = '<div id="refercoleagueform"><form method="post" id="refer_colleague_form">';
        $content .= $this->render('refer_colleague_form', $data);
        $content .= "</form></div>\n";
        return array(
            'meta_title' => $module_title,
            'content' => $content,
        );



        // if anything goes wrong, return 404
              $this->output->set_status_header('404');
              redirect('page-not-found');
        return array('content' => '<p>Form not found.</p>');
        //}

        if ($data['environment'] == 'admin_panel') {
            return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
        }
    }

}

?>