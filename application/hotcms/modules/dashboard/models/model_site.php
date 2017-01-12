<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Load data form site_table
 *
 */
class Model_site extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_all_sites()
  {
    $query = $this->db->get_where('site', array('active' => '1'));
    return $query->result();
  }
}
?>