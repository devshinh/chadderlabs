<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Operation_hours_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('operation_hours/operation_hours', TRUE);
    $this->tables = $this->config->item('tables', 'operation_hours');
  }

  public function add_empty_hours_for_location($org_id) {
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Monday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Tuesday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Wednesday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Thursday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Friday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Saturday' );
    $this->insert();
    $this->db->set( 'connection_id', $org_id );
    $this->db->set( 'connection_name', 'location' );
    $this->db->set('create_timestamp', time(), false);
    $this->db->set( 'day', 'Sunday' );
    $this->insert();
  }

  /**
  * get_hours_by_connection() - get all hours from DB for another model item
  *
  *  @param con_id - table name for connection
  *  @param con_name - id of item for connection
  *  @return object with all records for item
  *
  */
  public function get_hours_by_connection($con_name, $con_id) {
    $this->db->select();

    $this->db->where('connection_name', $con_name);
    $this->db->where('connection_id', $con_id);
    $this->db->order_by('id','ASC');
    $query =  $this->db->get($this->tables['hours']);
    return $query->result();
  }

  public function insert() {

    $this->db->set('create_timestamp', time(), false);
    $this->db->insert( $this->tables['hours'] );

  }
  public function update($id) {
    self::_setElement($id);

    $this->db->set('update_timestamp', time(), false);
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['hours'] );

  }
  public function update_attribute($name,$id,$value) {

    $this->db->set($name, $value);
    $this->db->set('update_timestamp', time(), false);
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['hours'] );
  }

  private function _setElement($id) {
    // assign values
    $this->db->set( 'from1', $this->input->post( 'from1_'.$id ) );
    $this->db->set( 'from2', $this->input->post( 'from2_'.$id ) );
    $this->db->set( 'to1', $this->input->post( 'to1_'.$id ) );
    $this->db->set( 'to2', $this->input->post( 'to2_'.$id ) );
    $this->db->set( 'closed', $this->input->post( 'closed_'.$id ) );
  }

}

?>