<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_contact extends HotCMS_Model {

   public static function render($module,$view)
   {
      $module->load->view($view);
   }

   public function __construct() {

    parent::__construct();
    $this->load->database();
    $this->load->config('contact/contact', TRUE);
    $this->tables = $this->config->item('tables', 'contact');
    $this->columns = $this->config->item('columns', 'contact');

  }

  /**
  * get_all_contacts() - get all contacts from DB
  *
  * Get all contact from DB - ordrer by name
  *
  *  @return object with all contacts
  *
  */
  public function get_all_contacts()
  {
    $query = $this->db->order_by('name', 'ASC')->get($this->tables['contact']);
    return $query->result();
  }


  /**
  * get_contact_by_id() - get contact from DB by contact id
  *
  *
  *  @param id contact
  *  @return object with one row
  *
  */
  public function get_contact_by_id($id) {
    $this->db->select();

    $this->db->where('id', $id);
    $query =  $this->db->get($this->tables['contact']);

    return $query->row();
  }

  /**
  * get_contact_by_connection() - get all contacts from DB for another model item
  *
  *  @param con_id - table name for connection
  *  @param con_name - id of item for connection
  *  @return object with all contact for connection
  *
  */
  public function get_contact_by_connection($con_name, $con_id) {
    $this->db->select();
    $this->db->where('connection_name', $con_name);
    $this->db->where('connection_id', $con_id);
    $query =  $this->db->get($this->tables['contact']);

    return $query->result();
  }

  /**
  * insert() - insert contact in DB
  *
  *
  *  @param con_id - table name for connection
  *  @param con_name - id of item for connection
  *  @param name - name of contact
  *
  */
  public function insert($con_id, $con_name, $name) {

    //self::_setElement();

    self::_setElementEmpty();
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    $this->db->set( 'connection_id', $con_id );
    $this->db->set( 'connection_name', $con_name );
    $this->db->set( 'name', $name );

    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $this->db->insert( $this->tables['contact'] );

  }

  public function update($id) {
    self::_setElement($id);

    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['contact'] );
  }

  public function update_single($id, $additional_data) {
    if (!empty($this->columns))
		{
			foreach ($this->columns as $input)
			{
				if (is_array($additional_data) && isset($additional_data[$input]))
				{
					$data[$input] = $additional_data[$input];
				}
				//else
				//{
				//	$data[$input] = $this->input->post($input);
				//}
			}
		}
    $this->db->where( 'id', $id );
		$this->db->update($this->tables['contact'], $data);

   /*
    //die(var_dump($data));
    $this->db->set( 'phone', isset($data['phone'])?$data['phone']:'' );
    $this->db->set( 'fax', isset($data['fax'])?$data['fax']:'' );
    $this->db->set( 'ext', isset($data['ext'])?$data['ext']:'' );
    $this->db->set( 'cell', isset($data['cell'])?$data['cell']:'' );
    $this->db->set( 'email', isset($data['email'])?$data['email']:'' );
    $this->db->set( 'twitter', isset($data['twitter'])?$data['twitter']:'' );
    $this->db->set( 'website', isset($data['website'])?$data['website']:'' );
    $this->db->set( 'address_1', isset($data['address_1'])?$data['address_1']:'' );
    $this->db->set( 'address_2', isset($data['address_2'])?$data['address_2']:'' );
    $this->db->set( 'city', isset($data['city'])?$data['city']:'' );
    $this->db->set( 'province', isset($data['province'])?$data['province']:'' );
    $this->db->set( 'postal_code', isset($data['postal_code'])?$data['postal_code']:'' );

    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['contact'] );
    */
  }

  public function delete_by_id($id) {
    $this->db->where( 'id', $id );
    $this->db->delete( $this->tables['contact'] );
  }

  public function delete_by_user_id($user_id) {
    $this->db->where( 'connection_id', $user_id );
    $this->db->where( 'connection_name', 'user' );
    $this->db->delete( $this->tables['contact'] );
  }

  private function _setElement($id) {
    // assign values
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );
    //$this->db->set( 'author_id', $this->session->userdata( 'user_id' ) );
    //$this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'phone', $this->input->post( 'phone_'.$id ) );
    //$this->db->set( 'toll_free_phone', $this->input->post( 'toll_free_phone' ) );
    $this->db->set( 'fax', $this->input->post( 'fax_'.$id ) );
    $this->db->set( 'ext', $this->input->post( 'ext_'.$id ) );
    $this->db->set( 'cell', $this->input->post( 'cell_'.$id ) );
    $this->db->set( 'email', $this->input->post( 'email_'.$id ) );
    $this->db->set( 'twitter', $this->input->post( 'twitter_'.$id ) );
    $this->db->set( 'website', $this->input->post( 'website_'.$id ) );
    $this->db->set( 'address_1', $this->input->post( 'address_1_'.$id ) );
    $this->db->set( 'address_2', $this->input->post( 'address_2_'.$id ) );
    $this->db->set( 'city', $this->input->post( 'city_'.$id ) );
    $this->db->set( 'province', $this->input->post( 'province_'.$id) );
    $this->db->set( 'postal_code', $this->input->post( 'postal_code_'.$id ) );
  }
  private function _setElementEmpty() {
    $this->db->set( 'email', $this->input->post( 'email' ) );
  }
}
?>