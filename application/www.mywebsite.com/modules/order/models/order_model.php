<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('order/order', TRUE);
    $this->tables = $this->config->item('tables', 'order');
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
  public function order_list($filters = FALSE, $detailed = FALSE, $page_num = 1, $per_page = 0)
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
        /*
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
      }*/
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
        $this->db->order_by('id', 'ASC');
      }
    }
    else {
      $this->db->order_by('id', 'ASC');
    }
    if ($detailed) {
      $query = $this->db->select('o.*')
        ->join($this->tables['order_status'] . ' s', 's.status_id = o.order_status')
        ->get($this->tables['order'] . ' o');
    }
    else {
      $query = $this->db->select('o.*')
        ->get($this->tables['order'] . ' o');
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
  public function order_count($filters)
  {
    if (is_array($filters)) {
        /*
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
         */
    }
    return $this->db->count_all_results($this->tables['order']);
  }

  /**
   * get order from DB
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active orders only
   * @return object with one row
   */
  public function order_load($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('o.*')
      //->join($this->tables['order_item'] . ' i', 'o.id = i.order_id')
      ->where('o.id', $id)
      ->get($this->tables['order'] . ' o');
    return $query->row();
  }

  /**
   * get order from DB
   * @param  int  order id
   * @param  bool  $active_only when set to true, returns active orders only
   * @return object with one row
   */
  public function order_load_items($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('status', 1);
    }
    $query = $this->db->select('i.*')
      ->where('i.order_id', $id)
      ->get($this->tables['order_item'] . ' i');
    return $query->result();
  }  

  /**
   * Insert a new record
   * @param  array  data
   * @return mixed  unique ID if succeed or FALSE if failed
   */
  public function order_insert($data)
  {
    $ts = time();
    $this->db->set('name', array_key_exists('name', $data) ? $data['name'] : '');
    $this->db->set('country_code', array_key_exists('country_code', $data) ? $data['country_code'] : 'US');
    $this->db->set('status', array_key_exists('status', $data) ? $data['status'] : 0);
    $this->db->set('author_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('create_timestamp', $ts);
    $this->db->set('update_timestamp', $ts);
    $inserted = $this->db->insert($this->tables['order']);
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
  public function order_update($id, $data)
  {
    if (is_array($data)) {

      if (array_key_exists('shipping_firstname', $data)) {
        $this->db->set('shipping_firstname', $data['shipping_firstname']);
      }
      if (array_key_exists('shipping_lastname', $data)) {
        $this->db->set('shipping_lastname', $data['shipping_lastname']);
      }
      if (array_key_exists('shipping_street1', $data)) {
        $this->db->set('shipping_street1', $data['shipping_street1']);
      }
      if (array_key_exists('shipping_street2', $data)) {
        $this->db->set('shipping_street2', $data['shipping_street2']);
      }      
      if (array_key_exists('shipping_city', $data)) {
        $this->db->set('shipping_city', $data['shipping_city']);
      } 
      if (array_key_exists('shipping_province', $data)) {
        $this->db->set('shipping_province', $data['shipping_province']);
      }       
      if (array_key_exists('shipping_postal', $data)) {
        $this->db->set('shipping_postal', $data['shipping_postal']);
      }       
      if (array_key_exists('shipping_phone', $data)) {
        $this->db->set('shipping_phone', $data['shipping_phone']);
      }   
      if (array_key_exists('shipping_email', $data)) {
        $this->db->set('shipping_email', $data['shipping_email']);
      }      
      if (array_key_exists('shipping_instruction', $data)) {
        $this->db->set('shipping_instruction', $data['shipping_instruction']);
      }     
      if (array_key_exists('order_status_options', $data)) {
        $this->db->set('order_status', $data['order_status_options']);
      }          
      

    }
    //$this->db->set('editor_id', (int) ($this->session->userdata('user_id')));
    $this->db->set('update_timestamp', time());
    $this->db->where('id', $id);
    return $this->db->update($this->tables['order']);
  }

  /**
   * delete a record by id
   * @param  int  $id
   * @return bool
   */
  public function order_delete($id)
  {
    // delete all stores for this order
    $this->db->where('order_id', $id);
    $this->db->delete($this->tables['store']);
    // delete assigned roles
    $this->db->where('order_id', $id);
    $this->db->delete($this->tables['order_role']);
    // delete the order itself
    $this->db->where('id', $id);
    return $this->db->delete($this->tables['order']);
  }
}
?>