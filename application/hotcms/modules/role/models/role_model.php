<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role_model extends HotCMS_Model {

  private $key_prefix;

  // TODO: list site name along with roles, roles are always associated with a site ID
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('role/role', TRUE);
    $this->tables = $this->config->item('tables', 'role');
    $this->key_prefix = 'manage_user_';
  }

  /**
   * get all roles from DB for each site
   * @param  bool  $detailed when set to true, returns detailed information as well as a user count
   *                         when set to false, only returns ID and Name
   * @param int site_id
   * TODO: list site name along with roles
   * @return object with all roles
   */
  public function get_all_roles($detailed = FALSE, $site_id = 0)
  {
    $this->db->where('r.site_id', $site_id);
    if ($detailed) {
      $query = $this->db->select('r.id, r.name, r.description, r.active, r.system, r.create_date, COUNT( ur.user_id ) AS users')
        ->join($this->tables['user_role'] . ' ur', 'ur.role_id = r.id', 'LEFT OUTER')
        ->group_by('r.id, r.name, r.description, r.active, r.system, r.create_date')
        ->order_by('sequence', 'ASC')->get($this->tables['role'] . ' r');
    }
    else {
      $query = $this->db->select('r.id, r.name')
        ->order_by('r.sequence', 'ASC')->get($this->tables['role'] . ' r');
    }
    return $query->result();
  }

  /**
   * get ACTIVE roles from DB
   *
   *  @return object with all roles
   */
  public function get_all_active_roles()
  {
    $this->db->where('active', 1);
    $this->db->where('site_id', $this->site_id);
    //exclude superadmin and guest
    $this->db->where('system !=', 1);
    $this->db->where('system !=', 4);
    $query = $this->db->order_by('sequence', 'ASC')->get($this->tables['role']);
    return $query->result();
  }

  /**
   * get role from DB by id
   *  @param  int  role id
   *  @param  int  site_id
   *  @return object with one row
   */
  public function get_role_by_id($id, $site_id)
  {
    $query = $this->db->select()
      ->where('id', $id)
      ->where('site_id', $site_id)
      ->get($this->tables['role']);
    return $query->row();
  }

  /**
  *  get_role_id_by_user_id() - get role for user
  *
  *
  *  @param id user
  *  @return id role
  *
  *
  public function  get_role_id_by_user_id($id_user) {
    $this->db->select();

    $this->db->where('user_id', $id_user);
    $query =  $this->db->get($this->tables['user_role']);

    return $query->row()->role_id;
  } */

  /**
   *  get_role_names_by_user_id() - get role for user
   *
   *  @param id user
   *  @return array of role names
   */
  public function get_role_names_by_user_id($id_user) {
    $this->db->select($this->tables['role'].'.name');
    $this->db->from($this->tables['user_role']);
    $this->db->join($this->tables['role'], $this->tables['user_role'].'.role_id = '.$this->tables['role'].'.id');
    $this->db->where($this->tables['user_role'].'.user_id', $id_user);
    $query = $this->db->get();

    return $query->result();
  }

  public function insert()
  {
    self::_setElement();
    $this->db->set( 'site_id', $this->site_id );
    $inserted = $this->db->insert( $this->tables['role'] );
    if ($inserted) {
      $role_id = $this->db->insert_id();
      // add a new permission accordingly
      self::_add_permission($this->input->post('name'));
      return $role_id;
    }
    return FALSE;
  }


  public function insert_user_role($user_id, $role_id) {
    $this->db->set( 'user_id', $user_id );
    $this->db->set( 'role_id', $role_id );
    $this->db->insert( $this->tables['user_role'] );
  }

  public function update($id) {
    self::_setElement();
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    return $this->db->update( $this->tables['role'] );
  }

  /**
   * delete role by id
   * @param type $id
   * @return type
   */
  public function delete_by_id($id) {
    // delete assigned user-role map
    $this->db->where('role_id', $id);
    $this->db->delete($this->tables['user_role']);
    // delete assigned permissions
    $this->db->where('role_id', $id);
    $this->db->delete($this->tables['permission_map']);
    // delete related permissions
    $role = self::get_role_by_id($id);
    $role_name = strtolower($role->name);
    $permission_key = $this->key_prefix . str_replace(' ', '_', $role_name);
    $query = $this->db->select('id')
      ->where('site_id', $this->site_id )
      ->where('category', 'user')
      ->where('permission_key', $permission_key)
      ->get($this->tables['permission']);
    $perm = $query->row();
    $permission_id = $perm->id;
    // delete related user permissions who can manage this role
    $this->db->where('permission_id', $permission_id);
    $this->db->delete($this->tables['permission_map']);
    // delete related user permissions accordingly
    $this->db->where('id', $permission_id);
    $this->db->delete($this->tables['permission']);
    // finally delete the role itself
    $this->db->where('id', $id);
    return $this->db->delete($this->tables['role']);
  }

  /**
   * delete user-role combinations
   * @param  int  $user_id
   * @param  int  $role_id
   * @return bool
   */
  public function delete_user_roles($user_id, $role_id = 0){
    $this->db->where('user_id', $user_id);
    if ($role_id > 0) {
      $this->db->where('role_id', $role_id);
    }
    return $this->db->delete($this->tables['user_role']);
  }

  private function _setElement() {
    // assign values
    $this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'description', $this->input->post( 'description' ) );
    $this->db->set( 'active', $this->input->post( 'active' ) ? 1 : 0 );
  }

  /**
   * add a new user permission key
   * @param  str  role name
   * @return bool
   */
  private function _add_permission($role_name) {
    $role_name = strtolower(trim($role_name));
    if ($role_name == '') {
      return FALSE;
    }
    $permission_key = $this->key_prefix . str_replace(' ', '_', $role_name);
    $this->db->set('site_id', $this->site_id );
    $this->db->set('category', 'user');
    $this->db->set('permission_key', $permission_key);
    $this->db->set('description', 'Manage ' . $role_name);
    $this->db->insert($this->tables['permission']);
  }

  /**
   * update permission key
   * @param  str  old role name
   * @param  str  new role name
   * @return bool
   */
  public function update_permission_key($old_name, $new_name) {
    if ($old_name == '' || $new_name == '') {
      return FALSE;
    }
    $old_key = $this->key_prefix . str_replace(' ', '_', $old_name);
    $new_key = $this->key_prefix . str_replace(' ', '_', $new_name);
    $this->db->set('permission_key', $new_key);
    $this->db->set('description', 'Manage ' . $new_name);
    $this->db->where('site_id', $this->site_id);
    $this->db->where('permission_key', $old_key);
    return $this->db->update($this->tables['permission']);
  }

  /**
   * delete permission by role name
   * @param  str  role name
   * @return bool
   */
  public function delete_permission($role_name) {
    $role_name = strtolower(trim($role_name));
    if ($role_name == '') {
      return FALSE;
    }
    $permission_key = $this->key_prefix . str_replace(' ', '_', $role_name);
    $sql = 'DELETE FROM permission_map WHERE permission_id IN (SELECT id FROM permission WHERE permission_key=? AND site_id=?)';
    $this->db->query($sql, array($permission_key, $this->site_id));
    $this->db->where('site_id', $this->site_id);
    $this->db->where('permission_key', $permission_key);
    return $this->db->delete($this->tables['permission']);
  }

}
?>