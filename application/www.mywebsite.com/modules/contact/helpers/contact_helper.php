<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Contact helper
 *
 * Some functions to share within HotCMS
 */
/**
 * List contacts
 * @param  str  table name
 * @param  int  table primary field id
 */
if (!function_exists('contact_list')) {

    function contact_list($con_name, $con_id) {
        $CI = & get_instance();
        $CI->load->config('contact/contact', TRUE);
        $CI->load->model('contact/model_contact');
        $output = $CI->model_contact->get_contact_by_connection($con_name, $con_id);
        return $output;
    }

}

/**
 * Get a contact by id
 * @param  int  contact id
 */
if (!function_exists('contact_get')) {

    function contact_get($id) {
        $CI = & get_instance();
        $CI->load->config('contact/contact', TRUE);
        $CI->load->model('contact/model_contact');
        $output = $CI->model_contact->get_contact_by_id($id);
        return $output;
    }

}

/**
 * Display and process a Contact form
 */
if (!function_exists('contact_form')) {

    function contact_form($args = array()) {
        $CI = & get_instance();
        $CI->load->library('session');
        $CI->load->library('form_validation');
        $CI->load->config('contact/contact', TRUE);
        $CI->load->model('model__global', 'model');
        $CI->load->model('contact/model_contact');

        $data['message'] = $CI->session->flashdata('message');
        $data['error'] = $CI->session->flashdata('error');

        // Validation rules
        $CI->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $CI->form_validation->set_rules('email', 'Email Address', 'trim|required|filter_var|xss_clean');
        $CI->form_validation->set_rules('postal', 'Postal Code', 'trim|required|xss_clean');

        $firstname = $CI->input->post('firstname');
        $lastname = $CI->input->post('lastname');
        $email = $CI->input->post('email');
        $postal = $CI->input->post('postal');
        $concerns = $CI->input->post('concerns');
        $comment = $CI->input->post('comment');
        $terms = $CI->input->post('terms');

        if ($CI->form_validation->run()) {
            $result = $CI->model_contact->contact_request($firstname, $lastname, $email, $postal, $concerns, $comment);
            //var_dump($result);
            //die('its running');
            $CI->session->set_flashdata('postal', $postal);
            $CI->session->set_flashdata('concerns', $concerns);

            if ($result) {
                if ($email > '' && $terms == 1) {
                    // TODO: load other helpers
                    /*
                      try {
                      $CI->load->helper('newsletter/newsletter_signup');
                      if (function_exists('newsletter_signup')) {
                      $result = newsletter_signup($firstname, $lastname, $email, $postal, 'contact');
                      }
                      }
                      catch (Exception $e) {
                      $CI->session->set_flashdata('<p>Failed to subscribe to the e-newsletter.</p>');
                      } */
                }
                redirect('/contact-us-confirm', 'refresh');
                return;
            } else {
                $CI->session->set_flashdata('<p>Failed to submit the contact form.</p>');
            }
        } else {
            // return validation errors
            $ve = validation_errors();
            if ($ve > '') {
                $data['error'] = $ve;
            }
        }
        // build the form
        $data['firstname'] = array('name' => 'firstname',
            'id' => 'firstname',
            'type' => 'text',
            'value' => $CI->form_validation->set_value('firstname'),
        );
        $data['lastname'] = array('name' => 'lastname',
            'id' => 'lastname',
            'type' => 'text',
            'value' => $lastname,
        );
        $data['email'] = array('name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $CI->form_validation->set_value('email'),
        );
        $data['postal'] = array('name' => 'postal',
            'id' => 'postal',
            'type' => 'text',
            'value' => $CI->form_validation->set_value('postal'),
        );
        $data['concerns'] = array('name' => 'concerns[]',
            'type' => 'checkbox',
        );
        $data['concerns_default'] = (is_array($concerns) ? $concerns : array());
        $data['comment'] = array('name' => 'comment',
            'id' => 'comment',
            'type' => 'textarea',
            'value' => $comment,
            'cols' => '40',
            'rows' => '5',
        );
        $data['terms'] = array('name' => 'terms',
            'id' => 'terms',
            'type' => 'checkbox',
            'value' => '1',
            'checked' => ($terms == '1'),
        );
        // load module view
        return $CI->load->view('contact/index', $data, TRUE);
    }

    /**
     * Display and process a Contact form --> for shipping
     */
    if (!function_exists('contact_form_new')) {

        function contact_form_new($args = array()) {
            $CI = & get_instance();
            $CI->load->library('session');
            $CI->load->library('form_validation');
            $CI->load->config('contact/contact', TRUE);
            $CI->load->model('model__global', 'model');
            $CI->load->model('contact/model_contact');

            $data['message'] = $CI->session->flashdata('message');
            $data['error'] = $CI->session->flashdata('error');

            $user_id = (int)($CI->session->userdata('user_id'));

            $address_1 = $CI->input->post('address_1');
            $address_2 = $CI->input->post('address_2');
            $city = $CI->input->post('city');
            $province = $CI->input->post('province');
            $postal = $CI->input->post('postal');
            $contact_name = $CI->input->post('contact_name');

            // build the form
            $data['address_1'] = array('name' => 'address_1',
                'id' => 'address_1',
                'type' => 'text',
                'value' => $CI->form_validation->set_value('address_1'),
            );
            $data['address_2'] = array('name' => 'address_2',
                'id' => 'address_2',
                'type' => 'text',
                'value' => $address_2,
            );
            $data['city'] = array('name' => 'city',
                'id' => 'city',
                'type' => 'text',
                'value' => $CI->form_validation->set_value('city'),
            );
            $data['province'] = array('name' => 'province',
                'id' => 'province',
                'type' => 'text',
                'value' => $CI->form_validation->set_value('province'),
            );
            $data['postal'] = array('name' => 'postal',
                'id' => 'postal',
                'type' => 'text',
                'value' => $CI->form_validation->set_value('postal'),
            );
            $data['contact_name'] = array('name' => 'contact_name',
                'id' => 'contact_name',
                'type' => 'text',
                'value' => $CI->form_validation->set_value('contact_name'),
            );
            $data['user_id'] = array('name' => 'user_id',
                'id' => 'user_id',
                'type' => 'hidden',
                'value' =>  $user_id,
            );
            // load module view
            return $CI->load->view('contact/contact_create_shipping', $data, TRUE);
        }

    }
}

/* End of file contact_helper.php */
/* Location: ./helpers/contact_helper.php */
