<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Target Controller
 *
 *
 * @package		HotCMS
 * @author		Tao Long
 * @copyright	Copyright (c) 2013, HotTomali.
 * @since		Version 3.0
 */

class Target extends HotCMS_Controller {
  /**
   * Class constructor sets all command things for each function.
   */
  function __construct() {
    parent::__construct();
    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }

    $this->load->config('target/target', TRUE);
    $this->load->model('target/target_model');
    $this->load->library('pagination');
    $this->load->helper("array");


    $this->module_url = $this->config->item('module_url', 'target');
    $this->module_header = "Manage Target";
    $this->add_new_text = $this->lang->line('hotcms_add_new') . ' ' . strtolower($this->lang->line("hotcms_target"));
    $this->tables = $this->config->item('tables', 'target');
  }

  /**
   * List rows in "target" table.
   * @param int $page_num
   */
  function index($page_num = 1) {
    if (!has_permission('manage_targets')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $data = $left_data = array();
    $data['module_url'] = $left_data['module_url'] = $this->module_url;
    $data['site_id'] = $left_data['site_id'] = $this->session->userdata("siteID");
    $data['module_header'] = $this->module_header;
    $left_data['add_new_text'] = $this->add_new_text;
    $data['java_script'] = 'modules/' . $this->module_url . '/js/target.js';

    // search/filter form
    $default_sort_by = "name"; // default field to sort by
    $default_per_page = 10; // default items to display per page
    if ($this->input->post()) {
      $sort_direction = $this->input->post('sort_direction');
      if (!in_array($sort_direction, array('asc', 'desc'))) {
        $sort_direction = 'asc';
      }
      $filters = array(
        'sort_by' => $this->input->post('sort_by') > '' ? $this->input->post('sort_by') : $default_sort_by,
        'sort_direction' => $this->input->post('sort_direction'),
        'per_page' => $this->input->post('per_page') > 0 ? $this->input->post('per_page') : $default_per_page
      );
      $this->session->set_userdata('target_filters', $filters);
      redirect('target');
    }
    $filters = $this->session->userdata('target_filters');

    if (!is_array($filters)) {
      $filters = array(
        'sort_by' => $default_sort_by,
        'sort_direction' => 'asc',
        'per_page' => $default_per_page
      );
    }
    $data['filters'] = $filters;

    $data['form']['per_page_options'] = list_page_options();
    $data['form']['hidden'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page']);
    $data['form']['hidden_modal'] = array('sort_by' => $filters['sort_by'], 'sort_direction' => $filters['sort_direction'], 'per_page' => $filters['per_page']);

    // paginate configuration
    $pagination_config = pagination_configuration();
    $pagination_config['base_url'] = $this->config->item('base_url') . $this->module_url . "/index/";
    $pagination_config['per_page'] = $filters['per_page'];
    $pagination_config['total_rows'] = $this->target_model->target_count($filters);
    $data["targets"] = $this->target_model->target_list($filters, $page_num, $pagination_config['per_page']);
    $this->pagination->initialize($pagination_config);
    $data['pagination'] = $this->pagination->create_links();
    $this->session->set_userdata('target_index_page_num', $page_num);

    $this->load_messages();
    self::loadBackendView($data, 'target/target_leftbar', $left_data, 'target/target', NULL);
  }

  /**
   * Ajax handler for updating jstree data of organizations and their stores.
   */
  function ajax_tree_refresh() {
    $result = array("stores_tree" => "", "organizations" => "", "stores" => "");
    $types = $this->input->post("types");
    $categories = $this->input->post("categories");
    $result["organizations"] = $this->input->post("organizations");
    $result["stores"] = $this->input->post("stores");
    if (( !empty($types)) OR ( !empty($categories))) {
      $eligible_organizations = $this->target_model->get_organizations($categories, $types);
      $selected_organizations = explode(",", $result["organizations"]);
      foreach ($selected_organizations as $key => $organization_id) {
        $eligible = FALSE;
        foreach ($eligible_organizations as $organization) {
          if ($organization_id == $organization->id) {
            $eligible = TRUE;
            break;
          }
        }
        if ( !$eligible) {
          unset($selected_organizations[$key]);
        }
      }
      $result["organizations"] = implode(",", $selected_organizations);
      $eligible_stores = $this->target_model->get_stores($categories, $types);
      $selected_stores = explode(",", $result["stores"]);
      foreach ($selected_stores as $key => $store_id) {
        $eligible = FALSE;
        foreach ($eligible_stores as $store) {
          if ($store_id == $store->id) {
            $eligible = TRUE;
            break;
          }
        }
        if ( !$eligible) {
          unset($selected_stores[$key]);
        }
      }
      $result["stores"] = implode(",", $selected_stores);
    }
    $result["stores_tree"] = $this->target_model->get_stores_jstree($types, $categories, $result["organizations"], $result["stores"]);
    echo json_encode($result);
  }

  /**
   * Create a target from blank.
   * Requires users must in ChaddarLabs/hotcms and has the permission.
   * Success saving the new target will redirect user to editing the new target.
   */
  function create() {
    if (!has_permission('manage_targets')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    if ($this->session->userdata("siteID") != 1) {
      $this->add_message('error', 'This function is for admin only.');
      redirect("target/index/".$this->session->userdata("target_index_page_num"));
    }
    $data = array();
    $data['module_header'] = "Create Target";
    $data['module_url'] = $this->module_url;
    $data['java_script'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.min.js";
    $data['css'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.css";
    $data['css'] .= " modules/".$this->module_url."/css/target.css";
    //TODO: validate unique order name
    $this->form_validation->set_rules('name', lang("hotcms_target")." ".lang("hotcms_name"), "trim|required");
    $this->form_validation->set_rules('site_id', lang("hotcms_for")." ".lang("hotcms_site"), 'trim|required');
    $this->form_validation->set_rules('description', lang('hotcms_description'), 'trim');
    $this->form_validation->set_rules('organizations', lang('hotcms_organizations'), 'trim');
    $this->form_validation->set_rules('stores', lang('hotcms_stores'), "trim");

    if ($this->form_validation->run()) {
      if ($this->_target_check()) {
        $target_id = $this->target_model->target_insert(elements(array("name", "site_id", "description", "types", "categories", "job_titles", "organizations", "stores"), $this->input->post()));
        if ($target_id > 0) {
          $this->add_message('confirm', 'New Target was created.');
          redirect('target/edit/' . $target_id);
        } else {
          $this->add_message("error", "Insert into database fail.");
        }
      } else {
        $this->add_message('error', "Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.");
      }
    }
    elseif (validation_errors() > '') {
      $errors = validation_errors();
      if ( !$this->_target_check()) {
        $errors .= "<p>Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.</p>";
      }
      $this->add_message('error', $errors);
    }

    #$data['form']["name_input"] = $this->_create_text_input("name", (($this->input->post("name") === FALSE) ? "" : $this->input->post("name")), 140, 20, 'text');
    $data['form']["name_input"] = $this->_create_text_input("name", (($this->input->post("name") === NULL) ? "" : $this->input->post("name")), 140, 20, 'text');
    $sites = $this->target_model->get_sites();
    $site_dropdown_options = array();
    foreach ($sites as $site) {
      $site_dropdown_options[$site->id] = $site->name;
    }
    $data["form"]["site_options"] = array("" => "") + $site_dropdown_options;
    $data["form"]["selected_site"] = $this->input->post("site_id");
    $data["form"]["description_input"] = $this->_create_text_input("description", (($this->input->post("description") === NULL) ? "" : $this->input->post("description")), 250, 20, 'text');
    $data["form"]["type_options"] = $this->target_model->get_type_options();
    $data["form"]["selected_types"] = $this->input->post("types");
    $data["form"]["category_options"] = $this->target_model->get_category_options();
    $data["form"]["selected_categories"] = $this->input->post("categories");
    $data["form"]["job_title_options"] = $this->target_model->get_job_title_options();
    $data["form"]["selected_job_titles"] = $this->input->post("job_titles");
    $data["form"]["hidden"] = array("organizations" => $this->input->post("organizations"), "stores" => $this->input->post("stores"));
    $data["form"]["stores_tree"] = $this->target_model->get_stores_jstree($this->input->post("types"), $this->input->post("categories"), $this->input->post("organizations"), $this->input->post("stores"));

    $data['index_page_num'] = $this->session->userdata('target_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'target/target_leftbar', NULL, 'target/target_create', NULL);
  }

  /**
   * Delete a target by id.
   * May abort if any active quiz or lab still using it.
   * @param int $target_id
   */
  function delete($target_id) {
    if (!has_permission('manage_targets')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $target = $this->target_model->target_load($target_id);
    $result = $this->target_model->target_delete($target_id);
    switch($result) {
      case 8:
        $this->add_message("error", "Failed to delete target ".$target->name.".");
        break;
      case 7:
        $this->add_message("error", "Failed to delete ".$target->name."'s targeted store.");
        break;
      case 6:
        $this->add_message("error", "Failed to delete ".$target->name."'s targeted organization.");
        break;
      case 5:
        $this->add_message("error", "Failed to delete ".$target->name."'s targeted job title.");
        break;
      case 4:
        $this->add_message("error", "Failed to delete ".$target->name."'s targeted organization category.");
        break;
      case 3:
        $this->add_message("error", "Failed to delete ".$target->name."'s targeted organization type.");
        break;
      case 2:
        $this->add_message("error", "Lab(s) using target ".$target->name." still active.");
        break;
      case 1:
        $this->add_message("error", "Quiz(zes) using target ".$target->name." still active.");
        break;
      case 0:
      default:
        $this->add_message("confirm", "Target ".$target->name." was deleted.");
    }
    redirect("target/index/".$this->session->userdata("target_index_page_num"));
  }

  /**
   * Same as creating from blank, but populating data from a existing target by id.
   * @param int $target_id
   */
  function duplicate($target_id) {
    if (!has_permission('manage_targets')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $target = $this->target_model->target_load($target_id, FALSE);
    if (empty($target)) {
      $this->add_message('error', "Fail to open required target for editing.");
      redirect("target/index/".$this->session->userdata("target_index_page_num"));
    }
    $data = array();
    $data['module_header'] = "Copy Target";
    $data['module_url'] = $left_data['module_url'] = $this->module_url;
    $data['site_id'] = $left_data['site_id'] = $this->session->userdata("siteID");
    $left_data['add_new_text'] = $this->add_new_text;
    $data["target_id"] = $target_id;
    $data['java_script'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.min.js";
    $data['css'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.css";
    $data['css'] .= " modules/".$this->module_url."/css/target.css";

    //TODO: validate unique order name
    $this->form_validation->set_rules('name', lang("hotcms_target")." ".lang("hotcms_name"), "trim|required");
    $this->form_validation->set_rules('site_id', lang("hotcms_for")." ".lang("hotcms_site"), 'trim|required');
    $this->form_validation->set_rules('description', lang('hotcms_description'), 'trim');
    $this->form_validation->set_rules('organizations', lang('hotcms_organizations'), 'trim');
    $this->form_validation->set_rules('stores', lang('hotcms_stores'), "trim");

    if ($this->form_validation->run()) {
      if ($this->_target_check()) {
        $new_target_id = $this->target_model->target_insert(elements(array("name", "site_id", "description", "types", "categories", "job_titles", "organizations", "stores"), $this->input->post()));
        if ($target_id > 0) {
          $this->add_message('confirm', 'New target was creatied by copy.');
          redirect('target/edit/' . $new_target_id);
        } else {
          $this->add_message("error", "Insert into database fail.");
        }
      } else {
        $this->add_message('error', "Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.");
      }
      if ($this->_target_check()) {
        if ($this->target_model->target_update($target_id, elements(array("name", "site_id", "description", "types", "categories", "job_titles", "organizations", "stores"), $this->input->post()))) {
          $target = $this->target_model->target_load($target_id, TRUE);
          $this->add_message("confirm", "Target ".$target->name." updated.");
        } else {
          $this->add_message("error", "Update target ".$this->input->post("name")." fail.");
        }
      } else {
        $this->add_message('error', "Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.");
      }
    }
    elseif (validation_errors() > '') {
      $errors = validation_errors();
      if ( !$this->_target_check()) {
        $errors .= "<p>Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.</p>";
      }
      $this->add_message('error', $errors);
    }

    $data['form']["name_input"] = $this->_create_text_input("name", (($this->input->post("name") === NULL) ? $target->name : $this->input->post("name")), 140, 20, 'text');
    $sites = $this->target_model->get_sites();
    $site_dropdown_options = array();
    foreach ($sites as $site) {
      $site_dropdown_options[$site->id] = $site->name;
    }
    $data["form"]["site_options"] = array("" => "") + $site_dropdown_options;
    $data["form"]["selected_site"] = (($this->input->post("site_id") === NULL) ? $target->site_id : $this->input->post("site_id"));
    $data["form"]["description_input"] = $this->_create_text_input("description", (($this->input->post("description") === NULL) ? $target->description : $this->input->post("description")), 250, 20, 'text');
    $data["form"]["type_options"] = $this->target_model->get_type_options();
    $data["form"]["selected_types"] = $this->input->post("types");
    if (($data["form"]["selected_types"] === FALSE) && ( !empty($target->types))) {
      $data["form"]["selected_types"] = array();
      foreach ($target->types as $type) {
        if (( !empty($type)) && ( !empty($type->organization_type_id))) {
          $data["form"]["selected_types"][] = $type->organization_type_id;
        }
      }
    }
    $data["form"]["category_options"] = $this->target_model->get_category_options();
    $data["form"]["selected_categories"] = $this->input->post("categories");
    if (($data["form"]["selected_categories"] === FALSE) && ( !empty($target->categories))) {
      $data["form"]["selected_categories"] = array();
      foreach ($target->categories as $category) {
        if (( !empty($category)) && ( !empty($category->organization_category_id))) {
          $data["form"]["selected_categories"][] = $category->organization_category_id;
        }
      }
    }
    $data["form"]["job_title_options"] = $this->target_model->get_job_title_options();
    $data["form"]["selected_job_titles"] = $this->input->post("job_titles");
    if (($data["form"]["selected_job_titles"] === FALSE) && ( !empty($target->job_titles))) {
      $data["form"]["selected_job_titles"] = array();
      foreach ($target->job_titles as $job_title) {
        if (( !empty($job_title)) && ( !empty($job_title->job_title_id))) {
          $data["form"]["selected_job_titles"][] = $job_title->job_title_id;
        }
      }
    }
    $default_organizations = $this->input->post("organizations");
    if (($default_organizations === FALSE) && ( !empty($target->organizations))) {
      $default_organizations = "";
      foreach ($target->organizations as $organization) {
        if (( !empty($organization)) && ( !empty($organization->organization_id))) {
          $default_organizations .= $organization->organization_id.",";
        }
      }
      if ( !empty($default_organizations)) {
        $default_organizations = trim($default_organizations, ",");
      }
    }
    $default_stores = $this->input->post("stores");
    if (($default_stores === FALSE) && ( !empty($target->stores))) {
      $default_stores = "";
      foreach ($target->stores as $store) {
        if (( !empty($store)) && ( !empty($store->store_id))) {
          $default_stores .= $store->store_id.",";
        }
      }
      if ( !empty($default_stores)) {
        $default_stores = trim($default_stores, ",");
      }
    }
    $data["form"]["hidden"] = array("organizations" => $default_organizations, "stores" => $default_stores);
    $data["form"]["stores_tree"] = $this->target_model->get_stores_jstree($data["form"]["selected_types"], $data["form"]["selected_categories"], $default_organizations, $default_stores);

    $data['index_page_num'] = $this->session->userdata('target_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'target/target_leftbar', $left_data, 'target/target_duplicate', NULL);
  }

  /**
   * Editing a existing target by id.
   * Success will prompt a updated confirmation message.
   * @param int $target_id
   */
  function edit($target_id) {
    if (!has_permission('manage_targets')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }
    $target = $this->target_model->target_load($target_id, FALSE);
    if (empty($target)) {
      $this->add_message('error', "Fail to open required target for editing.");
      redirect("target/index/".$this->session->userdata("target_index_page_num"));
    }
    $data = array();
    $data['module_header'] = $this->module_header;
    $data['module_url'] = $left_data['module_url'] = $this->module_url;
    $data['site_id'] = $left_data['site_id'] = $this->session->userdata("siteID");
    $left_data['add_new_text'] = $this->add_new_text;
    $data["target_id"] = $target_id;
    $data['java_script'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.min.js";
    $data['css'] = "modules/".$this->module_url."/js/select2-2.1.0/select2.css";
    $data['css'] .= " modules/".$this->module_url."/css/target.css";

    //TODO: validate unique order name
    $this->form_validation->set_rules('name', lang("hotcms_target")." ".lang("hotcms_name"), "trim|required");
    $this->form_validation->set_rules('site_id', lang("hotcms_for")." ".lang("hotcms_site"), 'trim|required');
    $this->form_validation->set_rules('description', lang('hotcms_description'), 'trim');
    $this->form_validation->set_rules('organizations', lang('hotcms_organizations'), 'trim');
    $this->form_validation->set_rules('stores', lang('hotcms_stores'), "trim");

    if ($this->form_validation->run()) {
      if ($this->_target_check()) {
        if ($this->target_model->target_update($target_id, elements(array("name", "site_id", "description", "types", "categories", "job_titles", "organizations", "stores"), $this->input->post()))) {
          $target = $this->target_model->target_load($target_id, TRUE);
          $this->add_message("confirm", "Target ".$target->name." updated.");
        } else {
          $this->add_message("error", "Update target ".$this->input->post("name")." fail.");
        }
      } else {
        $this->add_message('error', "Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.");
      }
    }
    elseif (validation_errors() > '') {
      $errors = validation_errors();
      if ( !$this->_target_check()) {
        $errors .= "<p>Must choose one of Types, or Categories, or Job Titles, or Organizations, or Stores.</p>";
      }
      $this->add_message('error', $errors);
    }

    $data['form']["name_input"] = $this->_create_text_input("name", (($this->input->post("name") === NULL) ? $target->name : $this->input->post("name")), 140, 20, 'text');
    $sites = $this->target_model->get_sites();
    $site_dropdown_options = array();
    foreach ($sites as $site) {
      $site_dropdown_options[$site->id] = $site->name;
    }
    $data["form"]["site_options"] = array("" => "") + $site_dropdown_options;
    $data["form"]["selected_site"] = (($this->input->post("site_id") === NULL) ? $target->site_id : $this->input->post("site_id"));
    $data["form"]["description_input"] = $this->_create_text_input("description", (($this->input->post("description") === NULL) ? $target->description : $this->input->post("description")), 250, 20, 'text');
    $data["form"]["type_options"] = $this->target_model->get_type_options();
    $data["form"]["selected_types"] = $this->input->post("types");
    if (($data["form"]["selected_types"] === FALSE) && ( !empty($target->types))) {
      $data["form"]["selected_types"] = array();
      foreach ($target->types as $type) {
        if (( !empty($type)) && ( !empty($type->organization_type_id))) {
          $data["form"]["selected_types"][] = $type->organization_type_id;
        }
      }
    }
    $data["form"]["category_options"] = $this->target_model->get_category_options();
    $data["form"]["selected_categories"] = $this->input->post("categories");
    if (($data["form"]["selected_categories"] === FALSE) && ( !empty($target->categories))) {
      $data["form"]["selected_categories"] = array();
      foreach ($target->categories as $category) {
        if (( !empty($category)) && ( !empty($category->organization_category_id))) {
          $data["form"]["selected_categories"][] = $category->organization_category_id;
        }
      }
    }
    $data["form"]["job_title_options"] = $this->target_model->get_job_title_options();
    $data["form"]["selected_job_titles"] = $this->input->post("job_titles");
    if (($data["form"]["selected_job_titles"] === FALSE) && ( !empty($target->job_titles))) {
      $data["form"]["selected_job_titles"] = array();
      foreach ($target->job_titles as $job_title) {
        if (( !empty($job_title)) && ( !empty($job_title->job_title_id))) {
          $data["form"]["selected_job_titles"][] = $job_title->job_title_id;
        }
      }
    }
    $default_organizations = $this->input->post("organizations");
    if (($default_organizations === FALSE) && ( !empty($target->organizations))) {
      $default_organizations = "";
      foreach ($target->organizations as $organization) {
        if (( !empty($organization)) && ( !empty($organization->organization_id))) {
          $default_organizations .= $organization->organization_id.",";
        }
      }
      if ( !empty($default_organizations)) {
        $default_organizations = trim($default_organizations, ",");
      }
    }
    $default_stores = $this->input->post("stores");
    if (($default_stores === FALSE) && ( !empty($target->stores))) {
      $default_stores = "";
      foreach ($target->stores as $store) {
        if (( !empty($store)) && ( !empty($store->store_id))) {
          $default_stores .= $store->store_id.",";
        }
      }
      if ( !empty($default_stores)) {
        $default_stores = trim($default_stores, ",");
      }
    }
    $data["form"]["hidden"] = array("organizations" => $default_organizations, "stores" => $default_stores);
    $data["form"]["stores_tree"] = $this->target_model->get_stores_jstree($data["form"]["selected_types"], $data["form"]["selected_categories"], $default_organizations, $default_stores);

    $data['index_page_num'] = $this->session->userdata('target_index_page_num');

    $this->load_messages();
    self::loadBackendView($data, 'target/target_leftbar', $left_data, 'target/target_edit', NULL);
  }

  /**
   * Check has anything be selected as a target.
   * @return boolean
   */
  function _target_check() {
    $fields = array("types", "categories", "job_titles", "organizations", "stores");
    $valid = FALSE;
    foreach ($fields as $field) {
      if ($this->form_validation->required($this->input->post($field)) === TRUE) {
        $valid = TRUE;
        break;
      }
    }
    if ($valid === FALSE) {
      return FALSE;
    }
    return TRUE;
  }
}
