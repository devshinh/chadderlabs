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
    //$this->load->helper('site/site');
    $this->load->helper('array');
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

    // search/filter form
    $default_sort_by = 'create_timestamp'; // default field to sort by
    $default_per_page = 10; // default items to display per page
    if ($this->input->post()) {
      $sort_direction = $this->input->post('sort_direction');
      if (!in_array($sort_direction, array('asc', 'desc'))) {
        $sort_direction = 'DESC';
      }
      $filters = array(
        'sort_by' => $this->input->post('sort_by') > '' ? $this->input->post('sort_by') : $default_sort_by,
        'sort_direction' => $this->input->post('sort_direction'),
        'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page,
      );
    }
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'DESC',
        'per_page' => $default_per_page,
      );
    }
    $data['filters'] = $filters;

    $data['form']['per_page_options'] = list_page_options();
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page'], 'keyword' => $filters['keyword']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . '/index/';
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->draw_model->draw_count($filters);
    $data['draws'] = $this->draw_model->list_draws($filters, $page_num, $pagination_config['per_page']);

    // paginate
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('draw_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw', NULL);
  }
  
  public function list_winners($draw_history_id = 0)
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

    $draw_info = $this->draw_model->draw_details_load($draw_history_id);
    $draw_info->draw_creator = account_get_user($draw_info->author_id);
    $draw_info->draw_editor = account_get_user($draw_info->editor_id);

    $data['form']['draw_description_input'] = $this->_create_text_input('draw_description', $draw_info->description, 140, 20, 'text');
    $data['draw_details'] = $draw_info;
    $data['draws'] = $this->draw_model->list_winners($draw_history_id);

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_winners', NULL);
  }

  /**
   * list all items
   * @param  int  page number
  public function old_draw_list($page_num = 1)
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
    $default_sort_by = 'create_timestamp'; // default field to sort by
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
    }
    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'desc',
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
    $draws = $this->draw_model->draw_list($filters, $page_num, $pagination_config['per_page']);
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
   */

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
    
    //$data['css'] = "hotcms/asset/js/jstree/themes/htc/style.css";   
    $data['form']['draw_type_hide'] = TRUE;
    $data['form']['draw_monthly_hide'] = TRUE;
    $data['form']['draw_name_hide'] = TRUE;
    $data['form']['draw_custom_hide'] = TRUE;
    $draw_step = $this->input->post('draw_step') === FALSE ? 0 : $this->input->post('draw_step');
    $data['form']['draw_type_value'] = $this->input->post('draw_type') === FALSE ? "life" : $this->input->post('draw_type');
    $data['form']['draw_monthly_month_value'] = $this->input->post('draw_monthly_month') === FALSE ? 1 : $this->input->post('draw_monthly_month');
    $data['form']['draw_monthly_year_value'] = $this->input->post('draw_monthly_year') === FALSE ? date("Y") : $this->input->post('draw_monthly_year');
    
    $data['form']['draw_custom_start_value'] = $this->input->post('datepicker_begining') === FALSE ? 1 : $this->input->post('datepicker_begining');
    $data['form']['draw_custom_end_value'] = $this->input->post('datepicker_closing') === FALSE ? 1 : $this->input->post('datepicker_closing');
    
    $draw_description_value = $this->input->post('draw_description');
    
    $data['form']['draw_monthly_months_array'] = months_list();
    $this->load->model("site/site_model");
    $data['form']['draw_monthly_years_array'] = years_list(date("Y", $this->site_model->get_site_by_id($this->session->userdata("siteID"))->create_timestamp));
    $draw_name_default = "New Draw";
    if (strcasecmp($data["form"]["draw_type_value"], "monthly") === 0) {
      $draw_name_default = $data['form']['draw_monthly_months_array'][$data['form']['draw_monthly_month_value']]." ".$data['form']['draw_monthly_years_array'][$data['form']['draw_monthly_year_value']]." Draw";
    } elseif ($draw_step == 2) {
      $draw_name_default = $this->input->post('draw_name');
    }
    if (strcasecmp($data["form"]["draw_type_value"], "custom") === 0){
      $data["number_of_eligible_draws"] = $this->draw_model->get_eligible_entries_for_custom_range($data['form']['draw_custom_start_value'], $data['form']['draw_custom_end_value'],TRUE);
    }else {
      $data["number_of_eligible_draws"] = $this->draw_model->get_eligible_entries($data['form']['draw_type_value'], $data['form']['draw_monthly_month_value'], $data['form']['draw_monthly_year_value']);  
    }
    if ($this->input->post("button_back") === FALSE) {
      switch ($draw_step) {
        case 2: // Last step
          $this->form_validation->set_rules('draw_name', strtolower(lang('hotcms_name')), "trim|required|is_unique[user_draw_history.name]");
          $this->form_validation->set_rules('draw_winner_numer', strtolower(lang('hotcms_draw_winner_numer')), "trim|required|is_natural_no_zero|less_than_or_equal[".$data["number_of_eligible_draws"]."]");
          break;
        case 1: // example of draw-types require extra user input
            if (strcasecmp($data["form"]["draw_type_value"], "monthly") === 0) {
                $this->form_validation->set_rules('draw_monthly_year', strtolower(lang('hotcms_year')), "trim|required");
                $this->form_validation->set_rules('draw_monthly_month', strtolower(lang('hotcms_month')), "trim|required|monthly_type_draw_check");
            }elseif (strcasecmp($data["form"]["draw_type_value"], "custom") === 0) {
                $this->form_validation->set_rules('datepicker_begining', strtolower('start date'), "trim|required");
                $this->form_validation->set_rules('datepicker_closing', strtolower('end date'), "trim|required");            
            }
          break;
        case 0: // First step, choose draw type
        default:
          $this->form_validation->set_rules('draw_type', strtolower(lang('hotcms_type')), "trim|required|life_type_draw_check");
      }
    } else {
      $this->form_validation->set_rules('draw_type', strtolower(lang('hotcms_type')), "trim"); // so the form_validation->run() returns TRUE
    }

    if ($this->form_validation->run()) {
      if ($this->input->post("button_back") !== FALSE) { // User clicked "BACK" button
        if ((strcasecmp($data["form"]["draw_type_value"], "life") === 0) && ($draw_step == 2)) {
          $draw_step = 0;
        } else {
          $draw_step--;
        }
      } elseif ($draw_step == 2) { // User completed draw creation
        if (strcasecmp($data["form"]["draw_type_value"], "custom") === 0) { 
          $users_entries = $this->draw_model->get_eligible_entries_for_custom_range($data['form']['draw_custom_start_value'], $data['form']['draw_custom_end_value'], FALSE);
        }else{
          $users_entries = $this->draw_model->get_eligible_entries($data['form']['draw_type_value'], $data['form']['draw_monthly_month_value'], $data['form']['draw_monthly_year_value'], FALSE);
        }
        $entries = array();
        foreach ($users_entries as $user_entries) {
          for ($i = 0; $i < $user_entries->draws; $i++) {
            $entries[] = array("user_id" => $user_entries->user_id, "ref_id" => $user_entries->id);
          }
        }
        $number_of_winners = $this->input->post("draw_winner_numer");
        $winning_entries = array_rand($entries, $number_of_winners);
        $winner_id = 0;
        foreach ($winning_entries as $winning_entry) {
          $winner_id = $this->draw_model->insert_winner($draw_name_default, $entries[$winning_entry]);
          if ($winner_id === FALSE) {
            $this->add_message("error", "Fail to select entry #".$entries[$winning_entry]->ref_id." of user #".$entries[$winning_entry]->user_id." as a winner.");
            break;
          }
        }
        if ($winner_id > 0) {
          $history_id = $this->draw_model->insert_history($draw_name_default, $data['form']['draw_type_value'], $number_of_winners, $data['form']['draw_monthly_month_value'], $data['form']['draw_monthly_year_value'],$data['form']['draw_custom_start_value'],$data['form']['draw_custom_end_value'],$draw_description_value);
          if ($history_id !== FALSE) {
            $this->add_message("confirm", "Winners are selected.");
          } else {
            $this->add_message("error", "Fail to save draw history");
          }
          redirect("draw");
        }
      } else { // Define next step of draw
        if (strcasecmp($data["form"]["draw_type_value"], "life") === 0) {
          $draw_step = 2;
        } else {
          $draw_step++;
        }
      }
    } elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    switch($draw_step) {
      case -1:
        redirect("draw");
      case 2:
        $data['form']['draw_name_hide'] = FALSE;
        break;
      case 1:
          if (strcasecmp($data["form"]["draw_type_value"], "monthly") === 0) {
        $data['form']['draw_monthly_hide'] = FALSE;
        $data['form']['draw_custom_hide'] = TRUE;
          }elseif (strcasecmp($data["form"]["draw_type_value"], "custom") === 0) {
             $data['form']['draw_monthly_hide'] = TRUE;
             $data['form']['draw_custom_hide'] = FALSE;
          }
        break;
      case 0:
      default:
        $data['form']['draw_type_hide'] = FALSE;
        break;
    }
    $this->load->helper("date");
    $data['java_script'] = 'modules/' . $this->module_url . '/js/draw_create.js';
    $data['form']['draw_types_array'] = array("life" => "Life", "monthly" => "Monthly", "custom" => "Custom");
    $data['form']['draw_name_input'] = $this->_create_text_input('draw_name', $draw_name_default, 140, 20, 'text');
    $data['form']['draw_description_input'] = $this->_create_text_input('draw_description', $draw_description_value, 140, 20, 'text');
    $data['form']['draw_winner_numer_input'] = $this->_create_text_input('draw_winner_numer', (($this->input->post('draw_winner_numer') === FALSE) ? 20 : $this->input->post('draw_winner_numer')), 140, 20, 'text');
    $data['form']["draw_step_hidden"] = $this->_create_hidden_input("draw_step", $draw_step);
    
    $data['form']['start_input'] = $this->_create_text_input('datepicker_begining', $this->input->post('datepicker_begining'), 50, 20, 'text');
    $data['form']['end_input'] = $this->_create_text_input('datepicker_closing', $this->input->post('datepicker_closing'), 50, 20, 'text');
    
    //$data['sites'] = get_sites_array();
    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_winner_create', NULL);
  }

  /**
   * edit draw
   * @param  int  $id
   * @param  int  page number
   */
  public function edit_winner($id, $page_num = 1)
  {
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = "Edit draw winner";
    
    //$data['java_script'] = 'modules/' . $this->module_url . '/js/draw_edit.js';

    //$this->load->model('user/user_model');

    $data['draw_id'] = $id;
    $draw_winner = $this->draw_model->draw_load($id, FALSE);
    
    $draw_winner->user_info =  account_get_user($draw_winner->user_id);
        
    $data['currentItem'] = $draw_winner;

    $this->form_validation->set_rules('feed_description', 'activity feed description', 'trim|required');


    if ($this->form_validation->run()) {
      // update draw
      $this->draw_model->draw_winner_update($id, $this->input->post());     
      // reload
        $draw_winner = $this->draw_model->draw_load($id, FALSE);
        $draw_winner->user_info =  account_get_user($draw_winner->user_id);
        if($draw_winner->verified == 1 ){
            //TODO add check for duplicity
            //activity feed update 
            account_add_points($draw_winner->user_id, 0, 'draw_winner','user_draw_winner', $draw_winner->id, $draw_winner->feed_description);
        }
        $data['currentItem'] = $draw_winner;
        $this->add_message('confirm', 'Draw winner was updated.');
    }
    elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    // display edit form
    $data['form']['feed_description_input'] = $this->_create_text_input('feed_description', $draw_winner->feed_description, 250, 20, 'text');

     $data['form']['verified_input'] = array(
         'name'        => 'verified',
         'id'          => 'verified',
         'value'       => 'accept',
         'checked'     => $draw_winner->verified==1,
         'style'       => '',
     ); 
     
    $data['index_page_num'] = $this->session->userdata('draw_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_winner_edit', NULL);
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
   */
  
  /**
   * Updates a icon image
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
   */
  
  /**
   * Updates a big image image
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
  
   */
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
    
  /**
   * edit draw details (description)
   * @param  int  $id
   * @param  int  page number
   */
  public function edit_draw($id, $page_num = 1)
  {   
    if (!has_permission('manage_draw')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = array();
    $data['module_url'] = $this->module_url;
    $data['module_header'] = $this->module_header;
    $data['add_new_text'] = $this->add_new_text;

      
      $this->form_validation->set_rules('draw_description', strtolower(lang('hotcms_description')), "trim|required");
      if ($this->form_validation->run()) {
        $description = $this->input->post('draw_description');
        $this->draw_model->draw_details_update($id,$description);
          // reload
   
      $this->add_message('confirm', 'Draw was updated.');    
      }elseif (validation_errors() > '') {
      $this->add_message('error', validation_errors());
    }
    $draw_info = $this->draw_model->draw_details_load($id);
    $draw_info->draw_creator = account_get_user($draw_info->author_id);
    $draw_info->draw_editor = account_get_user($draw_info->editor_id);

    $data['form']['draw_description_input'] = $this->_create_text_input('draw_description', $draw_info->description, 140, 20, 'text');
    $data['draw_details'] = $draw_info;
    $data['draws'] = $this->draw_model->list_winners($id);
    $this->load_messages();
    self::loadBackendView($data, 'draw/draw_leftbar', NULL, 'draw/draw_winners', NULL);
  } 
}

?>
