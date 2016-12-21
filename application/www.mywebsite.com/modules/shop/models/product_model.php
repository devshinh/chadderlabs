<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Product Model
*
* Author: jeffrey@hottomali.com
*
* Created:  04.04.2011
* Last updated:  04.08.2010
*
* Description:  Product model.
*
*/

class Product_model extends HotCMS_Model {

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->config('shop', TRUE);
    $this->tables = $this->config->item('tables', 'shop');
  }

 /**
   * Lists all products from DB
   * @param  int   category id
   * @param  bool  activated items only
   * @param  int  page number
   * @param  int  per page
   * @return array of objects
   */
	public function list_all($category_id = 0, $active_only = TRUE, $page_num = 1, $per_page = 100)
  {
    $per_page = (int)$per_page;
    $page_num = (int)$page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num-1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($category_id > 0) {
      $this->db->where('p.category_id', (int)$category_id);
    }
    if ($active_only) {
      $this->db->where('p.active', 1);
    }
    if ($per_page > 0 && $offset > 0) {
      $this->db->limit($per_page, $offset);
    }
    $query = $this->db->select('p.*, c.name AS category_name')
      ->join($this->tables['category'] . ' c', 'c.id=p.category_id')
      ->where('p.site_id', $this->site_id)
      ->order_by('p.sequence', 'ASC')
      ->get($this->tables['product'] . ' p');
    $results = $query->result();
		/*
    foreach ($results as &$result) {
			if ($result->option_values) {
				$result->option_values = explode(',', $result->option_values);
			}
		}
    */
		return $results;
	}

 /**
   * Retrieves a product from DB
   * @param  int   product id
   * @return mixed
   */
	public function get($id, $active_only = TRUE)
  {
    if ($active_only) {
      $this->db->where('p.active', 1);
    }
		$result = $this->db->get_where($this->tables['product'], array('product_id' => $id))->row();
		return $result;
	}

}
