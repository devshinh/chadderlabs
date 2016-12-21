<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
class Model_module_permission  extends CI_Model {

   public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('role/role', TRUE);
    $this->tables = $this->config->item('tables', 'role');
  }

  public function get_module_permissions() {
    $this->db->select();

    $query =  $this->db->get($this->tables['module_permission']);

    return $query->result();
  }

  public function  get_active_module_permission_by_role_id($role_id) {

    $this->db->select($this->tables['role_module_permission'].'.permission_id');
    $this->db->from($this->tables['role_module_permission']);
    $this->db->where($this->tables['role_module_permission'].'.role_id', $role_id);
    $query =  $this->db->get();

    return $query->result();
  }

  public function delete_module_permission_by_role_id($role_id){

    $this->db->where( 'role_id', $role_id );
    $this->db->delete( $this->tables['role_module_permission'] );

  }

  public function insert_module_permission($role_id, $permission_id){

    $this->db->set( 'role_id', $role_id );
    $this->db->set( 'permission_id', $permission_id );

    $this->db->insert( $this->tables['role_module_permission'] );
  }
}
*/
?>