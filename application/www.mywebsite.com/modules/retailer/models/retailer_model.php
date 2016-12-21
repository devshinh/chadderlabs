<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Retailer_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('retailer/retailer', TRUE);
    $this->tables = $this->config->item('tables', 'retailer');
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
  public function retailer_list($filters = FALSE, $detailed = FALSE, $page_num = 1, $per_page = 0)
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
        $this->db->like('r.name', $filters['keyword']);
      }
      if (array_key_exists('country', $filters) && $filters['country'] > '') {
        if (is_array($filters['country'])) {
          $this->db->where_in('r.country_code', $filters['country']);
        }
        else {
          $this->db->where('r.country_code', $filters['country']);
        }
      }
      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('r.status', $filters['status']);
        }
        else {
          $this->db->where('r.status', $filters['status']);
        }
      }
      $sortable_fields = array('name', 'country', 'status', 'create_timestamp', 'stores', 'users');
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
        $this->db->order_by('name', 'ASC');
      }
    }
    else {
      $this->db->order_by('name', 'ASC');
    }
    if ($detailed) {
      $query = $this->db->select('r.id, r.name, c.country, r.status, r.create_timestamp, rr.role_id')
        ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
        ->join($this->tables['retailer_role'] . ' rr', 'rr.retailer_id = r.id AND rr.site_id = ' . $this->site_id, 'LEFT OUTER')
        ->group_by('r.id, r.name, c.country, r.status, r.create_timestamp, rr.role_id')
        ->get($this->tables['retailer'] . ' r');
    }
    else {
      $query = $this->db->select('r.id, r.name')
        ->get($this->tables['retailer'] . ' r');
    }
    $result = $query->result();
    //var_dump($this->db->last_query());
    return $result;
  }
  
   /**
   * Load all active retailers
   * 
   * @return array of objects
   */ 
  public function retailer_load_active($sorting = 0){
      if(is_array($sorting)){
          $this->db->order_by($sorting['sort_by'],$sorting['sort_direction']);
      }
      
      $query = $this->db->select('r.id, r.name, c.country, r.status, r.create_timestamp, r.logo_image_id, SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) AS active_store_count')
        ->join($this->tables['organization_in_type'] . ' o', 'o.organization_id = r.id')
        ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
        ->join($this->tables['store'] . ' s', 's.retailer_id = r.id')
        ->where('r.status',1)
        ->where('o.type_id',1)
        ->group_by('r.id, r.name, c.country, r.status, r.create_timestamp,r.logo_image_id')
        //->order_by('active_store_count','DESC')
        ->get($this->tables['retailer'] . ' r');
    $result = $query->result();
    return $result;      
  }

  /**
   * Counts all items
   * @param  array  filters, including search keyword, sorting field, and other filter criteria
   * @return int
   */
  public function retailer_count($filters)
  {
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('name', $filters['keyword']);
      }
      if (array_key_exists('country', $filters) && $filters['country'] > '') {
        if (is_array($filters['country'])) {
          $this->db->where_in('country_code', $filters['country']);
        }
        else {
          $this->db->where('country_code', $filters['country']);
        }
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
    return $this->db->count_all_results($this->tables['retailer']);
  }

  /**
   * get retailer from DB
   * @param  int  retailer id
   * @param  bool  $active_only when set to true, returns active retailers only
   * @return object with one row
   */
  public function retailer_load($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('r.*, c.country')
      ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
      ->where('r.id', $id)
      ->get($this->tables['retailer'] . ' r');
    return $query->row();
  }

  /**
   * get retailer from DB by slug
   * @param  string  slug
   * @return object with one row
   */
  public function get_retailer_by_slug($slug)
  {
      
    $query = $this->db->select('r.*, c.country')
      ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
      ->where('r.slug', $slug)
      ->get($this->tables['retailer'] . ' r');
    return $query->row();
  }  
  
  public function retailer_insert($data, $author_id = 0)
  {
    $ts = time();
    if($author_id == 0){
        $author_id = (int) $this->session->userdata('user_id');
    }
    $this->db->set('name', array_key_exists('name', $data) ? $data['name'] : '');
    $this->db->set('country_code', array_key_exists('country_code', $data) ? $data['country_code'] : 'US');
    $this->db->set('status', array_key_exists('status', $data) ? $data['status'] : 0);
    $this->db->set('author_id', $author_id);
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['retailer']);
    if ($inserted) {
      $new_id = $this->db->insert_id();
      return $new_id;
    }
    else {
      return FALSE;
    }
  }
  
  /**
   * Update a record
   * @param  int  unique ID
   * @param  array  data to be updated
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function retailer_update($id, $data)
  {
    if (is_array($data)) {
      if (array_key_exists('name', $data)) {
        $this->db->set('name', $data['name']);
      }
      if (array_key_exists('country_code', $data)) {
        $this->db->set('country_code', $data['country_code']);
      }
      if (array_key_exists('status', $data)) {
        $this->db->set('status', $data['status']);
      }
      else {
        $this->db->set('status', 0);
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['retailer']);
  }

  /**
   * delete a record by id
   * @param  int  $id
   * @return bool
   */
  public function retailer_delete($id)
  {
    // delete all stores for this retailer
    $this->db->where('retailer_id', $id);
    $this->db->delete($this->tables['store']);
    // delete assigned roles
    $this->db->where('retailer_id', $id);
    $this->db->delete($this->tables['retailer_role']);
    // delete the retailer itself
    $this->db->where('id', $id);
    return $this->db->delete($this->tables['retailer']);
  }

  /**
   * list all items from DB
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @param  bool  $detailed when set to true, returns detailed information
   *                         when set to false, only returns ID and Name
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
  public function store_list($filters = FALSE, $detailed = FALSE, $page_num = 1, $per_page = 0)
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
        //$this->db->like('r.store_name', $filters['keyword']);
        $this->db->where('(r.store_num = \'' . $filters['keyword'] . '\' OR r.store_name LIKE \'%' . $filters['keyword'] . '%\')');
      }
      if (array_key_exists('retailer_id', $filters) && $filters['retailer_id'] > '') {
        $this->db->where('r.retailer_id', $filters['retailer_id']);
      }
      if (array_key_exists('status', $filters) && $filters['status'] > '') {
        if (is_array($filters['status'])) {
          $this->db->where_in('r.status', $filters['status']);
        }
        else {
          $this->db->where('r.status', $filters['status']);
        }
      }
      $sortable_fields = array('store_name', 'store_num', 'province_name', 'city', 'status', 'create_timestamp', 'users');
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
        $this->db->order_by('store_name', 'ASC');
      }
    }
    else {
      $this->db->order_by('store_name', 'ASC');
    }
    if ($detailed) {
      $query = $this->db->select('r.id, r.retailer_id, r.store_name, r.store_num, p.province_name, r.city, r.status, r.create_timestamp, COUNT(up.id) AS users')
        ->join($this->tables['province'] . ' p', 'p.province_code = r.province')
        ->join($this->tables['user_profile'] . ' up', 'up.retailer_id = r.id', 'LEFT OUTER')
        ->group_by('r.id, r.retailer_id, r.store_name, r.store_num, p.province_name, r.city, r.status, r.create_timestamp')
        ->get($this->tables['store'] . ' r');
    }
    else {
      $query = $this->db->select('r.id, r.store_name')
        ->get($this->tables['store'] . ' r');
    }
    return $query->result();
  }

  /**
   * Counts all items
   * @param  array  filters, including search keyword, sorting field, and customized filter criteria
   * @return int
   */
  public function store_count($filters = FALSE)
  {
    if (is_array($filters)) {
      if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
        $this->db->like('store_name', $filters['keyword']);
      }
      if (array_key_exists('retailer_id', $filters) && $filters['retailer_id'] > '') {
        $this->db->where('retailer_id', $filters['retailer_id']);
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
    return $this->db->count_all_results($this->tables['store']);
  }

  /**
   * get item from DB
   * @param  int  unique id
   * @param  bool  $active_only when set to true, returns active items only
   * @return object with one row
   */
  public function store_load($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('s.*,p.province_name,c.country')
      ->join($this->tables['province'] . ' p', 'p.province_code = s.province')  
      ->join($this->tables['country'] . ' c', 'c.country_code = s.country_code') 
      ->where('id', $id)
      ->get($this->tables['store'].' s');
    return $query->row();
  }

  /**
   * Insert a new record
   * @param  int  retailer ID
   * @param  array  data
   * @return mixed  unique ID if succeed or FALSE if failed
   */
  public function store_insert($retailer_id, $data)
  {
    $ts = time();
    $this->db->set('retailer_id', $retailer_id);
    $this->db->set('store_name', $data['store_name']);
    $this->db->set('slug', strtolower(url_title($data['store_name'])));
    $this->db->set('store_num', $data['store_num']);
    $this->db->set('street_1', $data['street_1']);
    $this->db->set('street_2', $data['street_2']);
    $this->db->set('city', $data['city']);
    $this->db->set('province', $data['province']);
    $this->db->set('country_code', $data['country_code']);
    $this->db->set('postal_code', $data['postal_code']);
    $this->db->set('phone', $data['phone']);
    if (array_key_exists('status', $data)) {
      $this->db->set('status', $data['status']);
    }
    else {
      $this->db->set('status', 0);
    }
     if (array_key_exists('author_id', $data)) {
      $author_id = $data['author_id'];
     }else{
      $author_id = (int) ($this->session->userdata('user_id'));
     }
    $this->db->set('author_id', $author_id);
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['store']);
    if ($inserted) {
      $new_id = $this->db->insert_id();
      return $new_id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Update a record
   * @param  int  unique ID
   * @param  array  data
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function store_update($id, $data)
  {
    if (is_array($data)) {
      $this->db->set('store_name', $data['store_name']);
      $this->db->set('store_num', $data['store_num']);
      $this->db->set('slug', strtolower(url_title($data['store_name'])));
      $this->db->set('street_1', $data['street_1']);
      $this->db->set('street_2', $data['street_2']);
      $this->db->set('city', $data['city']);
      $this->db->set('province', $data['province']);
      $this->db->set('country_code', $data['country_code']);
      $this->db->set('postal_code', $data['postal_code']);
      $this->db->set('phone', $data['store_num']);
      if (array_key_exists('status', $data)) {
        $this->db->set('status', $data['status']);
      }
      else {
        $this->db->set('status', 0);
      }
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['store']);
  }

  /**
   * delete record by id
   * @param  int  $id
   * @return bool
   */
  public function store_delete($id)
  {
    $this->db->where('id', $id);
    return $this->db->delete($this->tables['store']);
  }

  /**
   * List available retailer permissions
   * @retun array
   */
  public function list_permissions()
  {
    return array(
      'A' => 'Full Access',
      'L' => 'Limited Access',
      'N' => 'No Access',
    );
  }

  /**
   * List available retailer roles
   * @retun array
   */
  public function list_retailer_roles()
  {
    $retailer_roles = array(
      'A' => '0',
      'L' => '0',
      'N' => '0',
    );
    // find the role that has full member access with points under this domain
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 5)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['A'] = $row->id;
    }
    // find the role that has only member access
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 3)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['L'] = $row->id;
    }
    return $retailer_roles;
  }

  /**
   * Update retailer permission
   * @param  int  retailer ID
   * @param  string  access code
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function update_retailer_permission($retailer_id, $access_code)
  {
    $result = FALSE;
    if ($access_code != 'A' && $access_code != 'L' && $access_code != 'N' || (int) $retailer_id <= 0) {
      return $result;
    }
    $this->db->where('retailer_id', $retailer_id)
      ->where('site_id', $this->site_id);
    $result = $this->db->delete($this->tables['retailer_role']);
    if ($access_code == 'N') {
      return $result;
    }
    $retailer_roles = $this->list_retailer_roles();
    $role_id = $retailer_roles[$access_code];
    if ($role_id > 0) {
      $this->db->set('site_id', $this->site_id);
      $this->db->set('retailer_id', $retailer_id);
      $this->db->set('role_id', $role_id);
      $result = $this->db->insert($this->tables['retailer_role']);
    }
    return $result;
  }
  
 /**
   * Count retailer's locations (stores)
   * @param  int  retailer id
   * @return int
   */  
  public function count_stores($rid){
      $query = $this->db->select('COUNT(distinct id) as count')
              ->where('retailer_id =',$rid)
              ->get($this->tables['store']);
      return $query->row();
  }

 /**
   * Counts retailre's users
   * @param  int  retailer id
   * @return int
   */  
  public function count_users($rid){
      $query = $this->db->select('COUNT(distinct id) as count')
              ->where('retailer_id =',$rid)
              ->get($this->tables['user_profile']);
      return $query->row();
  }  
  
  /**
   * get retailer from DB for user
   * @param  int  user_id
   * @return object 
   */
  public function retailer_user_load($user_id)
  {

    $query = $this->db->select('r.*, c.country, p.store_id')
      ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
      ->join($this->tables['user_profile'] . ' p', 'p.retailer_id = r.id')
      ->where('p.user_id', $user_id)
      ->get($this->tables['retailer'] . ' r');
    return $query->row();
  }  
  
  /**
   * Get all  types for organization
   * @return array of types , or NULL/FALSE otherwise
   */
  function organization_type_load_all() {
      return $this->db->get($this->tables['organization_type'])->result();
  }   
  
  /**
   * Get all retailers (organization) for type
   * @return array of retailers , or NULL/FALSE otherwise
   */  
  public function list_retailers_by_category(){

      $query = $this->db->select('r.id, r.name, c.country, r.status, r.create_timestamp, r.logo_image_id, SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) AS active_store_count')
        ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
        //->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
        //->join($this->tables['store'] . ' s', 's.retailer_id = r.id')
        ->where('r.status',1)
        //->where('r.status',1)
        ->group_by('r.id, r.name, c.country, r.status, r.create_timestamp,r.logo_image_id')
        ->get($this->tables['retailer'] . ' r');
    $result = $query->result();
    return $result;      
  }  
  
  /**
   * get retailer's store location for state from DB by slug
   * @param  string  id retailer id
   * @param  string  state name
   * @param  array   sorting variables
   * @return array of retailer's locations , or NULL/FALSE otherwise 
   */
  public function get_stores_state_list($retailer_id,$state_code,$sorting = 0)
  {
      if(is_array($sorting)){
          $this->db->order_by($sorting['sort_by'],$sorting['sort_direction']);
      }      
      $query = $this->db->select('s.*')
        ->join($this->tables['province'] . ' p', 'p.province_code=s.province')
        ->where('s.status',1)
        ->where('s.retailer_id',$retailer_id)
        ->where('s.province', strtoupper($state_code))
        //->group_by('r.id, r.name, c.country, r.status, r.create_timestamp,r.logo_image_id')
        //->order_by('active_store_count','DESC')
        ->get($this->tables['store'] . ' s');     
    return $query->result();
  }    
  
  /**
   * get all cities where retailer has store
   * @param  string  id retailer id
   * @return array of cities , or NULL/FALSE otherwise 
   */
  public function get_retailer_cities($retailer_id)
  {
    $query = $this->db->distinct('rs.city')
      ->where('rs.retailer_id', $retailer_id)         
      ->order_by('rs.city','ASC')
      ->get($this->tables['store'] . ' rs');
    return $query->result();
  }     
  
  /**
   * get state or province info
   * @param  string  state code
   * @return object
   */
  public function get_state_details($state_code){
    $query = $this->db->select('p.*')
      ->where('p.province_code', $state_code)         
      ->get($this->tables['province'] . ' p');
    return $query->row();
  }       
  
  /**
   * List provinces where retailer has a store
   * @param  str  $country_code
   * @retun array of states
   */
  public function list_retailer_provinces($retailer_id)
  {

    //$this->db->where('s.retailer_id', $retailer_id);
    
    $query = $this->db->query('SELECT DISTINCT (s.province) FROM (`retailer_store` s) WHERE `s`.`retailer_id` = '.$retailer_id.' and s.status = 1'); 

    foreach($query->result() as $s){
        $states_array[$s->province] = $s->province; 
    }
    return ($states_array);
  }    
}
?>
