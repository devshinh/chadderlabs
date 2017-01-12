<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_menu_group extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('menu/menu', TRUE);
    $this->tables = $this->config->item('tables','menu');
  }

  /**
   * get_all_groups() - get all menu groups from DB
   * @return object with all roles
   */
  public function get_all_groups()
  {
    $this->db->order_by('menu_name', 'ASC');
    $query = $this->db->get_where($this->tables['menu_group'], array('site_id' => $this->site_id));
    return $query->result();
  }

  /**
   * get_group_by_id() - get group by id
   * @return object
   */
  public function get_group_by_id($id)
  {
	  $query = $this->db->get_where($this->tables['menu_group'], array('id' => $id));
    return $query->row();
  }

  public function insert()
  {
    $this->db->set( 'site_id', $this->site_id );
    $this->db->set( 'menu_name', $this->input->post( 'menu_name' ) );
    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    return $this->db->insert( $this->tables['menu_group'] );
  }

  public function update($id)
  {
    $this->db->set( 'menu_name', $this->input->post( 'menu_name' ) );
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    return $this->db->update( $this->tables['menu_group'] );
  }

  public function delete_by_id($id)
  {
    $this->db->where( 'id', $id );
    return $this->db->delete( $this->tables['menu_group'] );
  }

  public function update_name($id, $menu_name)
  {
    $this->db->set( 'menu_name', $menu_name );
	  self::_update($id);
  }

  private function _update($id)
  {
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['menu_group'] );
  }
}
?>