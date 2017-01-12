<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_category_model extends HotCMS_Model {

   public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('product/product',TRUE);
    $this->tables = $this->config->item('tables','product');

  }


  /**
  * get_all_products() - get all products from DB
  *
  *
  *  @return object with all product
  *
  */
  public function get_all_product_categories() {
    $this->db->select();
    $this->db->from($this->tables['product_category']);
    $this->db->order_by($this->tables['product_category'].'.sequence');
    $query =  $this->db->get();

    return $query->result();
  }

  /**
  * get_product_by_id() - get product from DB by id user
  *
  *
  *  @param id product
  *  @return object with one row
  *
  */
  public function get_product_by_id($id) {
    $this->db->select();

    $this->db->where($this->tables['product'].'.id', $id);
    $query =  $this->db->get($this->tables['product']);

    return $query->row();
  }

  public function insert() {

    self::_setElement();

    $this->db->set( 'create_date', 'CURRENT_TIMESTAMP', false );
    $this->db->insert( $this->tables['product'] );

  }

  public function update($id) {
    self::_setElement();

    $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
    $this->db->where( 'id', $id );
    $this->db->update( $this->tables['product'] );
  }

  public function delete_by_id($id) {

    // delete user data
    $this->db->where( 'id', $id );
    $this->db->delete( $this->tables['product'] );
  }

  private function _setElement() {
    // assign values
    $this->db->set( 'site_id', $this->session->userdata( 'siteID' ) );

    $this->db->set( 'name', $this->input->post( 'name' ) );
    $this->db->set( 'slug', url_title($this->input->post( 'name' ),'dash',TRUE ));
    $this->db->set( 'description', $this->input->post( 'description' ) );

    $this->db->set( 'active', $this->input->post( 'active' ) ? 1 : 0 );
  }
}
?>