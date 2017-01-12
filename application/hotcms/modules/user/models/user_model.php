<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('user/user', TRUE);
    $this->tables = $this->config->item('tables', 'user');
  }

  public function check_user($username, $email, $password)
  {
    $this->db->select();
    $where = sprintf("(username = '%s' OR email = '%s') AND password = '%s'", $username, $email, $password);
    $this->db->where($where);
    $query = $this->db->get($this->tables['user']);
    return $query->row();
  }

  /**
   * 
DEPRECATED
   * list all users - order by first name
   * @param  array  role IDs
   * @param  int  page number
   * @param  int  per page
   * @return object with all users
   */
  public function list_all_users($role_ids = array(), $page_num = 1, $per_page = 100)
  {
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    $this->db->select('u.*, p.*')->distinct()
      ->from($this->tables['user'] . ' u')
      ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
      ->join($this->tables['user_role'] . ' ur', 'ur.user_id = u.id', 'LEFT OUTER');
    if (!empty($role_ids)) {
      $this->db->where_in('ur.role_id', $role_ids);
    }
    else {
      $this->db->join($this->tables['role'] . ' r', 'r.id = ur.role_id');
      $this->db->where('r.site_id', $this->site_id);
    }
    $this->db->order_by('p.first_name')->limit($per_page, $offset);
    return $this->db->get()->result();
  }

    /**
     * count all users for pagination purpose
     * @param  array  role IDs
     * @param  bool  verified
     * @return int
     */
    public function count_all_users($role_ids = array(),$verified = FALSE) {
        $this->db->from($this->tables['user'] . ' u')
                ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                ->join($this->tables['user_role'] . ' ur', 'ur.user_id = u.id');
        if (!empty($role_ids)) {
            $this->db->where_in('ur.role_id', $role_ids);
        } else {
            if($this->site_id==1){
                $this->db->join($this->tables['role'] . ' r', 'r.id = ur.role_id');
            }else{
                $this->db->join($this->tables['role'] . ' r', 'r.id = ur.role_id');
                $this->db->where('r.site_id', $this->site_id);
                
            }
        }
        if($verified){
            $this->db->where('p.verified', 1);
        }
        return $this->db->count_all_results();
    }

  /**
   * list all items from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  bool  $detailed when set to true, returns detailed information as well as store count, user count
   *                         when set to false, only returns ID and Name
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function user_list($filters = FALSE, $detailed = FALSE, $page_num = 1, $per_page = 0)
  {
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        if($filters['keyword_column'] == 'email'){
          $this->db->like('u.username', $filters['keyword']);
        }
        if($filters['keyword_column'] == 'screenname'){
          $this->db->like('p.screen_name', $filters['keyword']);
        }
      }
      
      $sortable_fields = array('status','last_login','created_on','email', 'screen_name','first_name','last_name');
      if (array_key_exists('sort_by', $filters) && $filters['sort_by'] > '' && in_array($filters['sort_by'], $sortable_fields)) {
        if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
          $sort_direction = 'DESC';
        }
        else {
          $sort_direction = 'ASC';
        }
        $this->db->order_by($filters['sort_by'], $sort_direction);
      }
      else {
        $this->db->order_by('username', 'ASC');
      }
      
    } else {
      $this->db->order_by('username', 'ASC');
    }
    
    if ($detailed) {
        if($this->site_id == 1){
        $query = $this->db->select('u.*, p.*')->distinct()
          ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
          //->join($this->tables['user_role'] . ' ur', 'ur.user_id = u.id', 'LEFT OUTER')
          //->join($this->tables['role'] . ' r', 'r.id = ur.role_id')
          //->where('u.site_id', $this->site_id)
          ->order_by('p.first_name')->limit($per_page, $offset)
        ->get($this->tables['user'] . ' u');
        }else{
          $query = $this->db->select('u.*, p.*')->distinct()
          ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
          //->join($this->tables['user_role'] . ' ur', 'ur.user_id = u.id', 'LEFT OUTER')
          //->join($this->tables['role'] . ' r', 'r.id = ur.role_id')
          ->where('u.site_id', $this->site_id)
          ->order_by('p.first_name')->limit($per_page, $offset)
        ->get($this->tables['user'] . ' u');            
            
        }
    }else {
      $query = $this->db->select('u.*')
        ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
        ->get($this->tables['user'] . ' u');
    }
    $result = $query->result();
    //var_dump($this->db->last_query());
    return $result;
  }

  /**
   * Counts all items
   * @param  array  filters, including search keyword, sorting field, and other filter criteria
   * @return int
   */
  public function user_count($filters)
  {
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {        
        $this->db->like('username', $filters['keyword']);
      }
    }
    $this->db->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id');
    $this->db->where('u.site_id', $this->site_id);
    return $this->db->count_all_results($this->tables['user'] .' u');
  }  
  
  /**
   * list users by role
   * @param  int  role ID
   * @return array
   */
  public function lists_users_by_role($role_id)
  {
    $this->db->select()->from($this->tables['user'])
      ->join($this->tables['user_profile'], $this->tables['user_profile'] . '.user_id = ' . $this->tables['user'] . '.id')
      ->join($this->tables['user_role'], $this->tables['user_role'] . '.user_id = ' . $this->tables['user'] . '.id')
      ->where($this->tables['user_role'] . '.role_id', $role_id)
      ->order_by($this->tables['user_profile'] . '.last_name');
    $query = $this->db->get();
    return $query->result();
  }

  /**
   * Get user from DB by user ID
   * @param id user
   * @return object with one row
   *
   */
  public function get_user_by_id($id)
  {
    $this->db->select();
    $this->db->from($this->tables['user']);
    $this->db->join($this->tables['user_profile'], $this->tables['user_profile'] . '.user_id = ' . $this->tables['user'] . '.id');
    $this->db->where($this->tables['user'] . '.id', $id);
    $this->db->order_by($this->tables['user_profile'] . '.last_name');
    $query = $this->db->get();
    return $query->row();
  }

  /**
   * Get user by user name
   * @param  str  user name
   * @return object with one row
   */
  public function get_user_by_username($username)
  {
    $this->db->select();
    $this->db->from($this->tables['user']);
    $this->db->join($this->tables['user_profile'], $this->tables['user_profile'] . '.user_id = ' . $this->tables['user'] . '.id');
    $this->db->where($this->tables['user'] . '.username', $username);
    $this->db->order_by($this->tables['user_profile'] . '.last_name');
    $query = $this->db->get();
    return $query->row();
  }

  public function insert()
  {
    self::_setElement();
    $this->db->set('create_date', 'CURRENT_TIMESTAMP', false);
    $this->db->insert($this->tables['user']);
  }

  public function update($id)
  {die('aa');
    $msg = '';
    //self::_setElement();
    //$this->db->set( 'username',             $this->input->post( 'username' ) );
    $this->db->set('email', $this->input->post('email'));
    //$this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where('id', $id);
    $this->db->update($this->tables['user']);

    // if valid password...
    //TODO: error handling
    if (($this->input->post('password') == $this->input->post('password_retype')) && $this->input->post('old_password')) {

      //$this->load->model('ion_auth_model');
      $this->load->library('ion_auth');

      //load user identity
      $identity = $this->ion_auth->get_user($id);

      $old = $this->input->post('old_password');
      $new = $this->input->post('password');

      if ($this->ion_auth->change_password($identity->username, $old, $new) == 1) {
        $msg = 'pwd_updated';
      }
      else {
        $msg = 'pwd_not_updated';
      }
    }

    $this->db->set('salutation', $this->input->post('salutation'));
    $this->db->set('first_name', $this->input->post('first_name'));
    //$this->db->set('middle_name', $this->input->post('middle_name'));
    $this->db->set('last_name', $this->input->post('last_name'));
    $this->db->set('screen_name', $this->input->post('screen_name'));
    $this->db->set('country_code', $this->input->post('country_code'));
    $this->db->set('province_code', $this->input->post('province_code'));
    $this->db->set('retailer_id', $this->input->post('retailer_id'));
    $this->db->set('store_id', $this->input->post('store_id'));
    $this->db->set('referral_code', $this->input->post('referral_code'));
    $this->db->set('employment', $this->input->post('employment'));
    $this->db->set('job_title', $this->input->post('job_title'));
    $this->db->set('hire_date', $this->input->post('hire_date'));
    $this->db->set('verified', $this->input->post('verified'));
    if ($this->input->post('verified') == '1') {
      $this->db->set('verified_date', time());
    }
    $this->db->set('newsletter_monthly', $this->input->post('newsletter_monthly'));
    $this->db->set('newsletter_newlab', $this->input->post('newsletter_newlab'));
    $this->db->set('newsletter_newswag', $this->input->post('newsletter_newswag'));
    $this->db->set('newsletter_survey', $this->input->post('newsletter_survey'));
    $this->db->where('user_id', $id);
    $this->db->update($this->tables['user_profile']);

    return $msg;
  }

  public function update_avatar($id, $avatar_id)
  {
    $this->db->set('avatar_id', $avatar_id);
    $this->db->where('user_id', $id);
    return $this->db->update($this->tables['user_profile']);
  }

  /**
   * update user verified date to the current timestamp
   * @param  int  $id  user ID
   * @return bool
   */
  public function update_verified_date($id)
  {
    $this->db->set('verified_date', time());
    $this->db->where('user_id', $id);
    return $this->db->update($this->tables['user_profile']);
  }

  public function delete_by_id($id)
  {
    // delete profile
    $this->db->where('user_id', $id);
    $this->db->delete($this->tables['user_profile']);
    // delete user roles
    $this->db->where('user_id', $id);
    $this->db->delete($this->tables['user_role']);
    // delete user data
    $this->db->where('id', $id);
    return $this->db->delete($this->tables['user']);
  }

    /**
     * List top users who earned most points
     * including spent points
     * @param  int  $limit how many records to be displayed
     * @param  str  $timespan could be "all", "year", or "month"
     * @return array
     */
    public function list_top_users($limit = 5, $timespan = 'all', $measurement = 'points', $site_restricted = 0) {
        if ($timespan == 'year') {
            $year = date('Y');
            $this->db->where('YEAR(i.create_timestamp)', $year);
        } elseif ($timespan == 'month') {
            $year = date('Y');
            $month = date('n');
            $this->db->where('YEAR(i.create_timestamp)', $year)
                    ->where('MONTH(i.create_timestamp)', $month);
        }
        if($measurement == 'points'){
            if($site_restricted == 1){
                $this->db->where('i.site_id >',  $this->site_id);
            }
        $query = $this->db->select('u.username, p.first_name, p.last_name, p.screen_name, SUM(i.points) AS points')
                        ->from($this->tables['user'] . ' u')
                        ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                        ->join($this->tables['points'] . ' i', 'i.user_id = u.id')
                        ->join($this->tables['user_role'] . ' r', 'r.user_id = u.id')
                        ->where('i.points >', '0')
                        ->where_not_in('r.role_id', array(1,2,8,9))
                        ->group_by(array('u.username', 'p.first_name', 'p.last_name', 'p.screen_name'))
                        ->order_by('points', 'desc')
                        ->limit($limit)->get();
        return $query->result();
        }else{
            if($site_restricted == 1){
                $this->db->where('i.site_id >',  $this->site_id);
            }
            $query = $this->db->select('u.username, p.first_name, p.last_name, p.screen_name, SUM(i.draws) AS points')
                            ->from($this->tables['user'] . ' u')
                            ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                            ->join($this->tables['draws'] . ' i', 'i.user_id = u.id')
                            ->join($this->tables['user_role'] . ' r', 'r.user_id = u.id')
                            ->where('i.draws >', '0')
                            ->where_not_in('r.role_id', array(1,2,8,9))
                            ->group_by(array('u.username', 'p.first_name', 'p.last_name', 'p.screen_name'))
                            ->order_by('points', 'desc')
                            ->limit($limit)->get();
            return $query->result();            
        }
            
    }
    /**
     * List user activities
     * @param  int  $limit how many records to be displayed
     * @return array
     */
    public function list_user_activity($limit = 5) {
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        //load last 10 from points table
        $query = $this->db->select('u.username, p.screen_name, p.first_name, p.last_name, i.points, i.point_type, i.ref_id, i.create_timestamp, i.description, p.avatar_id')
                ->from($this->tables['user'] . ' u')
                ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                ->join($this->tables['points'] . ' i', 'i.user_id = u.id')
                ->limit($limit)
                ->order_by('i.create_timestamp', 'desc')
                ->get();
        $points = $query->result();
        //echo '<pre>';
        //var_dump($points);
        if ($limit > 0) {
            $this->db->limit($limit);
        }        
        //load from draws table
        $query2 = $this->db->select('u.username, p.screen_name, p.first_name, p.last_name, i.point_type, i.draws, i.ref_id, i.create_timestamp, i.description, p.avatar_id')
                ->from($this->tables['user'] . ' u')
                ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                ->join($this->tables['draws'] . ' i', 'i.user_id = u.id')
                ->order_by('i.create_timestamp', 'desc')
                ->get();
        $draws = $query2->result();
        //var_dump($draws);
        //merging points & draws 
        if (!empty($draws)) {
            $result = array_merge($points, $draws);
            //sorting 
            foreach ($result as $key => $node) {
                $timestamps[$key] = $node->create_timestamp;
            }
            array_multisort($timestamps, SORT_DESC, $result);
            //limit to original limit
            $i = 0;
            $res_limited = array();

            foreach ($result as $res) {
                if ($limit > 0 && $limit > $i) {
                    $res_limited[$i] = $res;
                }
                $i++;
            }
            //var_dump($res_limited);
            //die();
            return ($res_limited);
        } else {
            return $points;
        }
    }

    /**
     * List user activities on profile page
     * @param  int  $limit how many records to be displayed
     * @param  int  user_id
     * @return array
     */
    public function list_user_activity_profile($limit = 5, $user_id) {
        if ($limit > 0) {
            //$this->db->limit($limit);
        }

        if(empty($user_id)){
            $user_id = $this->session->userdata( 'user_id' );
        }        
        
        $query = $this->db->select('u.username, p.screen_name, p.first_name, p.last_name, i.points, i.point_type, i.ref_id, i.create_timestamp, i.description, p.avatar_id')
                ->from($this->tables['user'] . ' u')
                ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                ->join($this->tables['points'] . ' i', 'i.user_id = u.id')
                ->where('i.user_id =', $user_id)                
                ->order_by('i.create_timestamp', 'desc')
                ->get();
        $points = $query->result();
        
        if ($limit > 0) {
            //$this->db->limit($limit);
        }        
        $query2 = $this->db->select('u.username, p.screen_name, p.first_name, p.last_name, i.point_type, i.draws, i.ref_id, i.create_timestamp, i.description, p.avatar_id')
                ->from($this->tables['user'] . ' u')
                ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
                ->join($this->tables['draws'] . ' i', 'i.user_id = u.id')
                ->where('i.user_id =', $user_id)
                ->order_by('i.create_timestamp', 'desc')
                ->get();
        $draws = $query2->result();
        //merging points & draws 
        if (!empty($draws)) {
            $result = array_merge($points, $draws);
            //sorting 
            foreach ($result as $key => $node) {
                $timestamps[$key] = $node->create_timestamp;
            }
            array_multisort($timestamps, SORT_DESC, $result);
            //limit to original limit
            $i = 0;
            $res_limited = array();

            foreach ($result as $res) {
                if ($limit > 0 && $limit > $i) {
                    $res_limited[$i] = $res;
                }
                $i++;
            }
            //var_dump($res_limited);
            //die();
            return ($result);
        } else {
            return $points;
        }
    }

    /**
     * Sum user points
     * @param  int  $limit how many records to be displayed
     * @return array
     */
    public function user_points_sum($limit = 5) {
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->select_sum('p.points')
                ->get($this->tables['points'] . ' p');
        $points = $query->row()->points;
        //var_dump($points);
        $query = $this->db->select_sum('p.points_reversed')
        ->get($this->tables['points'] . ' p');
        $points_reversed = $query->row()->points_reversed;
        //var_dump($points_reversed);
        return $points + $points_reversed;
    }

    /**
   * Sum user draws
   * 
   * @return int #of draws
   */
  public function user_draws_sum()
  {
    $query = $this->db->select_sum('d.draws')
      ->get($this->tables['draws'] . ' d');
    return $query->row()->draws;
  }
  

  /**
     * Count all retailers
     * @return int
     */
    public function retailer_count() {
        $count = $this->db->count_all_results($this->tables['retailer']);
        return $count;
    }

    /**
     * Count all stores
     * @return int
     */
    public function retailer_store_count() {
        $count = $this->db->count_all_results($this->tables['store']);
        return $count;
    }

    /**
     * List retailers
     * @param  str  $country_code
     * @retun array
     */
    public function list_retailers($country_code = '') {
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
    public function list_stores($retailer_id = 0, $province_code = 0) {
        if ($retailer_id > 0) {
            $this->db->where('retailer_id', $retailer_id);
        }
        if ($province_code != 0) {
            $this->db->where('province', $province_code);
        }
        $query = $this->db->select('id, retailer_id, store_name, store_num')
                ->where('status', 1)
                ->order_by('store_name')
                ->get($this->tables['store']);
        return $query->result();
    }

    /**
     * List provinces
     * @param str  $country_code
     * @retun array
     */
    public function list_provinces($country_code = '') {
        if ($country_code > '') {
            $this->db->where('country_code', $country_code);
        }
        $query = $this->db->select('province_code, province_name')
                ->order_by('country_code, province_name')
                ->get($this->tables['province']);
        return $query->result();
    }

    private function _setElement() {
        // assign values
        $this->db->set('salutation', $this->input->post('salutation'));
        $this->db->set('first_name', $this->input->post('first_name'));
        //$this->db->set( 'middle_name',           $this->input->post( 'middle_name' ) );
        $this->db->set('last_name', $this->input->post('last_name'));
        $this->db->set('username', $this->input->post('username'));
        $this->db->set('email', $this->input->post('email'));
        $this->db->set('position', $this->input->post('position'));
        $this->db->set('active', $this->input->post('active') ? 1 : 0 );
        // if invalid username...
        if (!$this->input->post('txtUser')) {
            $this->db->set('password', '');
        }
        // if valid password...
        if ($this->input->post('password')) {
            $password = $this->input->post('password');
            $salt = 'h0tt0m4l!secureCode' . sha1($password);
            $this->db->set('password', sha1($salt . $password . $salt));
        }
    }

    /**
     * get_user_avatar()
     * @param id asset
     * @return object with avatar image
     */
    public function get_user_avatar($id) {
        $this->db->select();
        $this->db->where($this->tables['asset'] . '.id', $id);
        $query = $this->db->get($this->tables['asset']);
        return $query->row();
    }

    /** add_badge()
     * 
     * add row to user_points table with badge info
     * 
     * @param int user_id
     * @param string badge_name
     * 
     */
    public function add_badge($user_id, $name) {
        $this->load->model('badge/badge_model');
        $this->load->model('account/account_model');
        $badge = $this->badge_model->badge_load_by_name($name);
        if($badge->award_type == 'draws'){
          $this->account_model->add_user_ce($user_id, $badge->award_amount, 'badge', 'badge', $badge->id, $badge->activity_feed_description);
        }else{
          $this->account_model->add_user_points($user_id, $badge->award_amount, 'badge', 'badge', $badge->id, $badge->activity_feed_description);
        }
    }
    
  /**
   * email unverify info
   */
  public function email_verify($user)
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
			  <td style="padding-bottom: 5px;"><p>Your account is now verified. It's time to test your knowledge and collect some cheddar!</p></td>
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
        $this->postmark->subject('Your Cheddar Labs Account Was Verified');
        $this->postmark->message_html($message_send);

        if ($this->postmark->send()) {
            return TRUE;
        } else {
            return FALSE;
        }      
  }     

}

?>