<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_admin_widget extends Widget {
    function run($args = array()) {
        $this->load->config('target/target', TRUE);
        $this->load->model("target/target_model");
        $this->load->model("account/account_model");

        $site_id = $this->account_model->get_admin_site($this->session->userdata("user_id"));
        if ($site_id > 0) {
          return $this->render("report_list_admin", array('content' => '<p>Nothing to config.</p>'));
        } else {
          return "<p>You don't have permission to manage this widget.</p>";
        }
    }
}