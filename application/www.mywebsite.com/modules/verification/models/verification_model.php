<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verification_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('verification/verification', TRUE);
    $this->load->helper('account/account');
    $this->tables = $this->config->item('tables', 'verification');
  }

  /**
   * Counts all verifications
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @return int
   */
  public function verification_count($filters = FALSE)
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
    return $this->db->count_all_results($this->tables['verification']);
  }  
  
  /**
   * Lists all verifications from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function verification_list($filters = FALSE, $page_num = 1, $per_page = 0)
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
        $this->db->order_by('sequence', 'ASC');
      }
    }
    else {
      $this->db->order_by('sequence', 'ASC');
    }
    $query = $this->db->select('*')
      ->order_by('name', 'DESC')
      ->get($this->tables['verification']);
    return $query->result();
  }
  
  public function insert_from_form($user_id,$asset_id)
  {
    //load more info about user
    $user=account_get_user($user_id);
      
    $time = now();
    $this->db->set('user_id', $user_id );
    $this->db->set('asset_id', $asset_id );
    $this->db->set('retailer_id', $user->retailer_id );
    $this->db->set('status', 'pending' );
    $this->db->set('create_timestamp', $time);
    $this->db->set('update_timestamp', $time);    
    
    $inserted = $this->db->insert( $this->tables['verification'] );
    if ($inserted) {
      $verification_id = $this->db->insert_id();
      return $verification_id;
    }
    return FALSE;
  }    
   
  /**
   * Get verifications from DB for user
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active verifications only
   * @return object with one row
   */
  public function verification_load_by_user_id($user_id)
  {
    $query = $this->db->select('*')
      ->where('user_id', $user_id)
      ->order_by('create_timestamp','DESC')
      ->get($this->tables['verification']);
    return $query->result();
  }  
  
  public function verification_update($verification_id){
      $this->db->set( 'name', $this->input->post( 'name' ) );
      $this->db->set( 'description', $this->input->post( 'description' ) );
      $this->db->set( 'activity_feed_description', $this->input->post( 'feed_description' ) );
      $this->db->set( 'status', $this->input->post( 'status' ) );
      $this->db->where('id',$verification_id);
      $this->db->update($this->tables['verification']);
      
  }
  
  /**
   * Get single verification from DB
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active verifications only
   * @return object with one row
   */
  public function verification_load($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('b.*')
      ->where('b.id', $id)
      ->get($this->tables['verification'] . ' b');
    return $query->row();
  }  
  
  /**
   * Get single verification from DB by verification name
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active verifications only
   * @return object with one row
   */
//  public function verification_load_by_name($name)
//  {
//
//    $this->db->where('status', 1);
//    $query = $this->db->select('b.*')
//      ->where('lower(b.name)', $name)
//      ->get($this->tables['verification'] . ' b');
//    return $query->row();
//  }  
   
  
  /**
   * Store sequence in database (duplicated for each module with sortable
   * @TODO move it to main model, fix return value 
   * @param  string  table
   * @param  int  item id
   * @param  int  item sequence
   */
  
  public function save_verification_sequence($table, $id, $sequence)
  {
    $this->db->set( 'sequence', $sequence );
    $this->db->where( 'id', $id );
    $this->db->update( $table );
  }

/**
 * Check user verification
 * 
 * @param int user_id
 * @param string verification name
 * 
 * @return bool true when user has the verification, false when user hasn't
 */
  
  public function check_user_verification($user_id, $verification_name)
  {
    //get verification id
    $verification_id = $this->get_verification_id_by_name($verification_name);      
    //var_dump($verification_id);
    $query = $this->db->select( 'id' )
    ->where( 'ref_table', 'verification' )
    ->where( 'ref_id', $verification_id )
    ->where( 'user_id', $user_id )
    ->get( $this->tables['points'] );
    
    if(!empty($query->row()->id))
    {
        return true;
    }
    
    $query2 = $this->db->select( 'id' )
    ->where( 'ref_table', 'verification' )
    ->where( 'ref_id', $verification_id )
    ->where( 'user_id', $user_id )
    ->get( $this->tables['draws'] );
    if(!empty($query2->row()->id))
    {
        return true;
    }    
    
    return false;
  }  
  
  /**
   * Get verification id by name
   * @param  string  verification name
   * @return int verification id
   */
//  public function get_verification_id_by_name($name)
//  {
//
//    $query = $this->db->select('b.id')
//      ->where('lower(b.name)', $name)
//      ->get($this->tables['verification'] . ' b');
//    $result = (!empty($query->row()->id))?$query->row()->id:0;
//    return $result;
//  }    

    /**
     * unverified user
     * @param  int  $id  user ID
     * @return bool
     */
    public function unverify_user($user_id) {
        //user_profile table
        $this->db->set('verified_date', null);
        $this->db->set('verified', 0);
        $this->db->where('user_id', $user_id);
        $this->db->update($this->tables['profile']);
        
        //find active verification image for user and deactivate
        
        $query = $this->db->select( 'id' )
        ->where( 'status', 'active' )
        ->where( 'user_id', $user_id )
        ->get( $this->tables['verification'] );        
        if($query->num_rows()> 0){
            $verification_id = $query->row()->id;

            $ts= time();
            $this->db->set('status', 'inactive');
            $this->db->set('update_timestamp', $ts);
            $this->db->where('id', $verification_id);

            $this->db->update($this->tables['verification']);        
        }else{
            //load user info to get retailer id
            $user_info = account_get_user($user_id);
            //enter new row for expired
            $ts= time();
            $this->db->set('asset_id', 0);
            $this->db->set('user_id', $user_id);
            $this->db->set('retailer_id', $user_info->retailer_id);
            $this->db->set('status', 'expired');
            $this->db->set('update_timestamp', $ts);
            $this->db->set('create_timestamp', $ts);
            
            $this->db->insert($this->tables['verification']);              
        }
        
    }      
  
  public function verification_delete($id){
    $this->db->where('id', $id);
    $this->db->delete($this->tables['verification']);
  }    
}
?>