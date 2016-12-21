<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Draw_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('draw/draw', TRUE);
    $this->tables = $this->config->item('tables', 'draw');
  }

  /**
   * Counts all draws
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @return int
   */
  public function draw_count($filters = FALSE)
  {
//    if (is_array($filters)) {
//      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
//        $this->db->like('name', $filters['keyword']);
//      }
//
//      if (array_key_exists('status', $filters) && $filters['status'] > '') {
//        if (is_array($filters['status'])) {
//          $this->db->where_in('status', $filters['status']);
//        }
//        else {
//          $this->db->where('status', $filters['status']);
//        }
//      }
//    }
    //$this->db->where('site_id', $this->site_id);
    return $this->db->count_all_results($this->tables['draw_history']);
  }  
  
  /**
   * Lists all draws from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
  public function draw_list($filters = FALSE, $page_num = 1, $per_page = 0)
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
    //if ($type_id > 0) {
    //  $this->db->where('n.quiz_type_id', (int)$type_id);
    //}
    //if ($live_only) {
    //  $this->db->where('n.status', 1);
    //}
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('n.name', $filters['keyword']);
      }
      if (array_key_exists('type_id', $filters) && $filters['type_id'] > '') {
        if (is_array($filters['type_id'])) {
          $this->db->where_in('n.quiz_type_id', $filters['type_id']);
        }
        else {
          $this->db->where('n.quiz_type_id', $filters['type_id']);
        }
      }
      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('n.status', $filters['status']);
        }
        else {
          $this->db->where('n.status', $filters['status']);
        }
      }
      $sortable_fields = array('name', 'status', 'create_timestamp');
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
        $this->db->order_by('id', 'ASC');
      }
    }
    else {
      $this->db->order_by('id', 'ASC');
    }
    
    $query = $this->db->select('*')
      ->order_by('name', 'ASC')
      ->get($this->tables['draw_winner']);
    return $query->result();
  }
   */
  
  

  /**
   * List limited and filtered draw hitory rows.
   * @param  array $filters
   * @param  int   $page_num hint for offset
   * @param  int   $per_page limit number
   * @return array
   */
  public function list_draws($filters = FALSE, $page_num = 1, $per_page = 0)
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
//      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
//        $this->db->like('n.name', $filters['keyword']);
//      }
//      if (array_key_exists('type_id', $filters) && $filters['type_id'] > '') {
//        if (is_array($filters['type_id'])) {
//          $this->db->where_in('n.quiz_type_id', $filters['type_id']);
//        }
//        else {
//          $this->db->where('n.quiz_type_id', $filters['type_id']);
//        }
//      }
//      if (array_key_exists('status', $filters) && $filters['status'] > '') {
//        if (is_array($filters['status'])) {
//          $this->db->where_in('n.status', $filters['status']);
//        }
//        else {
//          $this->db->where('n.status', $filters['status']);
//        }
//      }
      $sortable_fields = array('name', 'type', 'number_of_winners', 'monthly_month', 'monthly_year', 'create_timestamp');
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
        $this->db->order_by('create_timestamp', 'DESC');
      }
    }
    else {
      $this->db->order_by('create_timestamp', 'DESC');
    }
    
    $query = $this->db->select('*')
      ->order_by('name', 'ASC')
      ->get($this->tables['draw_history']);
    return $query->result();
  }

  function list_winners($draw_history_id = 0) {
    if ($draw_history_id > 0) {
      $query = $this->db->get_where($this->tables["draw_history"], array("id" => $draw_history_id));
      $draw_name = $query->row()->name;
      if ( !empty($draw_name)) {
        $this->db->where("user_draw_winner.name", $draw_name);
      }
    }
    $query = $this->db->select("user_draw_winner.*, user_profile.screen_name, user_profile.verified as account_verified, user.email")->join("user_profile", "user_profile.user_id = user_draw_winner.user_id")->join("user", "user_profile.user_id = user.id")->get("user_draw_winner");
    return $query->result();
  }

  function insert_winner($draw,$winner) {
    $time = now();
    $this->db->set( 'name', $draw);
    $this->db->set( 'user_id', $winner['user_id'] );
    $this->db->set( 'ref_id', $winner['ref_id'] );
    $this->db->set('update_timestamp', $time);
    $this->db->set('create_timestamp', $time);
    
    $inserted = $this->db->insert( $this->tables['draw_winner'] );
    if ($inserted) {
      $draw_winner_id = $this->db->insert_id();
      return $draw_winner_id;
    }
    return FALSE;
  }
  
  /**
   * Insert new draw into history table.
   * @param  string $name
   * @param  string $type
   * @param  int    $number_of_winners
   * @param  int    $monthly_month
   * @param  int    $monthly_year
   * @return mixed  new row ID if success, otherwise FALSE
   */
  function insert_history($name = "New Draw", $type = "life", $number_of_winners = 20, $monthly_month = 1, $monthly_year = 2012, $start = 1, $end = 2, $description = '') {
     switch($type) {
      case "monthly":
        return $this->insert_monthly_type_history($name, $number_of_winners, $monthly_month, $monthly_year, $description);
      case "life":
        return $this->insert_life_type_history($name, $number_of_winners, $description);
      case "custom":
        return $this->insert_custom_type_history($name, $number_of_winners, $start, $end, $description);          
      default:
        return $this->insert_life_type_history($name, $number_of_winners);
    }
  }
  
  /**
   * Insert new life-type draw into history table.
   * @param  string $name
   * @param  int    $number_of_winners
   * @param  text   $description
   * @return mixed  new row ID if success, otherwise FALSE
   */
  function insert_life_type_history($name = '', $number_of_winners = 20, $description) {
    $time = now();
    $this->db->set("name", $name);
    $this->db->set('description', $description);
    $this->db->set("type", "life");
    $this->db->set("number_of_winners", $number_of_winners);
    $this->db->set('create_timestamp', $time);
    $this->db->set('update_timestamp', $time);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    
    $inserted = $this->db->insert($this->tables["draw_history"]);
    if ($inserted) {
      $draw_hitory_id = $this->db->insert_id();
      return $draw_hitory_id;
    }
    return FALSE;
  }
  
  /**
   * Insert new monthly-type draw into history table.
   * @param  string $name
   * @param  int    $number_of_winners
   * @param  int    $monthly_month
   * @param  int    $monthly_year
   * @param  text   $description
   * @return mixed  new row ID if success, otherwise FALSE
   */
  function insert_monthly_type_history($name = '', $number_of_winners = 20, $monthly_month = 1, $monthly_year = 2012, $description) {
    $time = now();
    $this->db->set("name", $name);
    $this->db->set('description', $description);
    $this->db->set("type", "monthly");
    $this->db->set("number_of_winners", $number_of_winners);
    $this->db->set("monthly_month", $monthly_month);
    $this->db->set("monthly_year", $monthly_year);
    $this->db->set('create_timestamp', $time);
    $this->db->set('update_timestamp', $time);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    
    $inserted = $this->db->insert($this->tables["draw_history"]);
    if ($inserted) {
      $draw_hitory_id = $this->db->insert_id();
      return $draw_hitory_id;
    }
    return FALSE;
  }
  
  /**
   * Insert new custom period draw into history table.
   * @param  string     $name
   * @param  int        $number_of_winners
   * @param  timestamp  $start
   * @param  timestamp  $end
   * @param  text       $description
   * @return mixed  new row ID if success, otherwise FALSE
   */
  function insert_custom_type_history($name = '', $number_of_winners = 20, $start, $end, $description) {
    $start_array = date_parse($start);
    $time = now();
    $this->db->set("name", $name);
    $this->db->set('description', $description);
    $this->db->set("type", "custom");
    $this->db->set("number_of_winners", $number_of_winners);
    $this->db->set("start", strtotime($start));
    $this->db->set("end", strtotime($end));
    $this->db->set("monthly_month", $start_array['month']);
    $this->db->set("monthly_year", $start_array['year']);
    $this->db->set('create_timestamp', $time);
    $this->db->set('update_timestamp', $time);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    
    $inserted = $this->db->insert($this->tables["draw_history"]);
    if ($inserted) {
      $draw_hitory_id = $this->db->insert_id();
      return $draw_hitory_id;
    }
    return FALSE;
  }  
  
  public function draw_winner_update($draw_id,$post){
      $time = now();
      $this->db->set( 'feed_description', $post[ 'feed_description' ]);
      $this->db->set( 'verified', $post[ 'verified' ]=='accept' ? 1 : 0 );
      $this->db->set('update_timestamp', $time);
      $this->db->where('id',$draw_id);
      $this->db->update($this->tables['draw_winner']);   
  }
  
  /**
   * Get single draw from DB
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active draws only
   * @return object with one row
   */
  public function draw_load($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('b.*')
      ->where('b.id', $id)
      ->get($this->tables['draw_winner'] . ' b');
    return $query->row();
  }  
  
  /**
   * Get sum of all contest entries for this draw period
   * @param string $sum determind return is just sum or all results
   * @return in sum of all draws
   */
  public function get_active_draws($sum = '')
  {
      $ts = time();
    //check winner table for timestamp of LAST contest
    $query_1 = $this->db->select('create_timestamp')
      ->limit(1)
      ->order_by('id','ACS')
      ->get($this->tables['draw_winner']);
    $last_draw = $query_1->row('create_timestamp');
    
    if(!empty($last_draw)){
       $this->db->where('create_timestamp >=', $last_draw);
    }
    if($sum == 'sum'){
        $query = $this->db->select_sum('draws')
                ->where('create_timestamp <=', $ts)
          ->get($this->tables['draws']);

        return $query->row('draws');
    }else{
        $query = $this->db->select('id, user_id, draws')
                ->where('create_timestamp <=', $ts)
          ->get($this->tables['draws']);
        return $query->result();        
    }
  }  
  
  /**
   * Get draw id by name
   * @param  string  draw name
   * @return int draw id
   */
  private function get_draw_id_by_name($name)
  {

    $query = $this->db->select('b.id')
      ->where('lower(b.name)', $name)
      ->get($this->tables['draw_winner'] . ' b');
    $result = (!empty($query->row()->id))?$query->row()->id:0;
    return $result;
  }    
  
  public function draw_delete($id)
  {
    //delete all winners from history table
    $query = $this->db->select('user_draw_history`.name')
      ->where('id', $id)
      ->get($this->tables['draw_history']);      
    $draw_name = $query->row()->name;  

    $query2 = $this->db->where('name', $draw_name);
    $query2->delete($this->tables['draw_winner']);
    $query3 = $this->db->where('id', $id);
    return $query3->delete($this->tables['draw_history']);
  }
  
  /**
   * Get the number of eligible entries base on parameters.
   * @param  string  $type          the type of draw
   * @param  int     $monthly_month month number for monthly type of draw
   * @param  int     $monthly_year  year number for monthly type of draw
   * @param  boolean $get_sum       determine getting total number or all results
   * @return int                    total number
   */
  public function get_eligible_entries($type = "life", $monthly_month = 1, $monthly_year = 2012, $get_sum = TRUE) {
    switch($type) {
      case "monthly":
        return $this->get_eligible_entries_for_monthly($monthly_month, $monthly_year, $get_sum);
      case "life":
      default :
        return $this->get_eligible_entries_for_life($get_sum);
    }
  }
  
  /**
   * Get eligible entries for life since last time it is picked.
   * @param  boolean $get_sum determine getting total number or all results
   * @return int total number
   */
  public function get_eligible_entries_for_life($get_sum = TRUE) {
    $this->db->select_max("create_timestamp")->from($this->tables["draw_history"])->where("type", "life");
    $query = $this->db->get();

    $super_admins = $this->_get_super_admin_users();
    
    if ($get_sum) {
      $this->db->select_sum("draws");
    } else {
      $this->db->select("id, user_id, draws");
    }    
    $this->db->where(array("create_timestamp >" => (empty($query->row()->create_timestamp) ? 0 : $query->row()->create_timestamp)));
    $this->db->where_not_in('user_id',$super_admins);
    $query = $this->db->get($this->tables["draws"]. ' d');

    if ($get_sum) {
      return $query->row()->draws;
    } else {
      return $query->result();
    }
  }
  
  /**
   * Get tje number of eligible entries for monsthly base on the spesific month of the spesific year.
   * @param  int     $monthly_month spesific month number
   * @param  int     $monthly_year  spesific month number
   * @param  boolean $get_sum       determine getting total number or all results
   * @return int                    total number
   */
  public function get_eligible_entries_for_monthly($monthly_month = 1, $monthly_year = 2012, $get_sum = TRUE) {
    $months = months_list();
    $month = strtotime($months[$monthly_month]." 1, ".$monthly_year);
    $fisrt_date_of_the_month = date("Y-m-01", $month);
    $last_date_of_the_month = date("Y-m-t", $month);
    $month_start = strtotime($fisrt_date_of_the_month." 00:00:00") - 1; // The second before 1st date of the month;
    $month_end = strtotime($last_date_of_the_month." 23:59:59") + 1; // The second after last date of the month;
    
    $super_admins = $this->_get_super_admin_users();
    if ($get_sum) {
      $this->db->select_sum("draws");
    } else {
      $this->db->select("id, user_id, draws");
    }
    $this->db->where(array("create_timestamp >" => $month_start, "create_timestamp <" => $month_end));
    $this->db->where_not_in('user_id',$super_admins);
    $query = $this->db->get($this->tables["draws"]. ' d');
    
    if ($get_sum) {
      return $query->row()->draws;
    } else {
      return $query->result();
    }
  }
  
  /**
   * Check the month is already used by monthly type draws or not.
   * @param  int $month month number
   * @param  int $year  year number
   * @return boolean
   */
  public function monthly_type_check($month = 1, $year = 2012) {
    $query = $this->db->get_where($this->tables["draw_history"], array("monthly_month" => $month, "monthly_year" => $year, "type" => "monthly"));
    return $query->num_rows() > 0;
  }
  
 /**
   * Get the number of eligible entries for custom date range
   * @param  int     $start         
   * @param  int     $end  spesific month number
   * @param  boolean $get_sum       determine getting total number or all results
   * @return int                    total number
   */
  public function get_eligible_entries_for_custom_range($start, $end, $get_sum = TRUE) {
      if(empty($start)) {
        $start = time();          
      }else{
        $start = strtotime($start);
      }
      if(empty($end)) {
        $end = time();
      }else{
        $end = strtotime($end); 
      }
    $super_admins = $this->_get_super_admin_users();
    if ($get_sum) {
      $this->db->select_sum("draws");
    } else {
      $this->db->select("id, user_id, draws");
    }    
    $this->db->where(array("create_timestamp >" => $start, "create_timestamp <" => $end));
    $this->db->where_not_in('user_id',$super_admins);
    $query = $this->db->get($this->tables["draws"]. ' d');

    if ($get_sum) {
      return $query->row()->draws;
    } else {
      return $query->result();
    }
  } 
  
  /**
   * Get details about draw
   * @param  int  draw id
   * @return object with one row
   */
  public function draw_details_load($id)
  {

    $query = $this->db->select('b.*')
      ->where('b.id', $id)
      ->get($this->tables['draw_history'] . ' b');
    return $query->row();
  }   
  
  /**
   * Edit draw details
   * @param  int  draw id
   */
  
  public function draw_details_update($draw_id,$description){
      $time = now();
      $this->db->set( 'description', $description);
      $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
      $this->db->set('update_timestamp', $time);
      $this->db->where('id',$draw_id);
      $this->db->update($this->tables['draw_history']);   
  }  
  
}
?>
