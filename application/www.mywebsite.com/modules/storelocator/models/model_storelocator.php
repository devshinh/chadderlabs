<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Store Locator Model
*
* Author:  jeffrey@hottomali.com
*
* Created:  07.22.2010
* Last updated:  08.04.2010
*
* Description:  Geographical store locator using Google map APIs.
*
*/

class Model_storelocator extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('storelocator', TRUE);
    $this->tables = $this->config->item('tables', 'storelocator');
  }
  
  public function get_stores(){
    $query = $this->db->select()
      ->get($this->tables['stores']);

    return $query->result();
  }

  public function get_provinces(){
    $query = $this->db->select('sProvince')
      ->distinct()
      ->orderby('sProvince')
      ->get($this->tables['stores']);

    return $query->result();
  }

  public function get_cities(){
    $query = $this->db->select('sProvince')
      ->select('sCity')
      ->distinct()
      ->orderby('sProvince, sCity')
      ->get($this->tables['stores']);

    return $query->result();
  }

}
?>