<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_list_widget extends Widget {
  function run($args = array()) {
    $this->load->config('target/target', TRUE);
    $this->load->model("target/target_model");
    $this->load->model("account/account_model");
    $this->load->model("site/site_model");

    $data = array();
    $data['environment'] = $this->config->item('environment');
    $module_title = 'Report List';

    $site_id = $this->account_model->get_admin_site($this->session->userdata("user_id"));
    if ($site_id > 0) {
      if (((int) $site_id) === 1) {
        $sites = $this->account_model->get_all_sites(TRUE, FALSE);
        foreach ($sites as $site) {
          $data["reports"][$site->id] = $site;
        }
      } elseif (((int) $site_id) > 1) {
        $data["reports"][$site_id] = $this->account_model->get_site_by_id($site_id);
      }
      return array("content" => $this->render("report_list", $data));
    } else {
      return "<p>You don't have permission to manage this widget.</p>";
    }

    if ($data['environment'] == 'admin_panel') {
      return '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>';
    }
  }
}