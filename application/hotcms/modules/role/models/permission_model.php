<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
class Permission_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('role/role', TRUE);
    $this->tables = $this->config->item('tables', 'role');
  }

  public function list_permissions() {
    $query = $this->db->select()
      ->where('site_id', $this->site_id)
      ->get($this->tables['permission']);
    return $query->result();
  }

  public function list_permissions_by_role_id($role_id) {
    $query = $this->db->select('p.*')
      ->join($this->tables['permission_map'] . ' pm', 'pm.permission_id=p.id')
      ->where('p.site_id', $this->site_id)
      ->where('pm.role_id', $role_id)
      ->get($this->tables['permission'] . ' p');
    return $query->result();
  }

  public function delete_role_permission_by_role_id($role_id) {
    $this->db->where( 'role_id', $role_id );
    $this->db->delete( $this->tables['permission_map'] );
  }

  public function insert_role_permission($role_id, $permission_id){
    $this->db->set( 'role_id', $role_id );
    $this->db->set( 'permission_id', $permission_id );
    $this->db->insert( $this->tables['permission_map'] );
  }
}*/
?>