<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Randomizer Model
 * 
 * Author: jeffrey@hottomali.com
 * 
 * Created:  01.25.2012
 * Last updated:  03.13.2012
 * 
 * Description:  This module displays a random image or text.
 * 
 */

class Randomizer_model extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    //$this->load->database();
    $this->load->config('randomizer', TRUE);
    $this->tables = $this->config->item('tables', 'randomizer');
  }
  
  /**
   * Get a random item
   * @param int group ID
   */
  public function get_random_item($group_id) {
    $query = $this->db->select()
      ->where('active', 1)
      ->where('group_id', $group_id)
      ->order_by('id', 'random')
      ->limit(1)
      ->get($this->tables['random_item']);
    return $query->row();
  } 
  
  /**
   * Get a random group
   * @param int group ID
   */
  public function get_random_group($group_id) {
    $query = $this->db->select()
      ->where('active', 1)
      ->where('id', $group_id)
      ->get($this->tables['random_group']);
    return $query->row();
  } 
  
  /**
   * List groups
   * @return array
   */
  public function list_groups() {
    $query = $this->db->select('id, name')
      ->where('active', 1)
      ->where('site_id', $this->session->userdata( 'siteID' ))
      ->order_by('sequence')
      ->get($this->tables['random_group']);
    return $query->result();
  } 
  
}
?>