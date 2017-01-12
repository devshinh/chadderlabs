<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  FAQ Model
 * 
 * Author: jan@hottomali.com
 * 
 * Created:  08.26.2010
 * Last updated:  11.29.2011
 * 
 * Description:  FAQ module.
 * 
 */

class Model_faq extends HotCMS_Model {
  
  public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('faq', TRUE);
    $this->tables = $this->config->item('tables', 'faq');
  }
  
  /**
   * get a list of FAQs
   */
  public function list_faqs() {
    $this->db->select();
    $this->db->where('active', 1);
    $this->db->order_by('sequence');
    $query = $this->db->get($this->tables['faq']);
    return $query->result();
  } 
  
  /**
   * get a list of FAQ groups
   */
  public function list_faq_groups() {
    $this->db->select();
    $this->db->where('active', 1);
    $this->db->order_by('sequence');
    $query = $this->db->get($this->tables['faq_group']);
    return $query->result();
  } 
  
}
?>