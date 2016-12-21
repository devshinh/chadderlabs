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
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('name', $filters['keyword']);
      }

      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('status', $filters['status']);
        }
        else {
          $this->db->where('status', $filters['status']);
        }
      }
    }
    //$this->db->where('site_id', $this->site_id);
    return $this->db->count_all_results($this->tables['draw_winner']);
  }  
  
  /**
   * Lists all draws from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
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
      //$this->db->limit($per_page, $offset);
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
      $this->db->order_by('sequence', 'ASC');
    }
    $query = $this->db->select('*')
      ->order_by('name', 'DESC')
      ->get($this->tables['draw_winner']);
    return $query->result();
  }
  
  
  public function insert($post,$winner)
  {
    $time = now();
    $this->db->set( 'name', $post['name'] );
    $this->db->set( 'note', $post['note'] );
    $this->db->set( 'user_id', $winner['user_id'] );
    $this->db->set( 'ref_id', $winner['ref_id'] );
    $this->db->set('create_timestamp', $time);
    
    $inserted = $this->db->insert( $this->tables['draw_winner'] );
    if ($inserted) {
      $draw_winner_id = $this->db->insert_id();
      return $draw_winner_id;
    }
    return FALSE;
  }  
  
  public function draw_update($draw_id){
      $this->db->set( 'name', $this->input->post( 'name' ) );
      $this->db->set( 'description', $this->input->post( 'description' ) );
      $this->db->set( 'activity_feed_description', $this->input->post( 'feed_description' ) );
      $this->db->set( 'status', $this->input->post( 'status' ) );
      $this->db->set( 'award_type', $this->input->post( 'award_type' ) );
      $this->db->set( 'award_amount', $this->input->post( 'award_amount' ) );
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
   * 
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
   * Get last draw timestamp
   * @param  string  draw name
   * @return int draw id
   */
  public function get_last_draw_timestamp()
  {
      
    //check winner table for timestamp of LAST contest
    $query_1 = $this->db->select('create_timestamp')
      ->limit(1)
      ->order_by('id','ACS')
      ->get($this->tables['draw_winner']);
    $last_draw = $query_1->row('create_timestamp');
    if(!empty($last_draw)){
      return $last_draw;
    }else{
        return 0;
    }
    
  }    

  /**
   * Store sequence in database (duplicated for each module with sortable
   * @TODO move it to main model, fix return value 
   * @param  string  table
   * @param  int  item id
   * @param  int  item sequence
   */
  
  public function save_draw_sequence($table, $id, $sequence)
  {
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'id', $id );
    $this->db->update( $table );
  }
  
}
?>