<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Wireless Coverage Map Model
*
* Author:  jeffrey@hottomali.com
*
* Created:  08.04.2010
* Last updated:  08.04.2010
*
* Description:  Wireless coverage map.
*
*/

class Model_coverage extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('coverage', TRUE);
    $this->tables = $this->config->item('tables', 'coverage');
  }
  
  /**
  * get provinces
  */
  public function get_provinces(){
    $query = $this->db->select('sProvince')
      ->distinct()
      ->orderby('sProvince')
      ->get($this->tables['stores']);

    return $query->result();
  }

}
?>