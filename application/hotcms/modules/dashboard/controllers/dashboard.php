<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard Controller
 *
 * @package		HotCMS
 * @author		Jan Antl
 * @copyright	Copyright (c) 2011, HotTomali.
 * @since		Version 3.0
 */
class Dashboard extends HotCMS_Controller {

  public function __construct()
  {
    parent::__construct();

    // check permission
    if (!($this->ion_auth->logged_in())) {
      $this->session->set_userdata('redirect_to', $this->uri->uri_string());
      redirect($this->config->item('login_page'));
    }
    if (!has_permission('admin_area')) {
      show_error($this->lang->line('hotcms_error_insufficient_privilege'));
    }

    $this->load->config('dashboard', TRUE);
    $this->load->model('dashboard_model');

    $this->module_url = $this->config->item('module_url', 'dashboard');
    $this->module_header = $this->lang->line('hotcms_dashboard');
    $this->add_new_text = $this->lang->line('hotcms_add_new') . " " . $this->lang->line('hotcms_module');
    $this->java_script = 'modules/' . $this->module_url . '/js/' . $this->config->item('js', 'dashboard');
    $this->css = 'modules/' . $this->module_url . '/css/' . $this->config->item('css', 'dashboard');
  }

  public function index()
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'add_new_text' => $this->add_new_text,
      'css' => $this->css,
      'java_script' => $this->java_script,
    );

    // load avaliable sites for the current admin user
    $user_id = $this->session->userdata('user_id');
    $data['aSite'] = $this->permission->get_admin_sites($user_id);

    // if submit... assign a new site id
    if ($this->input->post('cboSite') > '' || $this->input->post('cboSite_global') > '') {
      $site_id = (int) ($this->input->post('cboSite'));
      if ($this->input->post('cboSite_global') > '') {
        $site_id = (int) ($this->input->post('cboSite_global'));
      }
      // one more step of validation
      if (!array_key_exists($site_id, $data['aSite'])) {
        die('Sorry but there is an error in the site configuration.');
      }
      $this->session->set_userdata('siteID', $site_id);
      $this->session->set_userdata('siteName', $data['aSite'][$site_id]->name);
      $this->session->set_userdata('siteURL', $data['aSite'][$site_id]->domain);
      $this->session->set_userdata('sitePath', $data['aSite'][$site_id]->path);
      redirect('/');
    }

    if ($this->input->post('back_url') > '') {
      $redirect_to = substr($this->input->post('back_url'), 7);
      redirect($redirect_to);
    }
    $this->load_messages();
    self::loadBackendView($data, 'dashboard/dashboard_leftbar', NULL, 'dashboard/dashboard');
  }

  /**
   * diaplay the Lab Analysis Dashboard
   */
  public function analysis()
  {
    $data = array(
      'module_url' => $this->module_url,
      'module_header' => $this->module_header,
      'css' => $this->css,
      'java_script' => $this->java_script,
    );
    $this->load->model("site/site_model");
    $site = $this->site_model->get_site_by_id($this->session->userdata('siteID'));
    $data['start_date'] = date("m/d/Y", $site->create_timestamp);
    $data['end_date'] = date("m/d/Y", time()+86400);
    $analysis_parameters = array('site_id' => $site->id, 'first_date' => $data['start_date']);
    $this->load->library("dashboard/analysis", $analysis_parameters);
    $this->load->library("form_validation");
    $this->form_validation->set_rules('from_filter_range', 'From Date', 'xss_clean');
    $this->form_validation->set_rules('to_filter_range', 'To Date', 'xss_clean');
    $this->form_validation->set_rules('country', 'Country', 'xss_clean');
    $this->form_validation->set_rules('retailer', 'Retailer', 'xss_clean');
    $this->form_validation->set_rules('province', 'Province', 'xss_clean');
    $this->form_validation->set_rules('city', 'City', 'xss_clean');
    $this->form_validation->set_rules('store', 'Store', 'xss_clean');
    if ($this->input->post('reset') === FALSE) {
      if ($this->form_validation->run() !== FALSE) {
        if ($this->input->post('from_filter_range') !== FALSE) {
          $analysis_parameters['from_date'] = $this->input->post('from_filter_range');
          if (empty($analysis_parameters['from_date'])) {
            unset($analysis_parameters['from_date']);
          } else {
            $data['start_date'] = $analysis_parameters['from_date'];
          }
        }
        if ($this->input->post('to_filter_range') !== FALSE) {
          $analysis_parameters['to_date'] = $this->input->post('to_filter_range');
          if (empty($analysis_parameters['to_date'])) {
            unset($analysis_parameters['to_date']);
          } else {
            $data['end_date'] = $analysis_parameters['to_date'];
          }
        }
        if ($this->input->post('country') !== FALSE) {
          $analysis_parameters['country'] = $this->input->post('country');
          if (empty($analysis_parameters['country'])) {
            unset($analysis_parameters['country']);
          } else {
            $query = $this->db->get_where("country", array("country_code" => $analysis_parameters['country']));
            $data['country'] = $query->row()->country;
          }
        }
        if ($this->input->post('retailer') !== FALSE) {
          $analysis_parameters['retailer'] = $this->input->post('retailer');
          if (empty($analysis_parameters['retailer'])) {
            unset($analysis_parameters['retailer']);
          }
        }
        if ($this->input->post('province') !== FALSE) {
          $analysis_parameters['state'] = $this->input->post('province');
          if (empty($analysis_parameters['state'])) {
            unset($analysis_parameters['state']);
          } else {
            $query = $this->db->get_where("province", array("province_code" => $analysis_parameters['state']));
            $data['region'] = $query->row()->province_name;
          }
        }
        if ($this->input->post('city') !== FALSE) {
          $analysis_parameters['city'] = $this->input->post('city');
          if (empty($analysis_parameters['city'])) {
            unset($analysis_parameters['city']);
          } else {
            $data['city'] = $analysis_parameters['city'];
          }
        }
        if ($this->input->post('store') !== FALSE) {
          $analysis_parameters['store'] = $this->input->post('store');
          if (empty($analysis_parameters['store'])) {
            unset($analysis_parameters['store']);
          }
        }
        if (count($analysis_parameters) > 2) {
          $this->analysis->set_all($analysis_parameters);
        }
      }
    }
    $data["brand"] = $this->analysis->brand;
    $data["retailers_count"] = $this->analysis->total_retailers;
    $data["active_locations_count"] = $this->analysis->active_locations;
    $data["pending_locations_count"] = $this->analysis->pending_locations;
    $data["members_count"] = $this->analysis->registered_members;
    $data["lab_count"] = $this->analysis->active_labs;
//    $data["lab_hours_count"] = $this->analysis->lab_hours;
    $data["active_quiz_count"] = $this->analysis->active_quizzes;
    $data["quiz_result_time_sum"] = $this->analysis->training_hours;
    $data["completed_quiz_count"] = $this->analysis->quizzes_completed;
    $data["quiz_time_avg"] = $this->analysis->quiz_time;
    $data["quiz_score_avg"] = $this->analysis->quiz_score;
    $data["quiz_points_avg"] = $this->analysis->quiz_points;
    $data["point_awarded"] = $this->analysis->cheddar_points_awarded;
    $data["point_redeemed"] = $this->analysis->points_redeemed;
    $data["order_count"] = $this->analysis->store_orders;
    $data["draws_sum"] = $this->analysis->contest_entries_awarded;
    $data["referral_count"] = $this->analysis->referrals_send;
    $data["referral_converson"] = $this->analysis->referral_converson;
    $data["badges_awarded"] = $this->analysis->badges_awarded;
    $data["member_sessions"] = $this->analysis->member_sessions;
    $data["chart_data"] = $this->analysis->quizzes_per_month;
    $data["countries"] = array("all" => "All") + $this->analysis->countries;
    $data["retailers"] = array("all" => "All") + $this->analysis->retailers;
    $data["provinces"] = array("all" => "All") + $this->analysis->states;
    $data["cities"] = array("all" => "All") + $this->analysis->cities;
    $data["stores"] = array("all" => "All") + $this->analysis->stores;
//    $data['google_analytic_view_id'] = $site->google_analytic_view_id;
    $data['domain'] = $site->domain;
    $data['site_id'] = $site->id;
    self::loadBackendView($data, 'dashboard/dashboard_leftbar', NULL, 'dashboard/analysis');
  }

  private function _getLogData()
  {
    // assign values
    $aLog = array('ip_address' => $this->input->ip_address(),
      'user_agent' => $this->input->user_agent());
    // loop log items...
    foreach ($aLog as $key => $value) {
      $aLog[$key] = implode('|', array($key, $value));
    }
    return implode('||', $aLog);
  }

}

?>
