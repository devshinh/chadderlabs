<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Organization_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('organization/organization',TRUE);
    $this->tables = $this->config->item('tables','organization');
  }

  /**
   * list all items
   * @param  int  page number
   * @param  int  per page
   * @return objects
   */
  public function get_all_organizations($page_num = 1, $per_page = 100) {
    $per_page = (int)$per_page;
    $page_num = (int)$page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num-1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    $this->db->select();
    $this->db->from($this->tables['organization']);
    $this->db->limit($per_page, $offset);
    return $this->db->get()->result();
  }

  /**
   * count all items for pagination
   * @return int
   */
  public function count_all_organizations() {
    return $this->db->get($this->tables['organization'])->num_rows();
  }

  /**
  * get_organization_by_id() - get organization from DB by id organization
  *
  *
  *  @param id organization
  *  @return object with one row
  *
  */
  public function get_organization_by_id($id) {
    $this->db->select();

    $this->db->where($this->tables['organization'].'.id', $id);
    $query =  $this->db->get($this->tables['organization']);

    return $query->row();
  }

  /**
  * get_id_by_slug() - get id of organization from DB by slug
  *
  *
  *  @param slug
  *  @return int id
  *
  */
  public function get_id_by_slug($slug) {
    $this->db->select('id');

    $this->db->where($this->tables['organization'].'.slug', $slug);
    $query =  $this->db->get($this->tables['organization']);

    return $query->row();
  }

  /**
   * Check to see if a organization slug already exists
   * @param  str   news slug
   * @param  int   exclude news id
   * @return bool
   */
  public function slug_exists($slug, $exclude_id = 0) {
    $query = $this->db->select('id')
      ->where('slug', $slug);
    if ($exclude_id > 0) {
      $this->db->where('id != ', $exclude_id);
    }
    $query = $this->db->get($this->tables['organization']);
    return $query->num_rows();
  }

  public function insert() {

    self::_setElement();

    $this->db->set( 'create_timestamp', time(), false );
    $this->db->insert( $this->tables['organization'] );

  }

  public function update($id) {
    self::_setElement();

    $this->db->set( 'update_timestamp', time(), false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['organization'] );
  }

    public function delete_by_id($id) {

    $this->db->where( 'id', $id );
    $this->db->delete( $this->tables['organization'] );
  }

  private function _setElement() {

    // assign values

    $this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'slug', url_title($this->input->post( 'name' ),'dash',TRUE ));
    $this->db->set( 'phone', $this->input->post( 'phone' ) );
    $this->db->set( 'email', $this->input->post( 'email' ) );
    $this->db->set('author_id', $this->session->userdata('user_id'));

    $this->db->set( 'active', $this->input->post( 'active' ) ? 1 : 0 );

  }

  public function get_locations($org_id){


    $query = $this->db->select('o.*, COUNT( ou.user_id ) AS users')
  ->join($this->tables['location_user'] . ' ou', 'ou.location_id = o.id', 'LEFT OUTER')
  ->group_by('o.id, o.name')
  ->get($this->tables['location'] . ' o');
    return $query->result();
  }

}
?>