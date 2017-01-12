<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_module extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('module/module',TRUE);
    $this->tables = $this->config->item('tables', 'module');
    //TODO: check user permission
  }

  /**
   * List all modules from DB
   * @return object with all modules
   */
  public function get_all_modules() {
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['module']);
    return $query->result();
  }

  /**
   * Get a module from DB by id
   * @param id module ID
   * @return object with one row
   */
  public function get_module_by_id($id) {
    $this->db->select();
    $this->db->where('id', $id);
    $query =  $this->db->get($this->tables['module']);
    return $query->row();
  }

  public function insert($attr) {
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    //$this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    self::_setElement($attr);
    $this->db->insert( $this->tables['module'] );
  }

  public function update($id, $attr) {
    self::_setElement($attr);
    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['module'] );
  }

  public function delete_by_id($id) {
    // delete user data
    $this->db->where( 'id', $id );
    $this->db->delete( $this->tables['module'] );
  }

  private function _setElement($attr) {
    // assign values
    $this->db->set( 'name', $attr['name'] );
    $this->db->set( 'version', $attr['version'] );
    $this->db->set( 'core_level', $attr['core_level'] );
    $this->db->set( 'is_embed', $attr['is_embed'] ? 1 : 0 );
    $this->db->set( 'active', array_key_exists('active', $attr) && $attr['active'] ? 1 : 0 );
  }

  /**
   * List all module widgets
   * @param bool  TRUE: active widgets only; FALSE: all widgets
   * @return array of objects
   */
  public function list_widgets($active_only = TRUE) {
    $this->db->select('m.name AS module_name,w.name AS widget_name,m.module_code,w.id,w.widget_code,m.icon_name AS widget_icon_name', FALSE)
      ->join($this->tables['module'] . ' m', 'm.id=w.module_id')
      ->where('m.site_id', $this->session->userdata( 'siteID' ));
    if ($active_only) {
      $this->db->where('m.active', 1);
    }
    $query = $this->db->order_by('m.sequence, w.sequence', 'ASC')->get($this->tables['module_widget'] . ' w');
    return $query->result();
  }
  
  /**
   * Get a module from DB by id
   * @param id module ID
   * @return object with one row
   */
  public function get_module_by_site_id($site_id) {
    $this->db->select();
    $this->db->where('site_id', $site_id);
    $query =  $this->db->get($this->tables['module']);
    return $query->result();
  }  

}
?>