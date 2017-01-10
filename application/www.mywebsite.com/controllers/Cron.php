<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends HotCMS_Controller {

  /**
   * Constructor method
   * @access public
   * @return void
   */
  public function __construct()
  {
    // call the parent's constructor method
    parent::__construct();
  }

  /**
   * Default displaying method
   * @access public
   * @return void
   */
  public function index()
  {
    //$this->output->enable_profiler(TRUE);
    $modules = $this->model->list_modules();
    foreach ($modules as $mod) {
			$helper_filename = APPPATH . 'modules/' . $mod->module_code . '/helpers/' . $mod->module_code . '_helper.php';
			if (!file_exists($helper_filename)) {
        continue;
      }
      $this->load->helper($mod->module_code . '/' . $mod->module_code);
      $cronjob = $mod->module_code . '_cron';
      if (function_exists($cronjob)) {
        try {
          $cronjob();
          echo '<br />' . $cronjob . ' executed!<br />';
        }
        catch (Exception $e) {}
      }
    }
  }
  /*
  private function fix_admins(){
      $admins = $this->model->get_all_admins();
      foreach($admins as $admin){
          //echo $admin->user_id.',';
          $this->model->add_member_role($admin->user_id);
      }
  }
  */
  public function check_verified_users(){

   $this->load->model('account/account_model');
   $this->load->model('verification/verification_model');
   $accounts = $this->account_model->get_accounts_with_expired_verification();
   $email_content = 'Number of affected accounts: ' .count($accounts). "\n\n";
   foreach ($accounts as $account){
       $this->verification_model->unverify_user($account->user_id);
       $email_content .= sprintf('User id: %s Screenname: %s "\n\n"',$account->user_id, $account->screen_name);
   }
   $this->send_email($email_content);
  }

  private function send_email($message){
    $email_title = 'Cheddar Labs Cron job';
    $headers = 'From: support@cheddarlabs.com <support@cheddarlabs.com>' . "\r\n";
    //$headers .= 'Cc: nicole@hottomali.com, customercare@ztarmobile.com, speakout@ztarmobile.com, alaird@ztarmobile.com' . "\r\n";
    $notice_to = 'jan@hottomali.com';

    mail($notice_to, $email_title, $message, $headers);
  }

  public function add_slug_to_retailer(){

      $retailers = $this->db->select('*')
                    ->get('retailer')
                    ->result();

      foreach($retailers as $r){
          $this->db->set('slug',  strtolower(url_title($r->name,'-')))
                  ->where('id',$r->id)
                  ->update('retailer');

      }


  }
}
?>
