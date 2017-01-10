<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Account Model
*
* Author: jeffrey@hottomali.com
*
* Created:  04/18/2011
* Last updated:  10/25/2012
*
* Description:  Account model.
*
*/

class Account_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('account/account', TRUE);
    $this->tables = $this->config->item('tables', 'account');
  }

  /**
   * Get user account details
   * @param  int  $user_id
   */
  public function get_user($user_id)
  {
    $query = $this->db->select('u.email,p.*,u.created_on,u.last_login')
      ->join($this->tables['user_profile'] . ' p', 'p.user_id=u.id')
      ->where('u.id', $user_id)
      ->get($this->tables['user'] . ' u');
    return $query->row();
  }

  /**
   * Get user account details by user screen name
   * @param  int  $user_id
   */
  public function get_user_by_screename($screen_name)
  {
    $query = $this->db->select('u.email,p.*,u.created_on,u.last_login')
      ->join($this->tables['user_profile'] . ' p', 'p.user_id=u.id')
      ->where('p.screen_name', $screen_name)
      ->get($this->tables['user'] . ' u');
    return $query->row();
  }

  /**
   * Get user default contact details
   * @param  int  $user_id
   *
   * @return array with contact info
   */
  public function get_user_default_contact($user_id)
  {
    $query = $this->db->select('c.*')
      ->join($this->tables['contact'] . ' c', 'u.id=c.connection_id')
      ->where('u.id', $user_id)
      ->where('c.connection_name', 'user')
      ->where('c.default', 1)
      ->get($this->tables['user'] . ' u');
    return $query->row();
  }

  /**
   * Get user account public details
   * @param  int  $screenname
   */
  public function get_user_public_data($screnname)
  {
    $query = $this->db->select('u.email,p.*,u.created_on')
      ->join($this->tables['user_profile'] . ' p', 'p.user_id=u.id')
      ->where('p.screen_name', $screnname)
      ->get($this->tables['user'] . ' u');
    return $query->row();
  }

  /**
   * Get user account retailer details
   * @param  int  $user_id
   */
  public function get_user_retailer_info($user_id)
  {
    $query = $this->db->select('r.*')
      ->join($this->tables['retailer'] . ' r', 'r.id=p.retailer_id')
      ->where('p.user_id', $user_id)
      ->get($this->tables['user_profile'] . ' p');
    return $query->row();
  }

  /**
   * Determine user is an super admin.
   * @param  int  $user_id row id in user table
   * @return bool yes or no
   */
  function is_super_admin($user_id = 0) {
    if ($user_id > 0) {
      $query = $this->db->get_where($this->tables["user_role"], array("user_id" => $user_id, "role_id" => 1));
      return ($query->num_rows() > 0);
    }
    return FALSE;
  }
  /**
   * update user info
   * @param  int  $user_id
   */
  public function update_user($user_id)
  {
    $data = array(
      'first_name' => $this->input->post('first_name'),
      'last_name' => $this->input->post('last_name'),
      'screen_name' => $this->input->post('screen_name'),
      'country_code' => $this->input->post('country_code'),
      'province_code' => $this->input->post('province'),
      'retailer_id' => $this->input->post('retailer'),
      'store_id' => $this->input->post('store'),
      'employment' => $this->input->post('employment'),
      'job_title' => $this->input->post('job_title'),
      'hire_date' => $this->input->post('hire_date'),
      'newsletter_monthly' => $this->input->post('newsletter_monthly'),
      'newsletter_newlab' => $this->input->post('newsletter_newlab'),
      'newsletter_newswag' => $this->input->post('newsletter_newswag'),
      'newsletter_survey' => $this->input->post('newsletter_survey'),
    );
    $this->db->where('user_id', $user_id);
    return $this->db->update($this->tables['user_profile'], $data);
  }

  /**
   * update user retailer id
   * @param  int  $user_id
   * @param  int  $retailer_id
   */
  public function update_user_retailer($user_id, $retailer_id)
  {
    $data = array(
      'retailer_id' => $retailer_id,
    );
    $this->db->where('user_id', $user_id);
    return $this->db->update($this->tables['user_profile'], $data);
  }

  /**
   * update user store_id
   * @param  int  $user_id
   * @param  int  $retailer_id
   */
  public function update_user_store($user_id, $store_id)
  {
    $data = array(
      'store_id' => $store_id,
    );
    $this->db->where('user_id', $user_id);
    return $this->db->update($this->tables['user_profile'], $data);
  }


  /**
   * set basic role(s) for new users
   */
  public function set_member_role($user_id, $ref_site_id = 1)
  {
    //all users are registered only from the primary site
    //load member role for user's site_id (system = 3) and for main
    $query = $this->db->select('id')
      ->where_in('site_id', array($ref_site_id, 1))
      ->where('active', 1)
      ->where('system', 3)
      ->get($this->tables['role']);

    //insert member role for user site and for main site
    if ($query->num_rows() > 0) {
      foreach ($query->result() as $role) {
        $this->db->set('user_id', $user_id);
        $this->db->set('role_id', $role->id);
        $this->db->insert($this->tables['user_role']);
      }
    }
    return TRUE;
  }

  /**
   * List a user's points history
   * @param  int  $user_id
   * @retun array
   */
  public function list_user_points($user_id)
  {
    $query = $this->db->where('user_id', $user_id)
      ->get($this->tables['points']);
    return $query->result();
  }

  /**
   * Get a user's current points
   * @param int user_id
   * @param str type  [current] = current points, or lifetime
   * @retun int
   */
  public function get_user_points($user_id, $type = 'current')
  {
    if ($type == 'lifetime') {
      $query = $this->db->select_sum('points')
        ->where('user_id', $user_id)
        ->where('points >', 0)
        ->get($this->tables['points']);
    } elseif($type == 'ea') {
      $query = $this->db->select_sum('points')
        ->where('user_id', $user_id)
        ->where('points >', 0)
        ->where('point_type', 'EA')
        ->where('ref_table', 'EA')
        ->get($this->tables['points']);
    } else {
      $query = $this->db->select('points')
        ->where('user_id', $user_id)
        ->get($this->tables['user_profile']);
    }
    if ($query->row()->points > 0) {
        return $query->row()->points;
    }else{
        return 0;
    }
  }

  /**
   * Get a user's current draws
   * @param int user_id
   * @param str type  [current] = current points, or lifetime
   * @retun int
   */
  function get_user_draws($user_id, $type = 'current') {
      //if ($user_id);
    if (strcasecmp($type, 'lifetime') === 0) {
      $query = $this->db->select_sum('draws')
        ->where('user_id', $user_id)
        ->where('draws >', 0)
        ->get($this->tables['draws']);
    } else {
//      $last_live_draw = $this->db->order_by("create_timestamp", "desc")->get_where($this->tables["draw_history"], array("type" => "life"))->row();
//      $last_monthly_draw = $this->db->order_by("create_timestamp", "desc")->get_where($this->tables["draw_history"], array("type" => "monthly"))->row();
//      $last_second =  mktime(23, 59, 59, $last_monthly_draw->monthly_month+1, 0, $last_monthly_draw->monthly_year);
//      if (!empty($last_live_draw) && $last_live_draw->create_timestamp > $last_second) {
//        $last_draw = $last_live_draw->create_timestamp;
//      } else {
//        $last_draw = $last_second;
//      }
      //reset contest entries on evry 1st of the month
      $last_draw = strtotime(date('01-m-Y 00:00:01'));

      $query = $this->db->select_sum('draws')
        ->where('user_id', $user_id)
        ->where('create_timestamp >', $last_draw)
        ->get($this->tables['draws']);
    }

    if ($query->num_rows() > 0){
      return $query->row()->draws;
    }else{
        return 0;
    }
  }

  /**
   * UNUSED
   * get_user_points_rows($user_id) - number of rows in user_points table
   * ignoring record from EA import
   *
   * @param int user_id
   * @retun int
   */
  public function get_user_points_rows($user_id)
  {
      //if ($user_id);
      $query = $this->db->select('points')
        ->where('user_id', $user_id)
        ->where_not_in('point_type', 'ea')
        ->get($this->tables['user_points']);
    return $query->num_rows();
  }

  /**
   * UNUSED
   * get_user_draws_rows($user_id) - number of rows in user_points table
   * ignoring record from EA import
   *
   * @param int user_id
   * @retun int
   */
  public function get_user_draws_rows($user_id)
  {
      //if ($user_id);
      $query = $this->db->select('draws')
        ->where('user_id', $user_id)
        ->where_not_in('point_type', 'ea')
        ->get($this->tables['draws']);
    return $query->num_rows();
  }

  /**
   * get_user_points_orders($user_id)
   * get users prders
   *
   * @param int user_id
   * @retun object
   */
  public function get_user_orders($user_id)
  {
      //if ($user_id);
      $query = $this->db->select('*')
        ->where('user_id', $user_id)
        ->get($this->tables['user_order']);
    return $query->result();
  }

  /**
   * Adds points to a user
   * @param  int  $user_id
   * @param  int  $points
   * @param  str  $type
   * @param  str  $ref_table
   * @param  int  $ref_id
   * @param  str  $description
   * @return boolean
   */
  public function add_user_points($user_id, $points, $type, $ref_table = '', $ref_id = 0, $description = '')
  {
    $result = FALSE;
    if ($points == 0) {
        if(!in_array($type, array('draw-winner', 'badge'))){
           return $result;
        }
    }
    $this->db->set('site_id', $this->site_id);
    $this->db->set('user_id', $user_id);
    $this->db->set('points', $points);
    $this->db->set('point_type', $type);
    $this->db->set('ref_table', $ref_table);
    $this->db->set('ref_id', $ref_id);
    $this->db->set('description', $description);
    $this->db->set('create_timestamp', time());
    $result = $this->db->insert($this->tables['points']);
    if ($result) {
      $result = $this->_sync_user_points($user_id);
    }
    return $result;
  }

  /**
   * Refund points to a user
   * @param  int  $user_id
   * @param  str  $ref_table reference table
   * @param  int  $ref_id   reference foreign key
   * @param  int  $points points to be refunded
   * @return boolean
   */
  public function refund_user_points($user_id, $ref_table, $ref_id, $points)
  {
    $result = FALSE;
    if ($points == 0) {
      return $result;
    }
    $this->db->set('points_reversed', $points);
    $this->db->set('reverse_timestamp', time());
    $this->db->where('user_id', $user_id);
    $this->db->where('ref_table', $ref_table);
    $this->db->where('ref_id', $ref_id);
    $result = $this->db->update($this->tables['points']);
    if ($result) {
      $result = $this->_sync_user_points($user_id);
    }
    return $result;
  }

  /**
   * Recalculate user points and sync with the user's profile
   * @param  int  $user_id
   * @return bool
   */
  private function _sync_user_points($user_id)
  {
    $query = $this->db->select('SUM(points) AS total, SUM(points_reversed) AS reversed')
      ->where('user_id', $user_id)
      ->get($this->tables['points']);
    $row = $query->row();
    $points = $row->total + $row->reversed;
    $this->db->set('points', $points);
    $this->db->where('user_id', $user_id);
    $result = $this->db->update($this->tables['user_profile']);
    if ($result) {
      // refresh the session variable
      $this->session->set_userdata('user_points', $points);
    }
    return $result;
  }

  /**
   * Adds contest/draw entries to a user
   * @param  int  $user_id
   * @param  int  $ce
   * @param  str  $type
   * @param  str  $ref_table
   * @param  int  $ref_id
   * @param  str  $description
   * @return boolean
   */
  public function add_user_ce($user_id, $ce, $type, $ref_table = '', $ref_id = 0, $description = '')
  {

      //HACK -> check it there is upload_avat event for user_id
      if($type == 'upload_avat'){

      $query = $this->db->select('id')
        ->where('point_type', 'upload_avat')
        ->where('user_id', $user_id)
        ->get($this->tables['draws']);
      if($query->num_rows() > 0){

             return false;
        }

      }

    $result = FALSE;
    if ($ce == 0) {
      return $result;
    }
    $this->db->set('site_id', $this->site_id);
    $this->db->set('user_id', $user_id);
    $this->db->set('draws', $ce);
    $this->db->set('point_type', $type);
    $this->db->set('ref_table', $ref_table);
    $this->db->set('ref_id', $ref_id);
    $this->db->set('description', $description);
    $this->db->set('create_timestamp', time());
    $result = $this->db->insert($this->tables['draws']);
    if ($result) {
      $result = $this->_sync_user_contest_entries($user_id);
    }

    return $result;
  }

  /**
   * Recalculate user contest entires and sync with the user's profile
   * @param  int  $user_id
   * @return bool
   */
  private function _sync_user_contest_entries($user_id)
  {
    $query = $this->db->select('SUM(draws) AS total, SUM(draws_reversed) AS reversed')
      ->where('user_id', $user_id)
      ->get($this->tables['draws']);
    $row = $query->row();
    $draws = $row->total + $row->reversed;
    $this->db->set('draws', $draws);
    $this->db->where('user_id', $user_id);
    $result = $this->db->update($this->tables['user_profile']);
    if ($result) {
      // refresh the session variable
      $this->session->set_userdata('user_draws', $draws);
    }
    return $result;
  }

  /**
   * List retailers
   * @param  str  $country_code
   * @retun array
   */
  public function list_retailers($country_code = '')
  {
    if ($country_code > '') {
      $this->db->where('country_code', $country_code);
    }
    $query = $this->db->select('id, name, country_code')
      ->where('status', 1)
      ->order_by('name')
      ->get($this->tables['retailer']);
    return $query->result();
  }

  /**
   * List store locations
   * @param  int  $retailer_id
   * @retun array
   */
  public function list_stores($retailer_id = 0, $province_code = 0)
  {
    if ($retailer_id > 0) {
      $this->db->where('retailer_id', $retailer_id);
    }
    if ($province_code != 999999) {
      $this->db->where('province', $province_code);
    }
    /*
    if ($country_code != 0) {
      $this->db->where('country_code', $country_code);
    }
    */
    $query = $this->db->select('id, retailer_id, store_name, store_num')
      ->where('status', 1)
      ->order_by('store_name')
      ->get($this->tables['store']);
    return $query->result();
  }

  /**
   * List provinces
   * @param  str  $country_code
   * @retun array
   */
  public function list_provinces($country_code = 'us')
  {
    $this->db->where('country_code', $country_code);

    $query = $this->db->select('province_code, province_name')
      ->order_by('province_name')
      ->get($this->tables['province']);
    return $query->result();
  }

  /**
   * Get Site Name
   * @param  int  $site_id
   * @retun str
   */
  public function get_site_name($site_id = 1)
  {
    $query = $this->db->select('name, primary')
      ->where('id', $site_id)
      ->get('site');
    return $query->row();
  }

  /**
   * Get user account badges
   * @param  int  user_id
   */
  public function get_account_badges($user_id)
  {
      //TODO join to badge to load badge info

    $query = $this->db->select('ref_id')
      ->where('user_id', $user_id)
      ->where('point_type', 'badge')
      ->get($this->tables['user_points']);

    $points_badge = $query->result();

    $query2 = $this->db->select('ref_id')
      ->where('user_id', $user_id)
      ->where('point_type', 'badge')
      ->get($this->tables['draws']);

    $draws_badge = $query2->result();

    $result = array_merge($points_badge, $draws_badge);

    return $result;
  }

  public function get_shops(){
    $query = $this->db->select('*')
            ->order_by('id')
      ->get('retailer_store');
      return $query->result();
  }

  public function update_shop_id($old_id, $new_id){
    $this->db->set('store_id', $new_id);
    $this->db->where('store_id', $old_id);
    $result = $this->db->update('user_profile');

    return $result;
  }

  public function delete_shop_id($id){
    $this->db->where('id', $id);
    $result = $this->db->delete('retailer_store');

    return $result;
  }

  public function get_users($store_id){
    $query = $this->db->select('u.username, u.id')
      ->join('user as u','u.id = up.user_id')
      ->where('up.store_id', $store_id)
      ->get('user_profile up');
      return $query->result();
  }

  /**
   * email unverify info
   */
  public function email_unverify($user, $old_retailer, $new_retailer)
  {

  //<tr><td style="padding-top; 5px; padding-bottom: 5px; padding-left: 20px; padding-right: 20px; background: white; font-size: 9pt; width: 730px;">
  //Please retain this receipt for your records. If this message is not displaying properly, <a style="color: #36B8EA; text-decoration: none" href="https://{$_SERVER['HTTP_HOST']}/my-account">click here <img border="0" width="8" height="8" alt="Click here" src="http://www.example.com/asset/images/link_arrow.png" /></a></td></tr>
      $message_send = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title>Cheddar Labs Account Change</title>
  </head>
  <body style="font: 13px/18px arial, sans-serif; background-color: #e5e5e5; width:100%; margin: 0; padding: 0; color:#4e4e4e">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
	<td style="padding-top: 30px; padding-left: 20px; padding-bottom: 30px;">
	  <table cellspacing="0" cellpadding="0" border="0" style="width: 750px; background: white; border-right: 2px solid #DDD; border-bottom: 2px solid #BBB">
	    <tr>
	      <td width="730px" style="padding-top: 12px; padding-left: 10px">
		<a href="http://www.cheddarlabs.com"><img width="730px" height="123px" src="http://{$_SERVER['HTTP_HOST']}/asset/images/email/email-header-w1.jpg" alt="Thank you" /></a>
	      </td>
	    </tr>
	    <tr>
	      <td width="730px" style="padding-top: 12px; padding-left: 10px">
		<a href="http://www.cheddarlabs.com"><img width="730px" height="175px" src="http://{$_SERVER['HTTP_HOST']}/asset/images/email/img-user-notification.jpg" alt="User notification" /></a>
	      </td>
	    </tr>
	    <tr>
	      <td style="padding-left: 10px; padding-top: 10px; padding-right: 10px; valign="top">
		<table cellpadding="0" cellspacing=0" align="left" width="730px;" style="background-color: white;">
		  <tr>
		    <td>
		      <table width="730px" border="0" align="left" cellspacing="0" cellpadding="0" style="padding-right: 20px; padding-left: 10px; border-right: solid 4px #FFF; background-color: white">
			<tr>
			  <td style="padding-top: 20px"><h2 style="font-size: 1.25em; color: #b39451">Your Cheddar Labs account was changed.</h2></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;"><p>You have changed your retailer from <strong>{$old_retailer->name}</strong> to <strong>{$new_retailer->name}</strong> and your account is now unverified. You will have to re-verify with a recent paystub from your new retailer.
Don't wait too long, there's cheddar waiting for you!</p></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;"><p><a href="http://www.cheddarlabs.com">www.cheddarlabs.com</a></p></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 15px;"></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 5px;"><p style="line-height:20px; height: 20px;display:block;"><img height='20px' width='18px' src="http://{$_SERVER['HTTP_HOST']}/asset/images/email/icon-cheddar-signature-atom.png" alt="Cheddar Atom" /> The Cheddar Labs Team.</p></td>
			</tr>
			<tr>
			  <td style="padding-bottom: 20px;"></td>
			</tr>
		      </table>
		    </td>
		    <td valign="top" style="padding-left: 20px; padding-right: 20px; padding-top: 20px; background-color: white">

		    </td>
		  </tr>
		</table>
	      </td>
	    </tr>
	  </table>
	</td>
      </tr>
      <tr>
	<td style="border-top: 1px dotted #CCC; padding-top; 5px; padding-left: 20px; padding-right: 20px; background: white; font-size: 10pt; width: 730px;"></td>
      </tr>
    </table>
  </body>
</html>
EOF;

        $this->postmark->clear();
        $config['mailtype'] = "html";
        $this->postmark->initialize($config);
        $this->postmark->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
        $this->postmark->to($user->email);
        $this->postmark->bcc('jan@hottomali.com');
        $this->postmark->subject('Your Cheddar Labs Account Was Unverified');
        $this->postmark->message_html($message_send);

        if ($this->postmark->send()) {
            return TRUE;
        } else {
            return FALSE;
        }
  }
  /*
   * get users which are verified more than year ago
   */

  public function get_accounts_with_expired_verification(){
      // 1 year in seconds = 31556940
      $ts = time();
      $query = $this->db->select('*')
              ->where('verified',1)
//              ->where('verified_date !=', 0 )
              ->where('verified_date <=', ($ts - 31556940) )
              ->get($this->tables['user_profile']);
      return $query->result();
  }

  /**
   * Count times of user log-in.
   */
  public function count_logins($user_id = 0) {
    if ($user_id > 0) {
      $this->db->where("user_id", $user_id);
    }
    $query = $this->db->get($this->tables['log_login']);
    if ($query->num_rows() > 0) {
      return $query->num_rows();
    }
    return 0;
  }
  /**
   * Log every user log-in.
   */
  public function log_login() {
    $this->db->insert($this->tables['log_login'], array('user_id' => $this->session->userdata('user_id')));
  }

  function get_info($user_id = 0) {
    if ($user_id > 0) {
      $query = $this->db->get_where($this->tables["user_profile"], array("user_id" => $user_id));
      if ($query->num_rows() > 0) {
        $user_info = $query->row();
        $user_info->draws = $this->get_user_draws($user_id);
        $user_info->points = $this->get_user_points($user_id);
        return $user_info;
      }
    }
    return FALSE;
  }

  /**
   * Get latest session of a period.
   * @param  int    $period_id first row id of same period in user session log table
   * @param  int    $esfp_key  key to verify row in user session log table
   * @return object latest session
   */
  private function _get_latest_session_of_period($period_id, $esfp_key = "") {
    if ($period_id <= 0) {
      return FALSE;
    }
    if ( !empty($esfp_key)) {
      $this->db->where("esfp_key", $esfp_key);
    }
    return $this->db->order_by("id", "desc")->get_where($this->tables["user_session_log"], array("period_id" => $period_id))->row();
  }

  /**
   * Extend the latest session for user to current timestamp.
   * @param  string $keys           $esfp_key.$period_id
   * @param  int    $last_n_seconds how long new extention last in secons
   * @return int    indicating error type or success
   */
  function extend_session_for_period($keys, $last_n_seconds) {
    if (strlen($keys) < 33) {
      return 1;
    }
    $latest_session = $this->_get_latest_session_of_period(substr($keys, 32), substr($keys, 0, 32));
    if ($latest_session === FALSE) {
      return 2;
    }
    if ($this->db->update($this->tables["user_session_log"], array("last_n_seconds" => (((int) $latest_session->last_n_seconds) + ((int) $last_n_seconds))), array("id" => $latest_session->id)) === FALSE) {
      return 3;
    } else {
      return 0;
    }
  }

  /**
   * Check this page is training item page. if yes, set training_id in cooky ci_session; otherwise, usset it.
   * @param array $segments
   */
  private function _process_training($segments) {
    if ((count($segments) === 3) && (strcasecmp($segments[0], "labs") === 0) && (strcasecmp($segments[1], "product") === 0)) {
      $this->load->model("training/training_model");
      $this->session->set_userdata("training_id", $this->training_model->get_training_id($segments[2]));
    } else {
      $this->session->unset_userdata("training_id");
    }
  }

  /**
   * Check this page is quiz item page. if yes, set quiz_id and its training_id in cooky ci_session; otherwise, usset it.
   * @param array $segments
   */
  private function _process_quiz($segments) {
    if ((count($segments) === 2) && (strcasecmp($segments[0], "quiz") === 0)) {
      $this->load->model("quiz/quiz_model");
      $quiz_id = $this->quiz_model->get_quiz_id($segments[1]);
      $this->session->set_userdata("quiz_id", $quiz_id);
      $quiz = $this->quiz_model->quiz_load($quiz_id);
      $this->session->set_userdata("training_id", $quiz->training_id);
    } else {
      $this->session->unset_userdata("quiz_id");
    }
  }

  /**
   * Check if a period is pausing not long, without extending its latest session's end timestap.
   * @param  int  $period_id   idenitfication number for period in user session log
   * @param  int  $pause_limit optional; maximum limit in seconds of a period can be pausing
   * @return bool indicating pausing too long or not
   */
  private function _period_pause_not_long($period_id, $pause_limit = 3600) {
    $latest_session = $this->_get_latest_session_of_period($period_id);
    if ($latest_session == FALSE) {
      return FALSE;
    }
    return ($latest_session->last_n_seconds <= $pause_limit);
  }

  /**
   * Set a new session for a period from cookie ci_session.
   * @param  int $period_id  first row id of same period in user session log table
   * @return int new row id in the user session log table
   */
  private function _new_session_for_period($period_id = 0) {
    if (((int) $period_id) <= 0) {
      $latest_session = $this->_get_latest_session_of_period($period_id);
      if ($latest_session !== FALSE) {
        $esfp_key = $latest_session->esfp_key;
      }
    } else {
      $period_id = ((int) $this->session->userdata("period_id"));
    }
    if ( !isset($esfp_key)) {
      $this->load->helper("string");
      $esfp_key = random_string();
    }
    $this->db->set("period_id", $period_id);
    $this->db->set("esfp_key", $esfp_key);
    $this->db->set("user_id", ((int) $this->session->userdata("user_id")));
    $this->db->set("site_id", ((int) $this->session->userdata("siteID")));
    $this->db->set("training_id", ((int) $this->session->userdata("training_id")));
    $this->db->set("quiz_id", ((int) $this->session->userdata("quiz_id")));
    $this->db->set("requested_url", current_url());
    $this->db->set("start_timestamp", time());
    $this->db->set("ip_address", $this->session->userdata("ip_address"));
    $this->db->set("user_agent", $this->session->userdata("user_agent"));
    $this->db->insert($this->tables["user_session_log"]);
    return $this->db->insert_id();
  }

  /**
   * Start a period of user session log.
   * @return int
   */
  private function _start_period() {
    $period_id = $this->_new_session_for_period();
    $this->db->update($this->tables["user_session_log"], array("period_id" => $period_id), array("id" => $period_id));
    $this->session->set_userdata("period_id", $period_id);
    return $period_id;
  }

  /**
   * Check user period, then start or update accordingly.
   * @return int user period identification number
   */
  function process_period() {
    $segments = explode("/", uri_string());
    $this->_process_training($segments);
    $this->_process_quiz($segments);
    $period_id = (int) $this->session->userdata("period_id");
    if (($period_id > 0) && $this->_period_pause_not_long($period_id)) {
      $this->_new_session_for_period($period_id);
    } else {
      $period_id = $this->_start_period();
    }
    $latest_session = $this->_get_latest_session_of_period($period_id);
    $esfp_key = $latest_session->esfp_key;
    return $esfp_key.$period_id;
  }

  /**
   * Get total training time for a site.
   * @param  int $site_id row id in site table
   * @return int number in seconds
   */
  function get_site_training_time($site_id = 1) {
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    try {
      return $this->db->select_sum("last_n_seconds")->where("training_id >", 0)->get($this->tables["user_session_log"])->row()->last_n_seconds;
    } catch (Exception $ex) {
      return 0;
    }
  }

  /**
   * Get total training time for an user.
   * @param  int $site_id row id in site table
   * @param  int $user_id row id in user table
   * @return int number in seconds
   */
  function get_user_training_time($site_id = 1, $user_id = 0) {
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    if ($user_id > 0) {
      $this->db->where("user_id", $user_id);
    }
    try {
//      $user_sessions = $this->db->select("last_n_seconds")->where("training_id >", 0)->get($this->tables["user_session_log"])->result();
//      $training_time = 0;
//      foreach ($user_sessions as $user_session) {
//        $training_time += ((int) $user_session->last_n_seconds);
//      }
//      return $training_time;
      return ((int) $this->db->select_sum("last_n_seconds", "training_time")->where("training_id >", 0)->get($this->tables["user_session_log"])->row()->training_time);
    } catch (Exception $ex) {
//      echo "<pre>";var_dump($this->db->last_query());die("</pre>");
      return 0;
    }
  }

  /**
   * Verify user can admin a site/brand/subdomain; if yes, return site id.
   * @param  int $user_id row id in user table
   * @return int row id in site table
   */
  function get_admin_site($user_id = 0) {
    if (((int) $user_id) < 1) {
      return 0;
    }
    $user = $this->get_user($user_id);
//    if ($user->retailer_id == 131) {
//      return 3;
//    } else
//      if ($user->retailer_id == 130) {
      if ( !empty($user)) {
      return 1;
    }
    return 0;
  }

  /**
   * Verify user is trained by site.
   * @param  int  $user_id row id in user table
   * @param  int  $site_id row id in site table
   * @return bool TRUE if user is trained, otherwise FALSE
   */
  function is_user_trained($user_id = 0, $site_id = 1) {
    if (($user_id < 1) OR ($site_id < 1)) {
      return FALSE;
    }
    if ($site_id > 1) {
      $this->db->where("q.site_id", $site_id)->join($this->tables["quiz"]." q", "q.id = qh.quiz_id");
    }
    return ($this->db->where("finish_timestamp >", 0)->get_where($this->tables["quiz_history"]." qh", array("qh.user_id" => $user_id))->num_rows() > 0);
  }

  /**
   * Log promo codes entered in registration proces
   */

  function log_user_promo_code($user_id = 0, $promo_code){
    $this->db->set('user_id', $user_id);
    $this->db->set('promo_code', $promo_code);
    $this->db->set('create_timestamp', time());
    $this->db->set('update_timestamp', time());
    $result = $this->db->insert('user_promo_code_log');
  }


  /**
   * Save brand info to db
   * @return boolean
   */
  public function save_brand_info($brand)
  {

    $this->db->set('first_name', $brand['first_name']);
    $this->db->set('last_name', $brand['last_name']);
    $this->db->set('company', $brand['company']);
    $this->db->set('email', $brand['email']);
    $this->db->set('phone', $brand['phone']);
    $this->db->set('comments', $brand['comments']);
    $this->db->set('create_timestamp', time());
    $result = $this->db->insert('brand_register');

    return $result;
  }


  /**
   * Add info about issued certificate
   * @param  string  certicate name
   * @param  string  cetrificate filename
   */

  function add_user_certificate($cert_name, $filename, $user_id){
    $this->db->set('user_id', $user_id);
    $this->db->set('certificate_name', $cert_name);
    $this->db->set('certificate_filename', $filename);
    $this->db->set('update_timestamp', time());
    $this->db->set('create_timestamp', time());
    $result = $this->db->insert('user_certificate');
  }


  /**
   * get_user_certificates($user_id)
   * get users certificates
   *
   * @param int user_id
   * @retun object
   */
  public function get_user_certificates($user_id)
  {
      //if ($user_id);
      $query = $this->db->select('*')
        ->where('user_id', $user_id)
        ->get('user_certificate');
    return $query->result();
  }
}
?>
