<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draw Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	        Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */
class Draw extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }

    $this->load->config('draw/draw', TRUE);
    $this->load->model('draw/draw_model');
    $this->load->library('pagination');

    
    $this->module_url = $this->config->item('module_url', 'draw');
    $this->module_header = 'Pick draw winner';
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line('hotcms_draw'));
    
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'product');
    
    $this->load->helper('account/account');
  }

  /**
   * list all items
   * @param  int  page number
   */
  public function index($page_num = 1)
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;
    //$data['java_script'] = 'modules/' . $this->module_url . '/js/draw.js';

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
      $this->session->set_userdata('draw_filters', $filters);
      redirect('draw');
    }
    $filters = $this->session->userdata('draw_filters');
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page,
        'keyword' => '',
        'country' => '',
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
    }
    if ($separator == false)
      $active_filters.= 'None';

    $data['active_filters'] = $active_filters;

    //$data['form']['country_code_options'] = array('all' => 'All') + list_country_array();
    $data['form']['country_code_options'] = list_country_array();
    $data['form']['status_options'] = array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Closed');
    $data['form']['per_page_options'] = list_page_options();
    $data['form']['keyword_input'] = $this->_create_text_input('keyword', $filters['keyword'], 50, 20, 'text');
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->draw_model->draw_count($filters);
    $draws = $this->draw_model->draw_list($filters, TRUE, $page_num, $pagination_config['per_page']);
    //load user info
    foreach($draws as $row){
        $row->user_info =  account_get_user($row->user_id);
    }
    $data['draws'] = $draws;
    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('draw_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw', NULL);
  }

  /**
   * creates new draw
   */
  public function create()
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_header'] = "Pick draw winner";
    $data['module_url'] = $this->module_url;
    $data['add_new_text'] = $this->lang->line('hotcms_add_new') . " winner draw";

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('note', 'note', 'trim|required');

    if ($this->form_validation->run()) {
      //make the array for all active contest entries -> each entry is one row
        $curent_draws_array = $this->draw_model->get_active_draws();

        $contest_draws = array();
        $index = 0;
        foreach($curent_draws_array as $user_draw){
            for($i=0; $i <= $user_draw->draws; $i++){
                $index++;
                $contest_draws[$index] = array('user_id' => $user_draw->user_id, 'ref_id' => $user_draw->id);
            }
        }        
        
        //pick winner
        $winner = random_element($contest_draws);
        
      $draw_winner_id = $this->draw_model->insert($this->input->post(), $winner);
      if ($draw_winner_id > 0) {
        $this->add_message('confirm', 'Winner was picked.');
        redirect('draw');
      }
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }

    $data['form']['name_input'] = $this->_create_text_input('name', $this->input->post('name'), 50, 20, 'text');
    $data['form']['note_input'] = $this->_create_text_input('note', $this->input->post('note'), 50, 20, 'text');
   
    //$pagination_config = pagination_configuration();
    $draws_sum = $this->draw_model->get_active_draws('sum');
    
    if(!empty($draws_sum)){
       $data['curent_draws_sum'] = $draws_sum;
    }else {
        $data['curent_draws_sum'] = 0;
    }
            
    
                 
    //var_dump($data['curent_draws_array']);
    $data['index_page_num'] = $this->session->userdata('draw_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_winner_create', NULL);
  }

  /**
   * edit draw
   * @param  int  $id
   * @param  int  page number
   */
  public function edit($id, $page_num = 1)
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit draw";
    
    $data['java_script'] = 'modules/' . $this->module_url . '/js/draw_edit.js';

    //$this->load->model('user/user_model');

    $data['draw_id'] = $id;
    $draw = $this->draw_model->draw_load($id, FALSE);
    
    if($draw->icon_image_id != 0){  
      $drawItems['icon'] = asset_load_item($draw->icon_image_id);
    }
    if($draw->big_image_id != 0){  
      $drawItems['hover'] = asset_load_item($draw->big_image_id);
    }    
    if (!empty($drawItems)){
      $draw->items = $drawItems;
    }else{
      $draw->items = '';  
    }
    $data['currentItem'] = $draw;

    $this->form_validation->set_rules('name', strtolower(lang('hotcms_name')), 'trim|required');
    $this->form_validation->set_rules('description', strtolower(lang('hotcms_description')), 'trim|required');
    $this->form_validation->set_rules('feed_description', 'activity feed description', 'trim|required');
    $this->form_validation->set_rules('award_amount', 'award amount', 'trim|required');
    $this->form_validation->set_rules('award_type', 'award type', 'trim|required');

    

    //$data['selected_country'] = $_POST ? $this->input->post('country_code') : $data['currentItem']->country_code;

    if ($this->form_validation->run()) {
      // update draw
      $this->draw_model->draw_update($id, $this->input->post());
      // reload
        $draw = $this->draw_model->draw_load($id, FALSE);

        if($draw->icon_image_id != 0){  
          $drawItems['icon'] = asset_load_item($draw->icon_image_id);
        }
        if($draw->big_image_id != 0){  
          $drawItems['hover'] = asset_load_item($draw->big_image_id);
        }    
        if (!empty($drawItems)){
          $draw->items = $drawItems;
        }else{
          $draw->items = '';  
        }
        
        $data['currentItem'] = $draw;
        $this->add_message('confirm', 'Draw was updated.');
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    // display edit form
    $data['form']['name_input'] = $this->_create_text_input('name', $draw->name, 50, 20, 'text');
    $data['form']['description_input'] = $this->_create_text_input('description', $draw->description, 50, 20, 'text');
    $data['form']['feed_description_input'] = $this->_create_text_input('feed_description', $draw->activity_feed_description, 250, 20, 'text');

    $data['form']['draw_status_options'] = array('0' => 'Draft', '1' => 'Active', '2' => 'Archived');
    
    $data['form']['award_type_options'] = array('points' => 'Points', 'draws' => 'Draws');
    $data['form']['award_amount_input'] = $this->_create_text_input('award_amount', $draw->award_amount, 50, 20, 'text');
    
    $data['index_page_num'] = $this->session->userdata('draw_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_edit', NULL);
  }

  /**
   * delete a draw
   * @param  int  id of the item to be deleted
   */
  public function delete($id)
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $draw = $this->draw_model->draw_load($id, FALSE);
    $result = $this->draw_model->draw_delete($id);
    if ($result) {
      $this->add_message('confirm', 'Draw ' . $draw->name . ' was deleted.');
    }
    else {
      $this->add_message('error', 'Failed to delete draw ' . $draw->name . '.');
    }
    redirect('draw/index/' . $this->session->userdata('draw_index_page_num'));
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
    if (!empty($attr) && array_key_exists('asset_id', $attr) && $attr['asset_id'] > 0 && $draw_id > 0) {
      $result = $this->draw_model->asset_update($field_id, $attr);
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
    $category_context = 'draw_default';
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
    $content = $this->load->view('draw_image_chooser', $data, true);

    $json = array('result' => $result, 'messages' => $messages, 'content' => $content);
    echo json_encode($json);
  }  
  
  /**
   * Updates a icon image
   */
  public function ajax_update_image($draw_id, $asset_id) {
    $id = (int) $draw_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->draw_model->update_icon_image($id,$asset_id);
        $messages = 'Draw image added.';
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Draw not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
  /**
   * Updates a big image image
   */
  public function ajax_update_big_image($draw_id, $asset_id) {
    $id = (int) $draw_id;
    $asset_id = (int) $asset_id;
    $result = FALSE;
    $messages = '';
    if ($id > 0) {
      
      
      try {
        $result = $this->draw_model->update_big_image($id,$asset_id);
        $messages = 'Draw hover image added.';
      } catch (Exception $e) {
        $messages = 'There was an error when trying to update image: ' . $e->getMessage();
      }
    } else {
      $messages = 'Draw not found.';
    }
    $json = array('result' => $result, 'messages' => $messages);
    echo json_encode($json);
  }
  
    /* function for call model fuction to store sequence in database */

    public function ajax_save_draw_sequence() {

        // load array
        $sequence = explode('_', $_GET['order']);
        // load model
        $this->load->model('draw_model');
        // loop sequence...
        $count = 0;
        foreach ($sequence as $id) {
            if(!empty($id)){
              $this->draw_model->save_draw_sequence('draw', $id, ++$count);
            }
        }
    }
    
}

?>
