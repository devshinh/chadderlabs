<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Carousel Model
 *
 * Author: jeffrey@hottomali.com
 *
 * Created:  01.31.2012
 * Last updated:  03.13.2012
 *
 * Description:  This module displays a carousel.
 *
 */

class Carousel_model extends HotCMS_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('carousel', TRUE);
    $this->tables = $this->config->item('tables', 'carousel');
  }

  /**
   * List carousel groups
   * @return array
   */
  public function list_groups() {
    $query = $this->db->select()
      ->where('active', 1)
      ->where('site_id', $this->session->userdata( 'siteID' ))
      ->order_by('name')
      ->get($this->tables['carousel_group']);
    return $query->result();
  }

  /**
   * Get a carousel group
   * @param int group id
   * @return object
   */
  public function get_carousel_group( $group_id ) {
    $query = $this->db->select()
      ->where('active', 1)
      ->where('id', $group_id)
      ->get($this->tables['carousel_group']);
    return $query->row();
  }

  /**
   * List carousel items
   * @param int group id
   * @return array
   */
  public function list_items($group_id) {
    $query = $this->db->select()
      ->where('active', 1)
      ->where('group_id', $group_id)
      ->order_by('sequence')
      ->get($this->tables['carousel']);
    return $query->result();
  }

  /**
   * Get a carousel item by id
   * @param int item id
   * @return array
   */
  public function get_item($id) {
    $query = $this->db->select()
      ->where('id', $id)
      ->get($this->tables['carousel']);
    return $query->row();
  }

  /**
   * Insert a new carousel item
   * @param int group id
   * @param int asset id
   * @return array
   */
  public function insert_item($group_id, $asset_id, $name = '') {
    $group_id = (int)$group_id;
    $asset_id = (int)$asset_id;
    if ($group_id > 0 && $asset_id > 0) {
      $this->db->set( 'group_id', $group_id );
      $this->db->set( 'asset_id', $asset_id );
      if ($name > '') {
        $this->db->set( 'link_title', $name );
      }
      $this->db->set( 'active', 1 );
      $this->db->set( 'active_date', 'CURRENT_TIMESTAMP', false );
      $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
      $inserted = $this->db->insert( $this->tables['carousel'] );
      if ($inserted) {
        $id = $this->db->insert_id();
        return $id;
      }
    }
    return FALSE;
  }

  /**
   * Update a carousel item
   * @param int item id
   * @param string link path
   * @param string link title
   * @return bool
   */
  public function update_item($id, $link, $title, $sequence) {
    $id = (int)$id;
    if ($id > 0) {
      $this->db->set( 'link', $link );
      $this->db->set( 'link_title', $title );
      $this->db->set( 'sequence', $sequence );
      $this->db->set( 'update_date', 'CURRENT_TIMESTAMP', false );
      $this->db->where( 'id', $id );
      $this->db->update( $this->tables['carousel'] );
      return $this->db->affected_rows();
    }
    return FALSE;
  }

  /**
   * Delete a carousel item
   * @param int item id
   * @return bool
   */
  public function delete_item($id) {
    $id = (int)$id;
    if ($id > 0) {
      $this->db->where( 'id', $id );
      $this->db->delete( $this->tables['carousel'] );
      return $this->db->affected_rows();
    }
    return FALSE;
  }

}
?>