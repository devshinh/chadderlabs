<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Location_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('location/location', TRUE);
    $this->tables = $this->config->item('tables', 'location');
  }

  /**
   * get_all_locations() - get all oragnizations for DB
   *
   * Get all oragnizations for DB - order by name
   * @param  bool  $detailed when set to true, returns detailed information as well as a user count
   *                         when set to false, only returns location number
   *  @return object with all oragnizations
   *
   */
  public function get_all_locations($detailed = FALSE) {
    if ($detailed){
      //$this->db->order_by('name', 'ASC')->get($this->tables['location']);
      $query = $this->db->select('l.*, COUNT( lu.user_id ) AS users')
        ->join($this->tables['location_user'] . ' lu', 'lu.location_id = l.id', 'LEFT OUTER')
        ->group_by('l.id, l.name')
        ->get($this->tables['location'] . ' l');
    }else{
      $query = $this->db->order_by('name', 'ASC')->get($this->tables['location']);
    }
    return $query->result();
  }

  /**
   * get_location_by_id() - get oraganization for DB by location id
   *
   *
   *  @param id location
   *  @return object with one row
   *
   */
  public function get_location_by_id($id) {
    $this->db->select();

    $this->db->where($this->tables['location'] . '.id', $id);
    $query = $this->db->get($this->tables['location']);

    return $query->row();
  }

  public function insert() {

    self::_setElement();

    $this->db->set('create_timestamp', time(), false);
    $this->db->insert($this->tables['location']);
  }

  public function update($id) {

    self::_setElement();
    $this->db->set('update_timestamp', time(), false);
    $this->db->where('id', $id);
    $this->db->update($this->tables['location']);
  }

  public function delete() {
    $this->db->where('nOrganizationID', $this->input->post('hdnIDCurr'));
    $this->db->delete($this->tables['location']);
  }

  public function delete_by_id($id) {
    $this->db->where('id', $id);
    $this->db->delete($this->tables['location']);
  }

  private function _setElement() {
    // assign values
    $this->db->set('site_id', $this->session->userdata('siteID'));
    $this->db->set('author_id', $this->session->userdata('user_id'));
    $this->db->set('name', $this->input->post('name'));
    $this->db->set('slug', strtolower(url_title($this->input->post('name'), '-')));
    //$this->db->set('website', $this->input->post('website'));
    $this->db->set('main_email', $this->input->post('main_email'));
    $this->db->set('main_phone', $this->input->post('main_phone'));
    $this->db->set('toll_free_phone', $this->input->post('toll_free_phone'));
    $this->db->set('main_fax', $this->input->post('main_fax'));
    $this->db->set('address_1', $this->input->post('address_1'));
    $this->db->set('address_2', $this->input->post('address_2'));
    $this->db->set('city', $this->input->post('city'));
    $this->db->set('province', $this->input->post('province'));
    $this->db->set('postal_code', $this->input->post('postal_code'));
    $this->db->set('page_location_title', $this->input->post('page_location_title'));
    $this->db->set('page_location_description', $this->input->post('page_location_description'));
    $this->db->set('page_location_services', $this->input->post('page_location_services'));
    //country
    //headquarterss
    //organization_id
  }

  /**
   * get_users_for_location() - get all user location for DB by location id
   *
   *
   *  @param id location
   *  @return object with one row
   *
   */
  public function get_users_for_location($id) {
    $this->db->select()->from($this->tables['location_user']);
    $this->db->join($this->tables['user'] . ' u', 'u.id = '.$this->tables['location_user'].'.user_id');
    $this->db->join($this->tables['user_profile'] . ' p', 'p.user_id = '.$this->tables['location_user'].'.user_id');
    $this->db->where('location_id', $id);
    $query = $this->db->get();

    return $query->result();
  }
  public function delete_all_users_for_location($loc_id){
    $this->db->where('location_id', $loc_id);
    $this->db->delete($this->tables['location_user']);
  }
  public function delete_user_for_location($usr_id, $loc_id){
    $this->db->where('user_id', $usr_id);
    $this->db->where('location_id', $loc_id);
    $this->db->delete($this->tables['location_user']);
  }
  public function delete_all_hours_for_location($loc_id){
    $this->db->where('connection_id', $loc_id);
    $this->db->delete($this->tables['hours']);
  }
  public function add_user($loc_id,$usr_id){
    //add new users
    $this->db->set('location_id', $loc_id);
    $this->db->set('user_id', $usr_id);
    $this->db->insert($this->tables['location_user']);

    //update timestamp
    $this->db->set('update_timestamp', time(), false);
    $this->db->where('id', $loc_id);
    $this->db->update($this->tables['location']);
  }

  /**
   * get_users_for_location() - get all user location for DB by location id
   *
   *
   *  @param id location
   *  @return object with one row
   *
   */
  public function set_coordinates($loc_id, $lat, $lng) {
    $this->db->set('latitude', $lat);
    $this->db->set('longitude', $lng);
    $this->db->where('id', $loc_id);
    $this->db->update($this->tables['location']);

  }


}

?>
