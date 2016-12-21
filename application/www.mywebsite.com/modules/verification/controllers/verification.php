<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verification Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	        Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */
class Verification extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }

    $this->load->config('verification/verification', TRUE);
    $this->load->model('verification/verification_model');
    $this->load->model('user/user_model');
    $this->load->model('refer_colleague/refer_colleague_model');    
    $this->load->library('pagination');

    $this->load->library('asset/asset_item');
    $this->load->helper('asset/asset');    
    $this->load->helper('account/account');    
    $this->load->helper('badge/badge');
    
    $this->module_url = $this->config->item('module_url', 'verification');
    $this->module_header = $this->lang->line('hotcms_verification');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_verification'));
    
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'product');
  }

  /**
   * list all items
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    if (!has_permission('manage_verification')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/verification.js';

    // search/filter form
    $default_sort_by = 'id'; // default field to sort by
    $default_per_page = 10; // default items to display per page
    if ($this->input->post()) {
      $sort_direction = $this->input->post('sort_direction');
      if (!in_array($sort_direction, array('asc', 'desc'))) {
        $sort_direction = 'asc';
      }
      $filters = array(
        'sort_by' => $this->input->post('sort_by') > '' ? $this->input->post('sort_by') : $default_sort_by,
        'sort_direction' => $this->input->post('sort_direction'),
        'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
        'keyword' => $this->input->post('keyword'),
        //'country' => $this->input->post('country_code'),
        //'status' => $this->input->post('status'),
      );
      $this->session->set_userdata('verification_filters', $filters);
      redirect('verification');
    }
    $filters = $this->session->userdata('verification_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        //'country' => '',
        'status' => '',
      );
    }
    $data['filters'] = $filters;

    //active filters string
    $active_filters = '';
    $separator = false;
    foreach ($filters as $filter_key => $filter_value) {
      if ($filter_key == 'keyword' && $filter_value != '') {
        $active_filters = 'Keyword - ' . $filter_value;
        $separator = true;
      }
      if ($filter_key == 'country' && $filter_value != '') {
        if ($separator) {
          $active_filters.= ', ';
        }
        $active_filters.= 'Country: ';
        foreach ($filter_value as $code) {
          $active_filters.= $code . ', ';
        }
        $active_filters = substr($active_filters, 0, -2);
        $separator = true;
      }
      if ($filter_key == 'status' && $filter_value != '') {
        if ($separator) {
          $active_filters.= ', ';
        }
        $active_filters.= 'Status: ';

        foreach ($filter_value as $code) {
          switch ($code) {
            case 1:
              $active_filters.= lang('hotcms_confirmed') . ', ';
              break;
            case 2:
              $active_filters.= lang('hotcms_closed') . ', ';
              break;
            case 0:
              $active_filters.= lang('hotcms_pending') . ', ';
              break;
            default:
              break;
          }
        }
        $active_filters = substr($active_filters, 0, -2);
        $separator = true;
      }
    }
    if ($separator == false)
      $active_filters.= 'None';

    $data['active_filters'] = $active_filters;

    //$data['form']['country_code_options'] = array('all' => 'All') + list_country_array();
    //$data['form']['country_code_options'] = list_country_array();
    //$data['form']['status_options'] = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Closed');
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    //$data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->verification_model->verification_count($filters);
    $verifications = $this->verification_model->verification_list($filters, $page_num, $pagination_config['per_page']);
    
    
    foreach( $verifications as $verification){
        //load user info
       $user_info = account_get_user($verification->user_id);     
       $verification->user_info = $user_info;
      }     
     
    //die(var_dump)
    $data['verifications'] = $verifications;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('verification_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'verification/verification_leftbar', NULL, 'verification/verification', NULL);
  }

  /**
   * creates new verification
   */
  public function create()
  {
    if (!has_permission('manage_verification')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Create verification";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " verification";

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('description', strtolower(lang('hotcms_description')), 'trim|required');

    if ($this->form_validation->run()) {
      $verification_id = $this->verification_model->insert($this->input->post());
      if ($verification_id > 0) {
        $this->add_message('confirm', 'Verification was created.');
        redirect('verification');
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['form']['description_input'] = $this->_create_text_input('description', $this->input->post('description'), 50, 20, 'text');
   
    //$pagination_config = pagination_configuration();
    //$data['verifications'] = $this->verification_model->verification_list(FALSE, TRUE, 1, $pagination_config['per_page']);
    $data['index_page_num'] = $this->session->userdata('verification_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'verification/verification_leftbar', NULL, 'verification/verification_create', NULL);
  }

  /**
   * edit verification
   * @param  int  $id
   * @param  int  page number
   */
  public function edit($id, $page_num = 1)
  {
    if (!has_permission('manage_verification')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit verification";
    
    $data['java_script'] = 'modules/' . $this->module_url . '/js/verification_edit.js';

    //$this->load->model('user/user_model');

    $data['verification_id'] = $id;
    $verification = $this->verification_model->verification_load($id, FALSE);
    
    if($verification->asset_id != 0){  
      $verification->image = asset_load_item($verification->asset_id);
    }
    if($verification->user_id != 0){  
      $verification->user = account_get_user($verification->user_id);
      $verification->user_retailer = account_get_retailer_info($verification->user_id);
    }    

    $data['currentItem'] = $verification;
    
    //get info about user refferal
    $referral_info = $this->refer_colleague_model->check_referral($verification->user->email,TRUE);
    if(!empty($referral_info)){
        $referral_user = $this->user_model->get_user_by_id($referral_info->referal_user_id);
        $data['ref_user'] = $referral_user;

        $referral_award = $this->refer_colleague_model->check_previous_referral($referral_info->id,$referral_info->referal_user_id);
        if(!$referral_award){
            $data['ref_award'] = $referral_user;
        }
    }    
   
    $this->form_validation->set_rules('status', strtolower(lang('hotcms_name')), 'required');

    if ($this->form_validation->run()) {
      // update verification
      $this->verification_model->verification_update_status($id);
      //update user table
      switch ($this->input->post('status')) {
          case 'pending':
          $st = 0;
              break;
          case 'active':
          $st = 1;
              break;
          case 'expired':
          $st = 0;
          case 'unactive':
          $st = 0;              
              break;          
          default:
              break;
      }
      $this->verification_model->verification_update_user_status($st,$verification->user_id);
      // reload
        $verification = $this->verification_model->verification_load($id, FALSE);
        if($verification->asset_id != 0){  
          $verification->image = asset_load_item($verification->asset_id);
        }
        if($verification->user_id != 0){  
          $verification->user = account_get_user($verification->user_id);
          $verification->user_retailer = account_get_retailer_info($verification->user_id);
        }      
          
        $data['currentItem'] = $verification;
        $this->add_message('confirm', 'Verification was updated.');
        if($st == 1){
            //send email
            $user_info =  $this->user_model->get_user_by_id($verification->user_id);
            $this->user_model->email_verify($user_info);
            //add cred badge
            $this->load->helper('badge/badge');
            $this->load->helper('account/account');
            if(!check_user_badge($verification->user_id, 'cred')){
              account_add_badge($verification->user_id, 'cred');
            }
            //check if there was an referal for this user
            $referral_user_info = $this->refer_colleague_model->check_referral($verification->user->email);                
            if(!empty($referral_user_info) && $referral_user_info->id > 0 && $referral_user_info->referal_user_id > 0){

              account_add_contest_entries($referral_user_info->referal_user_id, 50,'reffer_veri','refer_colleague',$referral_user_info->id,' earned 50 contest entries because their <a href="http://earetailprofessionals.cheddarlabs.com/overview/refer-a-colleague">referral</a> verified themselves!');
            //check for cred badge (5 successfull referrals for user)
              $num_of_ref = $this->refer_colleague_model->get_number_of_referrals($referral_user_info->referal_user_id);

              if($num_of_ref == 5){
                  account_add_badge($referral_user_info->referal_user_id, 'advocate');
              }
            }            
        }
        //get info about user refferal
        $referral_info = $this->refer_colleague_model->check_referral($verification->user->email,TRUE);
        if(!empty($referral_info)){
            $referral_user = $this->user_model->get_user_by_id($referral_info->referal_user_id);
            $data['ref_user'] = $referral_user;

            $referral_award = $this->refer_colleague_model->check_previous_referral($referral_info->id,$referral_info->referal_user_id);
            if(!$referral_award){
                $data['ref_award'] = $referral_user;
            }
        }             
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    // display edit form
    //$data['form']['name_input'] = $this->_create_text_input('name', $badge->name, 50, 20, 'text');    
    /*
    $data['form']['name_input'] = $this->_create_text_input('name', $verification->name, 50, 20, 'text');
    $data['form']['description_input'] = $this->_create_text_input('description', $verification->description, 50, 20, 'text');
    $data['form']['feed_description_input'] = $this->_create_text_input('feed_description', $verification->activity_feed_description, 250, 20, 'text');
*/
    $data['form']['verification_status_options'] = array('pending' => 'Pending', 'active' => 'Active', 'expired' => 'Expired', 'inactive' => 'Inactive');

    $data['index_page_num'] = $this->session->userdata('verification_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'verification/verification_leftbar', NULL, 'verification/verification_edit', NULL);
  }

  /**
   * delete a verification
   * @param  int  id of the item to be deleted
   */
  public function delete($id)
  {

//    if (!has_permission('manage_verification')) {
//      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
//    }
    //$verification = $this->verification_model->verification_load($id, FALSE);
    $result = $this->verification_model->verification_delete($id);
    if ($result) {
      $this->add_message('confirm', 'Verification  was deleted.');
    }
    else {
      $this->add_message('error', 'Failed to delete verification.');
    }
    //redirect('verification/index/' . $this->session->userdata('verification_index_page_num'));
    redirect('/profile');
  }
  
  /**
   * Image selection form
   * @param  string  asset ID
   * @param  string  training ID
   * @return string
   */
  public function ajax_image_chooser($asset_id = 0, $training_id = 0) {
    $result = FALSE;
    $messages = '';
    $content = '';

    $data = array();
    $data['message'] = $this->session->flashdata('message');
    $data['error'] = $this->session->flashdata('error');

    $attr = $this->input->post();
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $verification_id > 0) {
      $result = $this->verification_model->asset_update($field_id, $attr);
    } else {
      $result = TRUE;
    }

    $image = NULL;
    $asset_id = (int) $asset_id;
    if ($asset_id > 0) {
      $image = asset_load_item($asset_id);
    }
    $data['asset_id'] = $asset_id;
    $data['image'] = $image;

    $asset_category_id = 1; // default image category
    $data['asset_category_id'] = $asset_category_id;
    // build the config form
    $category_context = 'verification_default';
    $asset_categories = asset_list_categories(array('context' => $category_context));

    $options = array('' => ' -- select category -- ');
    foreach ($asset_categories as $c) {
      $options[$c->id] = $c->name;
    }
    $data['asset_categories'] = $options;

    $args = array();
    $args['asset_category_id'] = $asset_category_id;
    $data['media_upload_ui'] = asset_upload_ui($args);
    $images = asset_images_ui($args + array('single_selection' => 'ON'));
    $data['media_library_ui'] = $images['formatted'];
    $content = $this->load->view('verification_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }  
  
  /**
   * Updates a icon image
   */
  public function ajax_update_image($verification_id, $asset_id) {
    $id = (int) $verification_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->verification_model->update_icon_image($id,$asset_id);
        $messages = 'Verification image added.';
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Verification not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
  /**
   * Updates a big image image
   */
  public function ajax_update_big_image($verification_id, $asset_id) {
    $id = (int) $verification_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->verification_model->update_big_image($id,$asset_id);
        $messages = 'Verification image added.';
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Verification not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
    /* function for call model fuction to store sequence in database */

    public function ajax_save_verification_sequence() {

        // load array
        $sequence = explode('_', $_GET['order']);
        // load model
        $this->load->model('verification_model');
        // loop sequence...
        $count = 0;
        foreach ($sequence as $id) {
            if(!empty($id)){
              $this->verification_model->save_verification_sequence('verification', $id, ++$count);
            }
        }
    }
    
}

?>
