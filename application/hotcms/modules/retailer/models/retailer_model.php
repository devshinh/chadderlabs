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
      $sortable_fields = array('name', 'country', 'create_timestamp','status','active_store_count','pending_store_count');
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
      //$query = $this->db->select('r.id, r.name, c.country, r.status, r.create_timestamp, COUNT(distinct s.id) as active_store_count')
        $query = $this->db->select('r.id, r.slug, r.name, c.country, r.status, r.create_timestamp, SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END) AS active_store_count, SUM(CASE WHEN s.status = 0 THEN 1 ELSE 0 END) AS pending_store_count')
        ->join($this->tables['country'] . " c", 'c.country_code = r.country_code', 'left outer')
        ->join($this->tables['store'] . " s", 's.retailer_id = r.id', 'left outer')
        //>join($this->tables['user_profile'] . ' up', 'up.store_id = s.id')
        //->join($this->tables['retailer_role'] . ' rr', 'rr.retailer_id = r.id AND rr.site_id = ' . $this->site_id, 'LEFT OUTER')
        //->where('s.status', 1)
        ->group_by('r.id, r.name, c.country, r.status, r.create_timestamp')
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
        ->join($this->tables['country'] . ' c', 'c.country_code = r.country_code')
        ->join($this->tables['store'] . ' s', 's.retailer_id = r.id')
        ->where('r.status',1)
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
  /**
   * Insert a new record
   * @param  array  data
   * @return mixed  unique ID if succeed or FALSE if failed
   */
  public function retailer_insert($data, $author_id = 0)
  {
    $ts = time();
    if($author_id == 0){
        $author_id = (int) $this->session->userdata('user_id');
    } 
    $this->db->set('name', array_key_exists('name', $data) ? $data['name'] : '');
    $this->db->set('slug', array_key_exists('name', $data) ? strtolower(url_title($data['name'],'dash')) : '');
    $this->db->set('website', array_key_exists('website', $data) ? $data['website'] : '');
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
        $this->db->set('slug', strtolower(url_title($data['name'],'dash')));
      }
      if (array_key_exists('country_code', $data)) {
        $this->db->set('country_code', $data['country_code']);
      }
      if (array_key_exists('website', $data)) {
        $this->db->set('website', $data['website']);
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
   * Delete a row in database table by id
   * @param  int $id row id in organization/retailer table
   * @return mix array if retailer has stores, or TRUE if success deleting, or FALSE otherwise
   */
  public function retailer_delete($id) {
    $query = $this->db->get_where($this->tables['store'], array('retailer_id' => $id));
    if ($query->num_rows() > 0) {
      $store_names = array();
      foreach ($query->result() as $store) {
        $store_names[] = $store->store_name;
      }
      return $store_names;
    } elseif ($this->db->where("retailer_id", $id)->delete($this->tables["retailers_categories"])) {
      if ($this->db->where("organization_id", $id)->delete($this->tables["organization_in_type"])) {
        if ($this->db->where("organization_id", $id)->delete($this->tables["target_organization"])) {
          return $this->db->where('id', $id)->delete($this->tables['retailer']);
        }
      }
    }
    return FALSE;
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
        ->join($this->tables['province'] . ' p', 'p.province_code = r.province', "LEFT OUTER")
        ->join($this->tables['user_profile'] . ' up', 'up.retailer_id = r.id', 'LEFT OUTER')
        ->group_by('r.id, r.retailer_id, r.store_name, r.store_num, p.province_name, r.city, r.status, r.create_timestamp')
        ->get($this->tables['store'] . ' r');
    }
    else {
      $query = $this->db->select('r.id, r.store_name')
        ->get($this->tables['store'] . ' r');
    }
    //var_dump($this->db->last_query());
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
    $query = $this->db->select("s.*, p.province_name")
      ->join($this->tables['province'] . " p", 'p.province_code = s.province')
      ->where('id', $id)
      ->get($this->tables['store']." s");
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
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
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
      $this->db->set('retailer_id', $data['retailer']);
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
    }
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['store']);
  }

  /**
   * delete record by id
   * @param  int $id
   * @return mix array if any user still belongs to this store, or TRUE if completed deletion, or FALSE otherwise
   */
  public function store_delete($id)
  {
    $query = $this->db->get_where($this->tables["user_profile"], array("store_id" => $id));
    if ($query->num_rows() > 0) {
      $user_screen_names = array();
      foreach ($query->result() as $user_profile) {
        $user_screen_names[] = $user_profile->screen_name;
      }
      return $user_screen_names;
    } elseif ($this->db->where("store_id", $id)->delete($this->tables["target_store"])) {
      return $this->db->where('id', $id)->delete($this->tables['store']);
    }
    return FALSE;
  }

  /**
   * List available retailer permissions
   * @retun array
   */
  public function list_permissions()
  {
    return array(
      'A' => 'Full Access',
      'FD' => 'Limited Access (Draws + can shop with existing points)',
      'LD' => "Limited Access (Draw + can't shop)",
      'N' => "No Access (Draw + can't shop, no quiz rewards)",
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
      'FD' => '0',
      'LD' => '0',
      'N' => '0',
    );
    // find the role that has full member access with points under this domain
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 3)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['A'] = $row->id;
    }
    // find the role that has Full Draws access
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 5)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['FD'] = $row->id;
    }
    // find the role that has Limited Draws access
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 6)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['LD'] = $row->id;
    }  
    // find the role that has visitor's permissions
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id)
      ->where('system', 4)
      ->get($this->tables['role']);
    $row = $query->row();
    if ($row) {
      $retailer_roles['N'] = $row->id;
    }        
    //var_dump($retailer_roles);
    //die();
    return $retailer_roles;
  }

  /**
   * Update retailer permission
   * @param  int  retailer ID
   * @param  string  access code
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function add_retailer_permission($retailer_id, $permission_key)
  {
    $result = FALSE;
    if ((int) $retailer_id <= 0) {
      return $result;
    }
    //get retailer permission for site id (category retailer_target)
    $query= $this->db->select('p.id, p.permission_key')
            ->where('p.category =', 'retailer_target')
            ->where('p.site_id =', $this->site_id)
            ->get($this->tables['permission'].' p');    

    $permissions = $query->result();    
    
    foreach($permissions as $p){
      $retailer_permissions[$p->id] = $p->permission_key;
    }

    $permission_id = array_search($permission_key, $retailer_permissions); 
    

      $this->db->set('retailer_id', $retailer_id);
      $this->db->set('permission_id', $permission_id);
      $result = $this->db->insert($this->tables['retailer_permission']);
    
    return $result;
  }
  
 /**
   * Update retailer permission
   * @param  int  retailer ID
   * @param  string  access code
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function delete_retailer_permission($retailer_id, $permission_key)
  {
    $result = FALSE;
    if ((int) $retailer_id <= 0) {
      return $result;
    }
    
    //get retailer permission for site id (category retailer_target)
    $query= $this->db->select('p.id, p.permission_key')
            ->where('p.category =', 'retailer_target')
            ->where('p.site_id =', $this->site_id)
            ->get($this->tables['permission'].' p');    

    $permissions = $query->result();    
    
    foreach($permissions as $p){
      $retailer_permissions[$p->id] = $p->permission_key;
    }
    
    $permission_id = array_search($permission_key, $retailer_permissions); 
    
    $this->db->where('retailer_id', $retailer_id);
    $this->db->where('permission_id', $permission_id);
    $result = $this->db->delete($this->tables['retailer_permission']);
    
    return $result;
  }  
  
 /**
   * Count retailer's locations (stores)
   * @param  int  retailer id
   * @return int
   */  
  public function count_stores($rid, $status = 'all'){
      switch ($status) {
          case 'active':
              $this->db->where('status =',1);
              break;
          case 'pending':
              $this->db->where('status =',0);
              break;
          default:
              break;
      }
      $query = $this->db->select('COUNT(distinct id) as count')
              ->where('retailer_id =',$rid)
              ->get($this->tables['store']);
      return $query->row();
  }

 /**
   * Counts retailer's users
   * @param  int    row id
   * @param  string table name
   * @return int
   */  
  public function count_users($row_id, $table = "retailer"){
      $query = $this->db->select('COUNT(distinct id) as count')
              ->where($table."_id =",$row_id)
              ->get($this->tables['user_profile']);
      return $query->row();
  }  
  /**
   *  get_retailer_permissions_by_user_id() - get permission for retailer
   *
   *  @param id retailer
   *  @return array of permissions
   */
  public function get_retailer_permissions_by_user_id($rid) {
      //var_dump($this->site_id);
    $query= $this->db->select('p.permission_key')
            ->join($this->tables['retailer_permission'] . ' rp', 'rp.permission_id=p.id')
            ->where('rp.retailer_id =', $rid)
            ->where('p.site_id =', $this->site_id)
            ->get($this->tables['permission'].' p');
    return $query->result();
  }  
  
  /**
   * Update logo image
   * @param  int  retailer id
   * @param  int  asset id
   * @return object with one row
   */
  public function update_logo_image($retailer_id, $asset_id)
  {

    $query = $this->db->set('logo_image_id',$asset_id)
      ->where('id', $retailer_id)
      ->update($this->tables['retailer']);
    return TRUE;
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
  public function users_list($filters = FALSE, $detailed = FALSE, $page_num = 1, $per_page = 0)
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
        $this->db->like('p.screen_name', $filters['keyword']);
        //$this->db->where('(r.store_num = \'' . $filters['keyword'] . '\' OR r.store_name LIKE \'%' . $filters['keyword'] . '%\')');
      }
      /*
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
         */
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
        $this->db->order_by('p.last_name', 'ASC');
      }
    }
    else {
      $this->db->order_by('p.last_name', 'ASC');
    }
    if ($detailed) {
      if ($this->site_id > 1) {
        $this->db->where('u.site_id', $this->site_id);
      }
        $query = $this->db->select('u.*, p.*')->distinct()
          ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
          //->join($this->tables['user_role'] . ' ur', 'ur.user_id = u.id', 'LEFT OUTER')
          //->join($this->tables['role'] . ' r', 'r.id = ur.role_id')
          ->where("p.".$filters["table"]."_id", $filters["row_id"])
          ->order_by('p.first_name')->limit($per_page, $offset)
          ->get($this->tables['user'] . ' u');
    }else {
      $query = $this->db->select('u.*')
        ->join($this->tables['user_profile'] . ' p', 'p.user_id = u.id')
        ->get($this->tables['user'] . ' u');
    }
    return $query->result();
  }
  
//retailer categories
  /**
   *  get_categories_names_by_reailer_id() - get categories for retailer
   *
   *  @param id retailer
   *  @return array of catgories names
   */
  public function get_categories_names_by_reailer_id($id_retailer) {
    $this->db->select($this->tables['retailer_category'].'.name');
    $this->db->from($this->tables['retailer_category']);
    $this->db->join($this->tables['retailers_categories'], $this->tables['retailer_category'].'.id = '.$this->tables['retailers_categories'].'.category_id');
    $this->db->where($this->tables['retailers_categories'].'.retailer_id', $id_retailer);
    $query = $this->db->get();
    $category_names = array();
    if ($query->num_rows() > 0) {
      foreach ($query->result() as $category) {
        $category_names[$category->name] = $category->name;
      }
    }
    return $category_names;
  }

  /**
   *  Get selected types' names by row id in organization/retailer table.
   *  @param id retailer
   *  @return array of catgories names
   */
  function get_types_names_by_organization_id($organization_id) {
    $this->db->select($this->tables['organization_type'].'.name');
    $this->db->from($this->tables['organization_type']);
    $this->db->join($this->tables['organization_in_type'], $this->tables['organization_in_type'].".type_id = ".$this->tables['organization_type'].'.id');
    $this->db->where($this->tables['organization_in_type'].".organization_id", $organization_id);
    $query = $this->db->get();
    $types_names = array();
    if ($query->num_rows() > 0) {
      foreach ($query->result() as $type) {
        $types_names[$type->name] = $type->name;
      }
    }
    return $types_names;
  }
  
  /**
   * Get organization/retailer types from DB.
   *
   *  @return object with all types
   */
  function get_all_types() {
    return $this->db->order_by('name', 'ASC')->get($this->tables['organization_type'])->result();
  } 
  
  /**
   * get retailer categories from DB
   *
   *  @return object with all categories
   */
  public function get_all_categories()
  {
    //$this->db->where('active', 1);
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['retailer_category']);
    return $query->result();
  }
  
  /**
   * delete retailer_categoty combinations
   * @param  int  $user_id
   * @param  int  $role_id
   * @return bool
   */
  function delete_retailer_categories($retailer_id, $category_id = 0) {
    $this->db->where('retailer_id', $retailer_id);
    if ($category_id > 0) {
      $this->db->where('category_id', $category_id);
    }
    return $this->db->delete($this->tables['retailers_categories']);
  }
  
  public function insert_retailer_category($retailer_id, $category_id) {
    $this->db->set( 'retailer_id', $retailer_id );
    $this->db->set( 'category_id', $category_id );
    $this->db->insert( $this->tables['retailers_categories'] );
  }
  
  /**
   * Delete organization in type relationship in database
   * @param  int  $organization_id row id in organization/retailer table
   * @param  int  $type_id         row id in organization/retailer type table
   * @return bool
   */
  function delete_organization_in_type($organization_id, $type_id = 0) {
    $this->db->where('organization_id', $organization_id);
    if ($type_id > 0) {
      $this->db->where('type_id', $type_id);
    }
    return $this->db->delete($this->tables['organization_in_type']);
  }

  /**
   * Insert a new organization in type relationship
   * @param int $organization_id row id in organization/retailer table
   * @param int $type_id         row id in type table
   */
  function insert_organization_in_type($organization_id, $type_id) {
    $this->db->set( 'organization_id', $organization_id );
    $this->db->set( 'type_id', $type_id );
    $this->db->insert( $this->tables['organization_in_type'] );
  }
  
  /**
   * get retailer's category from DB
   * @param  int  category id
   * @return object with one row
   */
  public function retailer_category_load($id)
  {
    $query = $this->db->select('r.*')
      ->where('r.id', $id)
      ->get($this->tables['retailer_category'] . ' r');
    return $query->row();
  }  
  
  /**
   * Update a record
   * @param  int  unique ID
   * @param  array  data to be updated
   * @return mixed  TRUE if succeeded or FALSE if failed
   */
  public function retailer_category_update($id, $data)
  {
    if (is_array($data)) {
      if (array_key_exists('name', $data)) {
        $this->db->set('name', $data['name']);
      }
    }
    //$this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['retailer_category']);
  }  
  
  /**
   * Insert new category
   * @param  int  unique ID
   * @param  array  data to be updated
   * @return mixed  TRUE if succeeded or FALSE if failed
   */ 
   public function insert_category($data, $author_id = 0) {
    $ts = time();
    if($author_id == 0){
        $author_id = (int) $this->session->userdata('user_id');
    }
    $this->db->set('name', array_key_exists('name', $data) ? $data['name'] : '');
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['retailer_category']);
    if ($inserted) {
      $new_id = $this->db->insert_id();
      return $new_id;
    }
    else {
      return FALSE;
    }
  }  
  
  /**
   * delete a category by id
   * @param  int  $id row id in retailer category table
   * @return mix array if retailer(s) still be categorized with this, or TRUE if success deleting, or FALSE otherwise
   */
  public function retailer_category_delete($id)
  {
    $query = $this->db->get_where($this->tables["retailers_categories"], array("category_id" => $id));
    if ($query->num_rows() > 0) {
      $organization_ids = array();
      foreach ($query->result() as $row) {
        $organization_ids[] = $row->retailer_id;
      }
      return $this->db->where_in("id", $organization_ids)->get($this->tables["retailer"])->result();
    } elseif ($this->db->where('organization_category_id', $id)->delete($this->tables['retailer_category'])) {
      return $this->db->where('id', $id)->delete($this->tables['retailer_category']);
    }
    return FALSE;
  }

  /**
   * Insert new organization/retailer type into database
   * @param  array $data new type
   * @return mix   new row id if success, or FALSE otherwise.
   */
  function insert_type($data) {
    if (isset($data["name"]) && ( !empty($data["name"]))) {
      $now = time();
      $this->db->set('name', $data['name']);
      $this->db->set('create_timestamp', $now);
      $this->db->set('update_timestamp', $now);
      $inserted = $this->db->insert($this->tables['organization_type']);
      if ($inserted) {
        $new_id = $this->db->insert_id();
        return $new_id;
      }
    }
    return FALSE;
  }
  
  /**
   * Get a row from database table by id
   * @param  int $id row id of organization type
   * @return mix found organization object, or NULL/FALSE otherwise
   */
  function organization_type_load($id) {
    return $this->db->get_where($this->tables['organization_type'], array("id" => $id))->row();
  }
  
  /**
   * Update a row in database table by row id
   * @param  int   row id
   * @param  array data to be updated
   * @return mixed TRUE if succeeded or FALSE if failed
   */
  function organization_type_update($id, $data) {
    if (is_array($data)) {
      if (array_key_exists('name', $data)) {
        $this->db->set('name', $data['name']);
      }
    }
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['organization_type']);
  } 
  
  /**
   * Delete a organization type by row id
   * @param  int $id row id in database table
   * @return mix array if organizations(s) still be assosiated with this, or TRUE if success deleting, or FALSE otherwise
   */
  function organization_type_delete($id) {
    $query = $this->db->get_where($this->tables["organization_in_type"], array("type_id" => $id));
    if ($query->num_rows() > 0) {
      $organization_ids = array();
      foreach ($query->result() as $row) {
        $organization_ids[] = $row->organization_id;
      }
      return $this->db->where_in("id", $organization_ids)->get($this->tables["retailer"])->result();
    } elseif ($this->db->where('organization_type_id', $id)->delete($this->tables['target_type'])) {
      return $this->db->where('id', $id)->delete($this->tables['organization_type']);
    }
    return FALSE;
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
   * @return array of citis , or NULL/FALSE otherwise 
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
   * List provinces where retailer has a store
   * @param  str  $country_code
   * @retun array of states
   */
  public function list_retailer_provinces($retailer_id)
  {

    //$this->db->where('s.retailer_id', $retailer_id);
    
    $query = $this->db->query('SELECT DISTINCT (s.province) FROM (`retailer_store` s) WHERE `s`.`retailer_id` = '.$retailer_id.' and s.status = 1'); 
    $states_array = array();
    if ($query->num_rows() > 0) {
      foreach($query->result() as $s){
          $states_array[$s->province] = $s->province; 
      }
    }
    return $states_array;
  }  
  /**
   * get all active stores
   * @param  string  id retailer id
   * @return array of citis , or NULL/FALSE otherwise 
   */
  public function get_active_stores()
  {
    $query = $this->db->select('s.*,r.slug as ret_slug')
      ->join($this->tables['retailer'] . ' r', 's.retailer_id = r.id')
      ->where('s.status', 1)      
      ->get($this->tables['store'] . ' s');
    return $query->result();
  }   
}
?>
